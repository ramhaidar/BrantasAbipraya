<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RKB;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RKBUrgentController extends Controller
{
    public function index ( Request $request )
    {
        $perPage      = $this->getPerPage ( $request );
        $user         = Auth::user ();
        $proyeks      = $this->getProyeks ( $user );
        $query        = $this->buildQuery ( $request, $user, $proyeks );
        $uniqueValues = $this->getUniqueValues ( $request, $query );

        $TableData = $this->getTableData ( $query, $perPage );

        return view ( 'dashboard.rkb.urgent.urgent', [ 
            'headerPage'   => 'RKB Urgent',
            'page'         => 'Data RKB Urgent',
            'proyeks'      => $proyeks,
            'TableData'    => $TableData,
            'uniqueValues' => $uniqueValues,
            'menuContext'  => 'rkb_urgent',
        ] );
    }

    private function getPerPage ( Request $request )
    {
        $allowedPerPage = [ 10, 25, 50, 100 ];
        return in_array ( (int) $request->get ( 'per_page' ), $allowedPerPage ) ? (int) $request->get ( 'per_page' ) : 10;
    }

    private function buildQuery ( Request $request, $user, $proyeks )
    {
        $query = RKB::query ()
            ->with ( [ 'proyek', 'linkAlatDetailRkbs' ] )
            ->where ( 'tipe', 'urgent' );

        if ( $user->role === 'koordinator_proyek' )
        {
            $proyekIds = $proyeks->pluck ( 'id' )->toArray ();
            $query->whereIn ( 'id_proyek', $proyekIds );
        }

        $this->applyFilters ( $request, $query );
        $this->applySearch ( $request, $query );

        return $query;
    }

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

                // Check for null indicators (both "null" and "Empty/Null")
                $nullValues       = [ 'null', 'Empty/Null' ];
                $hasNullIndicator = count ( array_intersect ( $nullValues, $periodeValues ) ) > 0;

                if ( $hasNullIndicator )
                {
                    // Filter out null indicators
                    $nonNullValues = array_filter ( $periodeValues, function ($value) use ($nullValues)
                    {
                        return ! in_array ( $value, $nullValues );
                    } );

                    $query->where ( function ($q) use ($nonNullValues)
                    {
                        $q->whereNull ( 'periode' );

                        // Only add non-null values if they exist
                        if ( count ( $nonNullValues ) > 0 )
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

    private function getUniqueValues ( Request $request = null, $baseQuery = null )
    {
        // Query to get all RKB records with 'urgent' type
        $rkbs = RKB::where ( 'tipe', 'urgent' );

        // Get all unique nomor values
        $nomorValues = $rkbs->clone ()
            ->whereNotNull ( 'nomor' )
            ->distinct ()
            ->pluck ( 'nomor' );

        // Get all unique proyek names associated with urgent RKBs
        $proyekIds    = $rkbs->clone ()->distinct ()->pluck ( 'id_proyek' );
        $proyekValues = Proyek::whereIn ( 'id', $proyekIds )
            ->orderBy ( 'nama' )
            ->pluck ( 'nama' );

        // Get all unique periode values
        $periodeValues = $rkbs->clone ()
            ->whereNotNull ( 'periode' )
            ->orderBy ( 'periode', 'desc' )
            ->distinct ()
            ->pluck ( 'periode' );

        return [ 
            'nomor'   => $nomorValues,
            'proyek'  => $proyekValues,
            'periode' => $periodeValues
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

    private function applySearch ( Request $request, $query )
    {
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
                        $statusKeywords = [ 'pengajuan', 'evaluasi', 'disetujui', 'tidak diketahui' ];
                        if ( in_array ( strtolower ( $search ), $statusKeywords ) )
                        {
                            $this->getStatusQuery ( $q, $search );
                        }
                    } );
            } );
        }
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

    // Add this new helper method
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

    private function getAllStatusOptions ()
    {
        return collect ( [ 
            'pengajuan',
            'evaluasi',
            'disetujui',
            'tidak diketahui'
        ] );
    }

    public function show ( $id )
    {
        // Gunakan eager loading untuk relasi terkait
        $rkb = RKB::with ( [ 
            'proyek', // Relasi ke Proyek
            'linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.kategoriSparepart',
            'linkAlatDetailRkbs.linkRkbDetails.detailRkbGeneral.masterDataSparepart'
        ] )->find ( $id );

        // Cek apakah RKB ditemukan
        if ( ! $rkb )
        {
            return response ()->json ( [ 
                'success' => false,
                'message' => 'RKB Tidak Ditemukan.'
            ], 404 );
        }

        return response ()->json ( [ 
            'success' => true,
            'data'    => $rkb
        ] );
    }

    public function store ( Request $request )
    {
        // Validasi data request
        $validatedData = $request->validate ( [ 
            'nomor'   => [ 'required', 'string', 'max:255', 'unique:rkb,nomor' ], // Validasi nomor unik
            'periode' => [ 'required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/' ], // Validasi periode dalam format YYYY-MM
            'proyek'  => [ 'required', 'integer', 'exists:proyek,id' ], // Validasi proyek ID
        ] );

        // Tambahkan hari default (26) agar sesuai dengan tipe DATE di database
        $validatedData[ 'periode' ] = $validatedData[ 'periode' ] . '-26';

        // Pastikan kolom 'proyek' dipetakan ke 'id_proyek'
        $validatedData[ 'id_proyek' ] = $validatedData[ 'proyek' ];
        unset ( $validatedData[ 'proyek' ] ); // Hapus field 'proyek' karena tidak ada di tabel

        // Set default tipe ke 'General'
        $validatedData[ 'tipe' ] = 'urgent';

        // Simpan data ke tabel RKB
        RKB::create ( $validatedData );

        // Redirect dengan pesan sukses
        return redirect ()->route ( 'rkb_urgent.index' )->with ( 'success', 'RKB General successfully created' );
    }

    public function update ( Request $request, $id )
    {
        $rkb = RKB::find ( $id );

        if ( ! $rkb )
        {
            return redirect ()->route ( 'rkb_urgent.index' )->with ( 'error', 'RKB not found' );
        }

        // Modify the request to map 'proyek' to 'id_proyek'
        $request->merge ( [ 'id_proyek' => $request->proyek ] );

        // Validate the request
        $validatedData = $request->validate ( [ 
            'nomor'     => [ 'sometimes', 'required', 'string', 'max:255', Rule::unique ( 'rkb', 'nomor' )->ignore ( $rkb->id ) ],
            'periode'   => [ 'sometimes', 'required', 'date' ],
            'id_proyek' => [ 'sometimes', 'required', 'integer', 'exists:proyek,id' ],
        ] );

        // Update the RKB record
        $rkb->update ( $validatedData );

        return redirect ()->route ( 'rkb_urgent.index' )->with ( 'success', 'RKB successfully updated' );
    }

    public function destroy ( $id )
    {
        $rkb = RKB::find ( $id );

        if ( ! $rkb )
        {
            return redirect ()->route ( 'rkb_urgent.index' )->with ( 'error', 'RKB not found' );
        }

        $rkb->delete ();

        return redirect ()->route ( 'rkb_urgent.index' )->with ( 'success', 'RKB successfully deleted' );
    }

    public function finalize ( $id )
    {
        // Ambil RKB beserta relasi-relasi yang diperlukan
        $rkb = RKB::with (
            'linkAlatDetailRkbs.timelineRkbUrgents',
            'linkAlatDetailRkbs.lampiranRkbUrgent'
        )->findOrFail ( $id );

        // Validasi jika RKB tidak memiliki linkAlatDetailRkbs
        if ( ! isset ( $rkb->linkAlatDetailRkbs ) || $rkb->linkAlatDetailRkbs->isEmpty () )
        {
            return redirect ()->back ()->with ( 'error', 'Anda belum mengisi Data Detail RKB.' );
        }

        // Iterasi setiap linkAlatDetailRkbs untuk memastikan semua data terisi
        foreach ( $rkb->linkAlatDetailRkbs as $detail )
        {
            // Cek jika timelineRkbUrgents tidak diisi
            if ( ! isset ( $detail->timelineRkbUrgents ) || $detail->timelineRkbUrgents->isEmpty () )
            {
                return redirect ()->back ()->with ( 'error', "Detail RKB dengan Kode Alat {$detail->masterDataAlat->kode_alat} belum memiliki Data Timeline RKB." );
            }

            // Cek jika lampiranRkbUrgent tidak diisi
            if ( ! isset ( $detail->lampiranRkbUrgent ) )
            {
                return redirect ()->back ()->with ( 'error', "Detail RKB dengan Kode Alat {$detail->masterDataAlat->kode_alat} belum memiliki Data Lampiran RKB." );
            }
        }

        // Jika semua validasi lolos, finalisasi RKB
        $rkb->is_finalized = true;
        $rkb->save ();

        return redirect ()->route ( 'rkb_urgent.index' )->with ( 'success', 'RKB berhasil difinalisasi.' );
    }
}
