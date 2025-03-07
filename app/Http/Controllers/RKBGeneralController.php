<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RKBGeneralController extends Controller
{
    // Main CRUD Operations
    public function index ( Request $request )
    {
        $perPage      = $this->getPerPage ( $request );
        $user         = Auth::user ();
        $proyeks      = $this->getProyeks ( $user );
        $query        = $this->buildQuery ( $request, $user, $proyeks );
        $uniqueValues = $this->getUniqueValues ( $request, $query );
        $TableData    = $this->getTableData ( $query, $perPage );

        return view ( 'dashboard.rkb.general.general', [ 
            'headerPage'   => 'RKB General',
            'page'         => 'Data RKB General',
            'proyeks'      => $proyeks,
            'TableData'    => $TableData,
            'uniqueValues' => $uniqueValues,
        ] );
    }

    public function show ( $id )
    {
        $rkb = $this->getRKBWithRelations ( $id );

        if ( ! $rkb )
        {
            return $this->notFoundResponse ();
        }

        return response ()->json ( [ 
            'success' => true,
            'data'    => $rkb
        ] );
    }

    public function store ( Request $request )
    {
        $validatedData = $this->validateRKBData ( $request );
        $this->processAndSaveRKB ( $validatedData );
        return redirect ()->route ( 'rkb_general.index' )->with ( 'success', 'RKB General successfully created' );
    }

    public function update ( Request $request, $id )
    {
        $rkb = RKB::find ( $id );
        if ( ! $rkb )
        {
            return redirect ()->route ( 'rkb_general.index' )->with ( 'error', 'RKB not found' );
        }

        $validatedData = $this->validateUpdateData ( $request, $rkb );
        $rkb->update ( $validatedData );

        return redirect ()->route ( 'rkb_general.index' )->with ( 'success', 'RKB successfully updated' );
    }

    public function destroy ( $id )
    {
        $rkb = RKB::find ( $id );
        if ( ! $rkb )
        {
            return redirect ()->route ( 'rkb_general.index' )->with ( 'error', 'RKB not found' );
        }

        $rkb->delete ();
        return redirect ()->route ( 'rkb_general.index' )->with ( 'success', 'RKB successfully deleted' );
    }

    // Helper Methods for Query Building
    private function getPerPage ( Request $request )
    {
        $allowedPerPage = [ 10, 25, 50, 100 ];
        return in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;
    }

    private function buildQuery ( Request $request, $user, $proyeks )
    {
        $query = RKB::query ()
            ->with ( [ 'proyek', 'linkAlatDetailRkbs' ] )
            ->where ( 'tipe', 'general' );

        if ( $user->role === 'koordinator_proyek' )
        {
            $proyekIds = $proyeks->pluck ( 'id' )->toArray ();
            $query->whereIn ( 'id_proyek', $proyekIds );
        }

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
                                            (is_finalized = true AND is_approved_svp = false) OR
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
        $q->where ( 'nomor', 'ilike', "%{$search}%" ) // Menggunakan ilike untuk case-insensitive
            ->orWhereHas ( 'proyek', function ($query) use ($search)
            {
                $query->where ( 'nama', 'ilike', "%{$search}%" );
            } )
            ->orWhere ( function ($q) use ($search)
            {
                // Handle year search (4 digits)
                if ( preg_match ( '/^[0-9]{4}$/', $search ) )
                {
                    $q->whereRaw ( 'EXTRACT(YEAR FROM periode) = ?', [ $search ] ); // PostgreSQL syntax
                }
                // Handle month name in Indonesian or English
                elseif ( $this->isMonthName ( $search ) )
                {
                    $monthNumber = $this->getMonthNumber ( $search );
                    if ( $monthNumber )
                    {
                        $q->whereRaw ( 'EXTRACT(MONTH FROM periode) = ?', [ $monthNumber ] ); // PostgreSQL syntax
                    }
                }
                // Handle "Month Year" format
                elseif ( preg_match ( '/^([A-Za-z]+)\s+([0-9]{4})$/', $search, $matches ) )
                {
                    $monthNumber = $this->getMonthNumber ( $matches[ 1 ] );
                    if ( $monthNumber )
                    {
                        $q->whereRaw (
                            'EXTRACT(MONTH FROM periode) = ? AND EXTRACT(YEAR FROM periode) = ?',
                            [ $monthNumber, $matches[ 2 ] ]
                        ); // PostgreSQL syntax
                    }
                }
            } )
            ->orWhere ( function ($q) use ($search)
            {
                // Handle status search
                $statusKeywords = [ 'pengajuan', 'evaluasi', 'disetujui', 'tidak diketahui' ];
                if ( in_array ( strtolower ( $search ), $statusKeywords ) )
                {
                    $this->getStatusQuery ( $q, $search );
                }
            } );
    }

    // Date and Month Handling Methods
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

    // Status Query Methods
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
                    ->where ( 'is_approved_svp', false );
                } ),
            'disetujui' => $query->where ( function ($q)
                {
                    $q->where ( 'is_finalized', true )
                    ->where ( 'is_evaluated', true )
                    ->where ( 'is_approved_vp', true )
                    ->where ( 'is_approved_svp', true );
                } ),
            'tidak diketahui' => $query->where ( function ($q)
                {
                    $q->whereRaw ( 'NOT (
                    (is_finalized = false AND is_evaluated = false AND is_approved_vp = false AND is_approved_svp = false) OR
                    (is_finalized = true AND is_approved_svp = false) OR
                    (is_finalized = true AND is_evaluated = true AND is_approved_vp = true AND is_approved_svp = true)
                )' );
                } ),
            default => $query
        };
    }

    // Validation Methods
    private function validateRKBData ( Request $request )
    {
        return $request->validate ( [ 
            'nomor'   => [ 'required', 'string', 'max:255', 'unique:rkb,nomor' ],
            'periode' => [ 'required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/' ],
            'proyek'  => [ 'required', 'integer', 'exists:proyek,id' ],
        ] );
    }

    private function validateUpdateData ( Request $request, $rkb )
    {
        return $request->validate ( [ 
            'nomor'     => [ 'sometimes', 'required', 'string', 'max:255', Rule::unique ( 'rkb', 'nomor' )->ignore ( $rkb->id ) ],
            'periode'   => [ 'sometimes', 'required', 'date' ],
            'id_proyek' => [ 'sometimes', 'required', 'integer', 'exists:proyek,id' ],
        ] );
    }

    // Finalization Methods
    public function finalize ( $id )
    {
        $rkb = RKB::findOrFail ( $id );

        if ( ! $this->canBeFinalized ( $rkb ) )
        {
            return redirect ()->back ()->with ( 'error', 'Anda belum mengisi data detail RKB' );
        }

        $rkb->update ( [ 'is_finalized' => true ] );
        return redirect ()->route ( 'rkb_general.index' )->with ( 'success', 'RKB successfully finalized' );
    }

    private function canBeFinalized ( $rkb )
    {
        return isset ( $rkb->linkAlatDetailRkbs ) && $rkb->linkAlatDetailRkbs->count () > 0;
    }

    // Response Formatting Methods
    private function notFoundResponse ()
    {
        return response ()->json ( [ 
            'success' => false,
            'message' => 'RKB Tidak Ditemukan.'
        ], 404 );
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

        return $proyeksQuery
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();
    }

    private function getUniqueValues ( Request $request = null, $baseQuery = null )
    {
        // Get all RKBs with type 'general'
        $allRkbs = RKB::where ( 'tipe', 'general' )->get ();

        // Get all project IDs from RKBs
        $proyekIds = $allRkbs->pluck ( 'id_proyek' )->filter ()->unique ();

        // Get unique values for each field, filtering out empty values
        return [ 
            'nomor'   => $allRkbs->pluck ( 'nomor' )
                ->filter ()  // Remove null/empty values
                ->unique ()
                ->values (),

            'proyek'  => Proyek::whereIn ( 'id', $proyekIds )
                ->orderBy ( 'nama' )
                ->pluck ( 'nama' ),

            'periode' => $allRkbs->pluck ( 'periode' )
                ->filter ()
                ->unique ()
                ->sortByDesc ( function ($date)
                {
                    return strtotime ( $date );
                } )
                ->values ()
        ];
    }

    private function getTableData ( $query, $perPage )
    {
        return $query
            ->orderBy ( 'periode', 'desc' )
            ->orderBy ( 'updated_at', 'desc' )
            ->orderBy ( 'id', 'desc' )
            ->paginate ( $perPage )
            ->withQueryString ();
    }

    private function processAndSaveRKB ( $validatedData )
    {
        $validatedData[ 'periode' ]   = $validatedData[ 'periode' ] . '-26';
        $validatedData[ 'id_proyek' ] = $validatedData[ 'proyek' ];
        unset ( $validatedData[ 'proyek' ] );
        $validatedData[ 'tipe' ] = 'general';
        RKB::create ( $validatedData );
    }

    private function getRKBWithRelations ( $id )
    {
        return RKB::with ( [ 
            'proyek',
            'linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.kategoriSparepart',
            'linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.masterDataSparepart'
        ] )->find ( $id );
    }

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
