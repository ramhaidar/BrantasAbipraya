<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EvaluasiRKBUrgentController extends Controller
{
    public function index ( Request $request )
    {
        $allowedPerPage = [ 10, 25, 50, 100 ];
        $perPage        = in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;

        $user = Auth::user ();

        // Filter projects based on user role
        $proyeksQuery = Proyek::with ( "users" );
        if ( $user->role === 'koordinator_proyek' )
        {
            $proyeksQuery->whereHas ( 'users', function ($query) use ($user)
            {
                $query->where ( 'users.id', $user->id );
            } );
        }

        $proyeks = $proyeksQuery
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        $query = RKB::query ()
            ->with ( [ 'proyek' ] )
            ->where ( 'tipe', 'urgent' );

        // Add project filtering for koordinator_proyek
        if ( $user->role === 'koordinator_proyek' )
        {
            $proyekIds = $proyeks->pluck ( 'id' )->toArray ();
            $query->whereIn ( 'id_proyek', $proyekIds );
        }
        elseif ( $user->role === 'Pegawai' )
        {
            $query->whereHas ( 'proyek', function ($q) use ($user)
            {
                $q->whereHas ( 'users', function ($q) use ($user)
                {
                    $q->where ( 'users.id', $user->id );
                } );
            } );
        }
        elseif ( $user->role === 'Boss' )
        {
            $proyeks       = $user->proyek ()->with ( "users" )->get ();
            $usersInProyek = $proyeks->pluck ( 'users.*.id' )->flatten ();
            $query->whereHas ( 'proyek', function ($q) use ($usersInProyek)
            {
                $q->whereHas ( 'users', function ($q) use ($usersInProyek)
                {
                    $q->whereIn ( 'users.id', $usersInProyek );
                } );
            } );
        }

        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $q->where ( 'nomor', 'ilike', "%{$search}%" )
                    ->orWhereHas ( 'proyek', function ($query) use ($search)
                    {
                        $query->where ( 'nama', 'ilike', "%{$search}%" );
                    } )
                    ->orWhere ( function ($q) use ($search)
                    {
                        // Handle year search (4 digits)
                        if ( preg_match ( '/^[0-9]{4}$/', $search ) )
                        {
                            $q->whereYear ( 'periode', $search );
                        }
                        // Handle month name in Indonesian or English
                        elseif ( $this->isMonthName ( $search ) )
                        {
                            $monthNumber = $this->getMonthNumber ( $search );
                            if ( $monthNumber )
                            {
                                $q->whereMonth ( 'periode', $monthNumber );
                            }
                        }
                        // Handle "Month Year" format (e.g., "January 2023" or "Januari 2023")
                        elseif ( preg_match ( '/^([A-Za-z]+)\s+([0-9]{4})$/', $search, $matches ) )
                        {
                            $monthNumber = $this->getMonthNumber ( $matches[ 1 ] );
                            if ( $monthNumber )
                            {
                                $q->whereMonth ( 'periode', $monthNumber )
                                    ->whereYear ( 'periode', $matches[ 2 ] );
                            }
                        }
                    } )
                    ->orWhere ( function ($q) use ($search)
                    {
                        // Handle status search
                        $statusKeywords = [ 'pengajuan', 'evaluasi', 'disetujui', 'menunggu approval vp', 'menunggu approval svp', 'tidak diketahui' ];
                        if ( in_array ( strtolower ( $search ), $statusKeywords ) )
                        {
                            $this->getStatusQuery ( $q, $search );
                        }
                    } );
            } );
        }

        // Apply filters
        if ( $request->filled ( 'selected_nomor' ) )
        {
            $this->handleNomorFilter ( $request, $query );
        }
        if ( $request->filled ( 'selected_proyek' ) )
        {
            $this->handleProyekFilter ( $request, $query );
        }
        if ( $request->filled ( 'selected_periode' ) )
        {
            $this->handlePeriodeFilter ( $request, $query );
        }
        if ( $request->filled ( 'selected_status' ) )
        {
            $this->handleStatusFilter ( $request, $query );
        }

        $TableData = $query
            ->orderBy ( 'periode', 'desc' )
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();

        // Get unique values for filters
        $uniqueValues = $this->getUniqueValues ( $request, clone $query );

        return view ( 'dashboard.evaluasi.urgent.evaluasi_urgent', [ 
            'headerPage'   => "Evaluasi Urgent",
            'page'         => 'Data Evaluasi Urgent',
            'menuContext'  => 'evaluasi_urgent',
            'proyeks'      => $proyeks,
            'TableData'    => $TableData,
            'uniqueValues' => $uniqueValues, // Add this line
        ] );
    }

    /**
     * Check if the given string is a month name
     */
    private function isMonthName ( $string )
    {
        $months = array_merge (
            // Indonesian month names
            [ 
                'januari',
                'februari',
                'maret',
                'april',
                'mei',
                'juni',
                'juli',
                'agustus',
                'september',
                'oktober',
                'november',
                'desember'
            ],
            // English month names
            [ 
                'january',
                'february',
                'march',
                'april',
                'may',
                'june',
                'july',
                'august',
                'september',
                'october',
                'november',
                'december'
            ]
        );

        return in_array ( strtolower ( $string ), $months );
    }

    /**
     * Get month number from month name
     */
    private function getMonthNumber ( $monthName )
    {
        $monthMap = [ 
            // Indonesian
            'januari'   => 1,
            'februari'  => 2,
            'maret'     => 3,
            'april'     => 4,
            'mei'       => 5,
            'juni'      => 6,
            'juli'      => 7,
            'agustus'   => 8,
            'september' => 9,
            'oktober'   => 10,
            'november'  => 11,
            'desember'  => 12,
            // English
            'january'   => 1,
            'february'  => 2,
            'march'     => 3,
            'april'     => 4,
            'may'       => 5,
            'june'      => 6,
            'july'      => 7,
            'august'    => 8,
            'september' => 9,
            'october'   => 10,
            'november'  => 11,
            'december'  => 12
        ];

        return $monthMap[ strtolower ( $monthName ) ] ?? null;
    }

    private function getStatusQuery ( $query, $status )
    {
        return match ( strtolower ( $status ) )
        {
            'pengajuan' => $query->where ( function ($q)
                {
                    $q->where ( 'is_finalized', false )
                    ->where ( 'is_evaluated', false )
                    ->where ( 'is_approved_vp', false )
                    ->where ( 'is_approved_svp', false );
                } ),
            'evaluasi' => $query->where ( function ($q)
                {
                    $q->where ( 'is_finalized', true )
                    ->where ( 'is_evaluated', false )
                    ->where ( 'is_approved_vp', false )
                    ->where ( 'is_approved_svp', false );
                } ),
            'disetujui' => $query->where ( function ($q)
                {
                    $q->where ( 'is_finalized', true )
                    ->where ( 'is_evaluated', true )
                    ->where ( 'is_approved_vp', true )
                    ->where ( 'is_approved_svp', true );
                } ),
            'menunggu approval vp' => $query->where ( function ($q)
                {
                    $q->where ( 'is_finalized', true )
                    ->where ( 'is_evaluated', true )
                    ->where ( 'is_approved_vp', false )
                    ->where ( 'is_approved_svp', false );
                } ),
            'menunggu approval svp' => $query->where ( function ($q)
                {
                    $q->where ( 'is_finalized', true )
                    ->where ( 'is_evaluated', true )
                    ->where ( 'is_approved_vp', true )
                    ->where ( 'is_approved_svp', false );
                } ),
            default => $query
        };
    }

    private function getUniqueValues ( Request $request = null, $baseQuery = null )
    {
        if ( ! $baseQuery )
        {
            $baseQuery = RKB::where ( 'tipe', 'urgent' );
        }

        // Create fresh queries
        $nomorQuery = RKB::query ()
            ->select ( 'nomor' )
            ->where ( 'tipe', 'urgent' )
            ->whereNotNull ( 'nomor' )
            ->distinct ()
            ->orderBy ( 'nomor' );

        $proyekQuery = RKB::query ()
            ->where ( 'tipe', 'urgent' )
            ->join ( 'proyek', 'rkb.id_proyek', '=', 'proyek.id' )
            ->select ( 'proyek.nama' )
            ->distinct ()
            ->orderBy ( 'proyek.nama' );

        $periodeQuery = RKB::query ()
            ->select ( 'periode' )
            ->where ( 'tipe', 'urgent' )
            ->whereNotNull ( 'periode' )
            ->distinct ()
            ->orderByDesc ( 'periode' );

        // Apply base conditions
        foreach ( $baseQuery->getQuery ()->wheres as $where )
        {
            $nomorQuery->addNestedWhereQuery ( $baseQuery->getQuery () );
            $proyekQuery->addNestedWhereQuery ( $baseQuery->getQuery () );
            $periodeQuery->addNestedWhereQuery ( $baseQuery->getQuery () );
            break;
        }

        return [ 
            'nomor'   => $nomorQuery->pluck ( 'nomor' ),
            'proyek'  => $proyekQuery->pluck ( 'nama' ),
            'periode' => $periodeQuery->pluck ( 'periode' ),
        ];
    }

    private function handleNomorFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_nomor' ) )
        {
            $nomor = explode ( ',', $request->selected_nomor );
            if ( in_array ( 'null', $nomor ) )
            {
                $nonNullValues = array_filter ( $nomor, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'nomor' )
                        ->orWhere ( 'nomor', '-' )
                        ->orWhereIn ( 'nomor', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'nomor', $nomor );
            }
        }
    }

    private function handleProyekFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_proyek' ) )
        {
            $proyekNames = explode ( ',', $request->selected_proyek );
            if ( in_array ( 'null', $proyekNames ) )
            {
                $nonNullValues = array_filter ( $proyekNames, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereHas ( 'proyek', function ($sq) use ($nonNullValues)
                    {
                        $sq->whereIn ( 'nama', $nonNullValues );
                    } )->orWhereDoesntHave ( 'proyek' );
                } );
            }
            else
            {
                $query->whereHas ( 'proyek', function ($q) use ($proyekNames)
                {
                    $q->whereIn ( 'nama', $proyekNames );
                } );
            }
        }
    }

    private function handlePeriodeFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_periode' ) )
        {
            $periodeValues = explode ( ',', $request->selected_periode );
            if ( in_array ( 'null', $periodeValues ) )
            {
                $nonNullValues = array_filter ( $periodeValues, fn ( $value ) => $value !== 'null' );
                $query->where ( function ($q) use ($nonNullValues)
                {
                    $q->whereNull ( 'periode' )
                        ->orWhereIn ( 'periode', $nonNullValues );
                } );
            }
            else
            {
                $query->whereIn ( 'periode', $periodeValues );
            }
        }
    }

    private function handleStatusFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_status' ) )
        {
            $statusValues = explode ( ',', $request->selected_status );
            $query->where ( function ($q) use ($statusValues)
            {
                foreach ( $statusValues as $status )
                {
                    $q->orWhere ( function ($subQ) use ($status)
                    {
                        switch ($status)
                        {
                            case 'pengajuan':
                                $subQ->where ( 'is_finalized', false )
                                    ->where ( 'is_evaluated', false )
                                    ->where ( 'is_approved_vp', false )
                                    ->where ( 'is_approved_svp', false );
                                break;
                            case 'evaluasi':
                                $subQ->where ( 'is_finalized', true )
                                    ->where ( 'is_evaluated', false )
                                    ->where ( 'is_approved_vp', false )
                                    ->where ( 'is_approved_svp', false );
                                break;
                            case 'menunggu approval vp':
                                $subQ->where ( 'is_finalized', true )
                                    ->where ( 'is_evaluated', true )
                                    ->where ( 'is_approved_vp', false )
                                    ->where ( 'is_approved_svp', false );
                                break;
                            case 'menunggu approval svp':
                                $subQ->where ( 'is_finalized', true )
                                    ->where ( 'is_evaluated', true )
                                    ->where ( 'is_approved_vp', true )
                                    ->where ( 'is_approved_svp', false );
                                break;
                            case 'disetujui':
                                $subQ->where ( 'is_finalized', true )
                                    ->where ( 'is_evaluated', true )
                                    ->where ( 'is_approved_vp', true )
                                    ->where ( 'is_approved_svp', true );
                                break;
                            case 'tidak diketahui':
                                $subQ->where ( function ($q)
                                {
                                    $q->whereNotIn ( 'id', function ($sub)
                                    {
                                        $sub->select ( 'id' )
                                            ->from ( 'rkb' )
                                            ->where ( function ($q1)
                                            {
                                                // Pengajuan
                                                $q1->where ( 'is_finalized', false )
                                                    ->where ( 'is_evaluated', false )
                                                    ->where ( 'is_approved_vp', false )
                                                    ->where ( 'is_approved_svp', false );
                                            } )
                                            ->orWhere ( function ($q2)
                                            {
                                                // Evaluasi
                                                $q2->where ( 'is_finalized', true )
                                                    ->where ( 'is_evaluated', false )
                                                    ->where ( 'is_approved_vp', false )
                                                    ->where ( 'is_approved_svp', false );
                                            } )
                                            ->orWhere ( function ($q3)
                                            {
                                                // Menunggu Approval VP
                                                $q3->where ( 'is_finalized', true )
                                                    ->where ( 'is_evaluated', true )
                                                    ->where ( 'is_approved_vp', false )
                                                    ->where ( 'is_approved_svp', false );
                                            } )
                                            ->orWhere ( function ($q4)
                                            {
                                                // Menunggu Approval SVP
                                                $q4->where ( 'is_finalized', true )
                                                    ->where ( 'is_evaluated', true )
                                                    ->where ( 'is_approved_vp', true )
                                                    ->where ( 'is_approved_svp', false );
                                            } )
                                            ->orWhere ( function ($q5)
                                            {
                                                // Disetujui
                                                $q5->where ( 'is_finalized', true )
                                                    ->where ( 'is_evaluated', true )
                                                    ->where ( 'is_approved_vp', true )
                                                    ->where ( 'is_approved_svp', true );
                                            } );
                                    } );
                                } );
                                break;
                        }
                    } );
                }
            } );
        }
        return $query;
    }
}
