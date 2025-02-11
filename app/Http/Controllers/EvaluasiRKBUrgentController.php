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
    // Main CRUD Operations
    public function index ( Request $request )
    {
        $perPage      = $this->getPerPage ( $request );
        $user         = Auth::user ();
        $proyeks      = $this->getProyeks ( $user );
        $query        = $this->buildQuery ( $request, $user, $proyeks );
        $uniqueValues = $this->getUniqueValues ( $request, clone $query );
        $TableData    = $this->getTableData ( $query, $perPage );

        return view ( 'dashboard.evaluasi.urgent.evaluasi_urgent', [ 
            'headerPage'   => "Evaluasi Urgent",
            'page'         => 'Data Evaluasi Urgent',
            'menuContext'  => 'evaluasi_urgent',
            'proyeks'      => $proyeks,
            'TableData'    => $TableData,
            'uniqueValues' => $uniqueValues,
        ] );
    }

    // Query Building Methods
    private function buildQuery ( Request $request, $user, $proyeks )
    {
        $query = RKB::query ()
            ->with ( [ 'proyek' ] )
            ->where ( 'tipe', 'urgent' );

        $this->applyUserRoleFilters ( $query, $user, $proyeks );
        $this->applyFilters ( $request, $query );
        $this->applySearch ( $request, $query );

        return $query;
    }

    // Filter Methods
    private function applyFilters ( Request $request, $query )
    {
        $this->handleNomorFilter ( $request, $query );
        $this->handleProyekFilter ( $request, $query );
        $this->handlePeriodeFilter ( $request, $query );
        $this->handleStatusFilter ( $request, $query );
    }

    private function handleNomorFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_nomor' ) )
        {
            try
            {
                $nomor = $this->getSelectedValues ( $request->selected_nomor );
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
            catch ( \Exception $e )
            {
                \Log::error ( 'Error in nomor filter: ' . $e->getMessage () );
            }
        }
        return $query;
    }

    private function handleProyekFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_proyek' ) )
        {
            try
            {
                $proyekNames = $this->getSelectedValues ( $request->selected_proyek );
                if ( in_array ( 'null', $proyekNames ) )
                {
                    $nonNullValues = array_filter ( $proyekNames, fn ( $value ) => $value !== 'null' );
                    $query->where ( function ($q) use ($nonNullValues)
                    {
                        $q->whereDoesntHave ( 'proyek' )
                            ->orWhereHas ( 'proyek', function ($sq) use ($nonNullValues)
                            {
                                $sq->whereIn ( 'nama', $nonNullValues );
                            } );
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
            catch ( \Exception $e )
            {
                \Log::error ( 'Error in proyek filter: ' . $e->getMessage () );
            }
        }
        return $query;
    }

    private function handlePeriodeFilter ( Request $request, $query )
    {
        if ( $request->filled ( 'selected_periode' ) )
        {
            try
            {
                $periodeValues = $this->getSelectedValues ( $request->selected_periode );
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
            catch ( \Exception $e )
            {
                \Log::error ( 'Error in periode filter: ' . $e->getMessage () );
            }
        }
        return $query;
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

    // Search Methods
    private function applySearch ( Request $request, $query )
    {
        if ( $request->has ( 'search' ) )
        {
            $search = $request->get ( 'search' );
            $query->where ( function ($q) use ($search)
            {
                $this->buildSearchQuery ( $q, $search );
            } );
        }
    }

    private function buildSearchQuery ( $q, $search )
    {
        $q->where ( 'nomor', 'ilike', "%{$search}%" )
            ->orWhereHas ( 'proyek', function ($query) use ($search)
            {
                $query->where ( 'nama', 'ilike', "%{$search}%" );
            } )
            ->orWhere ( function ($q) use ($search)
            {
                $this->handleDateSearch ( $q, $search );
            } )
            ->orWhere ( function ($q) use ($search)
            {
                $this->handleStatusSearch ( $q, $search );
            } );
    }

    // Data Retrieval Methods
    private function getPerPage ( Request $request )
    {
        $allowedPerPage = [ 10, 25, 50, 100 ];
        return in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;
    }

    private function getProyeks ( $user )
    {
        $proyeksQuery = Proyek::with ( "users" );
        if ( $user->role === 'koordinator_proyek' )
        {
            $proyeksQuery->whereHas ( 'users', function ($query) use ($user)
            {
                $query->where ( 'users.id', $user->id );
            } );
        }
        return $proyeksQuery->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();
    }

    private function getTableData ( $query, $perPage )
    {
        return $query->orderBy ( 'periode', 'desc' )
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();
    }

    private function getUniqueValues ( Request $request = null, $baseQuery = null )
    {
        if ( ! $baseQuery )
        {
            $baseQuery = RKB::where ( 'tipe', 'urgent' );
        }

        // Create clones for each filter type
        $nomorQuery   = clone $baseQuery;
        $proyekQuery  = clone $baseQuery;
        $periodeQuery = clone $baseQuery;

        // Apply cascading filters
        if ( $request )
        {
            if ( $request->filled ( 'selected_nomor' ) )
            {
                $proyekQuery  = $this->handleNomorFilter ( $request, $proyekQuery );
                $periodeQuery = $this->handleNomorFilter ( $request, $periodeQuery );
            }
            if ( $request->filled ( 'selected_proyek' ) )
            {
                $nomorQuery   = $this->handleProyekFilter ( $request, $nomorQuery );
                $periodeQuery = $this->handleProyekFilter ( $request, $periodeQuery );
            }
            if ( $request->filled ( 'selected_periode' ) )
            {
                $nomorQuery  = $this->handlePeriodeFilter ( $request, $nomorQuery );
                $proyekQuery = $this->handlePeriodeFilter ( $request, $proyekQuery );
            }
        }

        return [ 
            'nomor'   => $this->getFilteredNomors ( $nomorQuery ),
            'proyek'  => $this->getFilteredProyeks ( $proyekQuery ),
            'periode' => $this->getFilteredPeriodes ( $periodeQuery ),
            'status'  => $this->getAllStatusOptions () // Static status options
        ];
    }

    private function getFilteredNomors ( $query )
    {
        return $query->select ( 'nomor' )
            ->whereNotNull ( 'nomor' )
            ->distinct ()
            ->get ()
            ->pluck ( 'nomor' )
            ->sort ()
            ->values ();
    }

    private function getFilteredProyeks ( $query )
    {
        $proyekIds = $query->pluck ( 'id_proyek' )->unique ();
        return Proyek::whereIn ( 'id', $proyekIds )
            ->orderBy ( 'nama' )
            ->pluck ( 'nama' );
    }

    private function getFilteredPeriodes ( $query )
    {
        return $query->select ( 'periode' )
            ->whereNotNull ( 'periode' )
            ->distinct ()
            ->get ()
            ->pluck ( 'periode' )
            ->sortDesc ()
            ->values ();
    }

    private function getAllStatusOptions ()
    {
        return collect ( [ 
            'pengajuan',
            'evaluasi',
            'menunggu approval vp',
            'menunggu approval svp',
            'disetujui',
            'tidak diketahui'
        ] );
    }

    // Helper Methods
    private function applyUserRoleFilters ( $query, $user, $proyeks )
    {
        if ( $user->role === 'koordinator_proyek' )
        {
            $proyekIds = $proyeks->pluck ( 'id' )->toArray ();
            $query->whereIn ( 'id_proyek', $proyekIds );
        }
        elseif ( $user->role === 'Pegawai' )
        {
            $this->applyPegawaiFilter ( $query, $user );
        }
        elseif ( $user->role === 'Boss' )
        {
            $this->applyBossFilter ( $query, $user );
        }
    }

    private function applyPegawaiFilter ( $query, $user )
    {
        $query->whereHas ( 'proyek', function ($q) use ($user)
        {
            $q->whereHas ( 'users', function ($q) use ($user)
            {
                $q->where ( 'users.id', $user->id );
            } );
        } );
    }

    private function applyBossFilter ( $query, $user )
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

    private function handleDateSearch ( $q, $search )
    {
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
    }

    private function handleStatusSearch ( $q, $search )
    {
        $statusKeywords = [ 'pengajuan', 'evaluasi', 'disetujui', 'menunggu approval vp', 'menunggu approval svp', 'tidak diketahui' ];
        if ( in_array ( strtolower ( $search ), $statusKeywords ) )
        {
            $this->getStatusQuery ( $q, $search );
        }
    }

    // Unique Value Retrieval Methods
    private function getUniqueNomors ( $query )
    {
        return $query->select ( 'nomor' )
            ->whereNotNull ( 'nomor' )
            ->distinct ()
            ->get ()
            ->pluck ( 'nomor' )
            ->sort ()
            ->values ();
    }

    private function getUniqueProyeks ( $query )
    {
        return Proyek::whereIn ( 'id', function ($subquery) use ($query)
        {
            $subquery->select ( 'id_proyek' )
                ->from ( 'rkb' )
                ->where ( 'tipe', 'urgent' )
                ->distinct ();
        } )
            ->orderBy ( 'nama' )
            ->pluck ( 'nama' );
    }

    private function getUniquePeriodes ( $query )
    {
        return $query->select ( 'periode' )
            ->whereNotNull ( 'periode' )
            ->distinct ()
            ->get ()
            ->pluck ( 'periode' )
            ->sortDesc ()
            ->values ();
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

    // Add helper method to decode base64 values
    private function getSelectedValues ( $paramValue )
    {
        if ( ! $paramValue ) return [];

        try
        {
            return explode ( '||', base64_decode ( $paramValue ) );
        }
        catch ( \Exception $e )
        {
            \Log::error ( 'Error decoding parameter value: ' . $e->getMessage () );
            return [];
        }
    }
}
