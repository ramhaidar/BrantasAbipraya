<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EvaluasiRKBGeneralController extends Controller
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

        return view ( 'dashboard.evaluasi.general.evaluasi_general', [ 
            'headerPage'   => 'Evaluasi General',
            'page'         => 'Data Evaluasi General',
            'menuContext'  => 'evaluasi_general',
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
            ->where ( 'tipe', 'general' );

        $this->applyUserRoleFilters ( $query, $user, $proyeks );
        $this->applyFilters ( $request, $query );
        $this->applySearch ( $request, $query );

        return $query;
    }

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

    // Filter Methods
    private function applyFilters ( Request $request, $query )
    {
        $this->handleNomorFilter ( $request, $query );
        $this->handleProyekFilter ( $request, $query );
        $this->handlePeriodeFilter ( $request, $query );
        $this->handleStatusFilter ( $request, $query );
    }

    // Add handling for nomor filter
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

    // Add handling for proyek filter
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

                // Check if the array contains "null" or "Empty/Null"
                $hasNullFilter = in_array ( 'null', $periodeValues ) || in_array ( 'Empty/Null', $periodeValues );

                if ( $hasNullFilter )
                {
                    // Filter out "null" and "Empty/Null" values
                    $nonNullValues = array_filter ( $periodeValues, function ($value)
                    {
                        return $value !== 'null' && $value !== 'Empty/Null';
                    } );

                    $query->where ( function ($q) use ($nonNullValues)
                    {
                        // Only check for NULL values - don't compare with '-' for PostgreSQL date fields
                        $q->whereNull ( 'periode' );

                        // Add non-null values if they exist
                        if ( ! empty ( $nonNullValues ) )
                        {
                            $q->orWhereIn ( 'periode', $nonNullValues );
                        }
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
            try
            {
                $statusValues = $this->getSelectedValues ( $request->selected_status );
                $query->where ( function ($q) use ($statusValues)
                {
                    foreach ( $statusValues as $status )
                    {
                        $q->orWhere ( function ($subQ) use ($status)
                        {
                            switch (strtolower ( trim ( $status ) ))
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
                                case 'empty/null':
                                    // Handle status that doesn't match any defined condition
                                    $subQ->where ( function ($q)
                                    {
                                        $q->whereRaw ( 'NOT (
                                            (is_finalized = false AND is_evaluated = false AND is_approved_vp = false AND is_approved_svp = false) OR
                                            (is_finalized = true AND is_evaluated = false AND is_approved_vp = false AND is_approved_svp = false) OR
                                            (is_finalized = true AND is_evaluated = true AND is_approved_vp = false AND is_approved_svp = false) OR
                                            (is_finalized = true AND is_evaluated = true AND is_approved_vp = true AND is_approved_svp = false) OR
                                            (is_finalized = true AND is_evaluated = true AND is_approved_vp = true AND is_approved_svp = true)
                                        )' );
                                    } );
                                    break;
                            }
                        } );
                    }
                } );
            }
            catch ( \Exception $e )
            {
                \Log::error ( 'Error in status filter: ' . $e->getMessage () );
            }
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
        // Get all RKB entries of type 'general' directly from the database
        // without applying any filters from the current request
        $allRKBs = RKB::where ( 'tipe', 'general' )->get ();

        // Get unique nomor values
        $nomors = $allRKBs->pluck ( 'nomor' )
            ->filter () // Remove null values
            ->unique ()
            ->sort ()
            ->values ();

        // Get unique periode values
        $periodes = $allRKBs->pluck ( 'periode' )
            ->filter () // Remove null values
            ->unique ()
            ->sortDesc ()
            ->values ();

        // Get all proyek IDs from RKBs
        $proyekIds = $allRKBs->pluck ( 'id_proyek' )->unique ();

        // Get all related proyeks' names
        $proyeks = Proyek::whereIn ( 'id', $proyekIds )
            ->orderBy ( 'nama' )
            ->pluck ( 'nama' );

        // Static status options remain unchanged
        $statuses = $this->getAllStatusOptions ();

        return [ 
            'nomor'   => $nomors,
            'proyek'  => $proyeks,
            'periode' => $periodes,
            'status'  => $statuses
        ];
    }

    // Helper Methods
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
    }

    private function handleStatusSearch ( $q, $search )
    {
        $statusKeywords = [ 'pengajuan', 'evaluasi', 'disetujui', 'menunggu approval vp', 'menunggu approval svp', 'tidak diketahui' ];
        if ( in_array ( strtolower ( $search ), $statusKeywords ) )
        {
            $this->getStatusQuery ( $q, $search );
        }
    }

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
                ->where ( 'tipe', 'general' )
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
