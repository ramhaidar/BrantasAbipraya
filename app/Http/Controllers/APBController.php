<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\APB;
use App\Models\ATB;
use App\Models\Alat;
use App\Models\User;
use App\Models\Nilai;
use App\Models\Saldo;
use App\Models\Proyek;
use App\Models\Komponen;
use App\Models\Perbaikan;
use App\Models\Pemeliharaan;
use Illuminate\Http\Request;
use App\Models\OilLubricants;
use App\Models\MaintenanceKit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class APBController extends Controller
{
    public function ex_unit_alat ( Request $request )
    {
        return $this->showApbPage (
            'Hutang Unit Alat',
            'Data APB EX Unit Alat',
            $request->id_proyek
        );
    }

    public function ex_panjar_unit_alat ( Request $request )
    {
        return $this->showApbPage (
            'Panjar Unit Alat',
            'Data APB EX Panjar Unit Alat',
            $request->id_proyek
        );
    }

    public function ex_mutasi_saldo ( Request $request )
    {
        return $this->showApbPage (
            'Mutasi Saldo',
            'Data APB EX Mutasi Saldo',
            $request->id_proyek
        );
    }

    public function ex_panjar_proyek ( Request $request )
    {
        return $this->showApbPage (
            'Panjar Proyek',
            'Data APB EX Panjar Proyek',
            $request->id_proyek
        );
    }

    private function showApbPage ( $type, $pageTitle, $id_proyek )
    {
        $user = Auth::user ();
        if ( $user->role === 'Admin' )
        {
            $proyek  = Proyek::findOrFail ( $id_proyek );
            $proyeks = Proyek::with ( "users" )->orderBy ( "created_at", "asc" )->orderBy ( "id", "asc" )->get ();
            $alat    = Alat::get ();
        }
        else
        {
            $proyeks = $user->proyek ()->with ( "users" )->orderBy ( "created_at", "asc" )->orderBy ( "id", "asc" )->get ();
            if ( ! $proyeks->pluck ( 'id' )->contains ( $id_proyek ) )
            {
                abort ( 403, 'Anda tidak memiliki akses ke proyek ini.' );
            }
            $proyek = $proyeks->where ( 'id', $id_proyek )->first ();
            $alat   = Alat::where ( 'id_user', $user->id )->get ();
        }
        if ( $type == "Hutang Unit Alat" )
        {
            $atb = ATB::where ( 'id_proyek', $id_proyek )->with ( [ 'komponen', 'saldo.apb.alat', 'masterData' ] )->when ( $type, function ($query)
            {
                $query->whereHas ( 'saldo.atb', function ($query)
                {
                    $query->whereIn ( 'tipe', [ 'Hutang Unit Alat', 'Mutasi Proyek' ] );
                } );
            } )->with ( [ 
                        'saldo.apb' => function ($query)
                        {
                            $query->orderBy ( 'tanggal', 'asc' );
                        }
                    ] )->get ();
        }
        elseif ( $type == "Mutasi Saldo" )
        {
            $atb = ATB::where ( 'id_proyek', $id_proyek ) // Filter ATB berdasarkan proyek
                ->with ( [ 
                    'komponen',
                    'saldo.apb.alat',
                    'masterData'
                ] )
                ->whereHas ( 'saldo.apb', function ($query)
                {
                    $query->whereNotNull ( 'id_tujuan_proyek' );
                } )
                ->with ( [ 
                    'saldo.apb' => function ($query)
                    {
                        $query->orderBy ( 'tanggal', 'asc' );
                    }
                ] )
                ->get ();
        }
        else
        {
            $atb = ATB::where ( 'id_proyek', $id_proyek )->with ( [ 'komponen', 'saldo.apb.alat', 'masterData' ] )->when ( $type, function ($query) use ($type)
            {
                $query->whereHas ( 'saldo.atb', function ($query) use ($type)
                {
                    $query->where ( 'tipe', $type );
                } );
            } )->with ( [ 
                        'saldo.apb' => function ($query)
                        {
                            $query->orderBy ( 'tanggal', 'asc' );
                        }
                    ] )->get ();
        }
        $totalNilai        = 0;
        $totalPerbaikan    = 0;
        $totalPemeliharaan = 0;
        foreach ( $atb as $the_atb )
        {
            foreach ( $the_atb->saldo->apb as $the_apb )
            {
                $saldo               = $the_atb->saldo;
                $the_atb->totalNilai = $saldo ? $the_atb->bruto : 0;
                $totalNilai += $the_atb->totalNilai;
                if ( $saldo && $the_atb->komponen && $the_atb->komponen->first_group->name == 'PERBAIKAN' )
                {
                    $the_atb->totalPerbaikan = $saldo->net;
                    $totalPerbaikan += $the_atb->totalPerbaikan;
                }
                else
                {
                    $the_atb->totalPerbaikan = 0;
                }
                if ( $saldo && $the_atb->komponen && $the_atb->komponen->first_group->name == 'PEMELIHARAAN' )
                {
                    $the_atb->totalPemeliharaan = $saldo->net;
                    $totalPemeliharaan += $the_atb->totalPemeliharaan;
                }
                else
                {
                    $the_atb->totalPemeliharaan = 0;
                }
            }
        }
        $totalNilaiFormatted        = 'Rp' . number_format ( $totalNilai, 0, ',', '.' );
        $totalPerbaikanFormatted    = 'Rp' . number_format ( $totalPerbaikan, 0, ',', '.' );
        $totalPemeliharaanFormatted = 'Rp' . number_format ( $totalPemeliharaan, 0, ',', '.' );
        if ( $type == "Hutang Unit Alat" || $type == "Mutasi Saldo" )
        {
            $atbWithQuantity = ATB::where ( 'id_proyek', $proyek->id )->whereHas ( 'saldo', function ($query)
            {
                $query->where ( 'current_quantity', '>', 0 );
            } )->whereHas ( 'saldo.atb', function ($query)
            {
                $query->whereIn ( 'tipe', [ 'Hutang Unit Alat', 'Mutasi Proyek' ] );
            } )->with ( 'proyek', 'masterData' )->get ();
        }
        else
        {
            $atbWithQuantity = ATB::where ( 'id_proyek', $proyek->id )->whereHas ( 'saldo', function ($query)
            {
                $query->where ( 'current_quantity', '>', 0 );
            } )->whereHas ( 'saldo.atb', function ($query) use ($type)
            {
                $query->where ( 'tipe', $type );
            } )->with ( 'proyek', 'masterData' )->get ();
        }
        $atbAll = ATB::where ( 'id_proyek', $proyek->id )->with ( 'proyek', 'saldo.apb.alat', 'saldo.atb', 'komponen', 'masterData' )->get ();

        if ( $type == "Mutasi Saldo" )
        {
            $allProyek = Proyek::with ( "users" )->orderBy ( "created_at", "asc" )->orderBy ( "id", "asc" )->get ();
        }

        $data = [ 
            'proyek'            => $proyek,
            'proyeks'           => $proyeks,
            'page'              => $pageTitle,
            'atbList'           => $atb,
            'atbAll'            => $atbAll,
            'alatList'          => $alat,
            'totalNilai'        => $totalNilaiFormatted,
            'totalPerbaikan'    => $totalPerbaikanFormatted,
            'totalPemeliharaan' => $totalPemeliharaanFormatted,
            'atbWithQuantity'   => $atbWithQuantity,
        ];

        if ( isset ( $allProyek ) )
        {
            $data[ 'allProyek' ] = $allProyek;
        }

        return view ( 'dashboard.apb.apb', $data );
    }

    public function store ( Request $request )
    {
        $rules = [ 
            'id_atb'      => 'required|exists:atb,id',
            'tanggal'     => 'required|date',
            'quantity'    => 'required|numeric|min:1',
            'dokumentasi' => 'nullable|mimes:jpeg,jpg,png,heic,heif|max:2048'
        ];

        if ( ! $request->filled ( 'id_tujuan_proyek' ) )
        {
            $rules[ 'id_alat' ] = 'required|exists:alat,id';
        }
        else
        {
            $rules[ 'id_tujuan_proyek' ] = 'required|exists:proyek,id';
        }
        $request->validate ( $rules );

        $atb   = ATB::find ( $request->id_atb );
        $saldo = $atb->saldo;

        if ( $request->quantity > $saldo->current_quantity )
        {
            return back ()->withErrors ( [ 'quantity' => 'Quantity tidak boleh lebih dari current quantity' ] );
        }

        $dokumentasiPath = $request->hasFile ( 'dokumentasi' ) ? $request->file ( 'dokumentasi' )->store ( 'dokumentasi/apb', 'private' ) : null;

        if ( $request->filled ( 'id_tujuan_proyek' ) )
        {
            $tujuanProyek = Proyek::find ( $request->id_tujuan_proyek );
            $newSaldo     = Saldo::create ( [ 'current_quantity' => $request->quantity, 'net' => $saldo->net ] );

            $newAtb = ATB::create ( [ 
                'tipe'           => 'Mutasi Proyek',
                'tanggal'        => $request->tanggal,
                'quantity'       => $request->quantity,
                'satuan'         => $atb->satuan,
                'harga'          => $atb->harga,
                'net'            => $atb->net,
                'ppn'            => $atb->ppn,
                'bruto'          => $atb->bruto,
                'id_komponen'    => $atb->id_komponen,
                'id_saldo'       => $newSaldo->id,
                'id_proyek'      => $tujuanProyek->id,
                'id_asal_proyek' => $atb->id_proyek,
                'id_master_data' => $atb->id_master_data
            ] );

            $apb = APB::create ( [ 
                'tanggal'          => $request->tanggal,
                'quantity'         => $request->quantity,
                'id_saldo'         => $saldo->id,
                'id_tujuan_proyek' => $tujuanProyek->id,
                'dokumentasi'      => $dokumentasiPath
            ] );

            $saldo->current_quantity -= $request->quantity;
            $saldo->save ();

            return back ()->with ( 'success', 'APB Mutasi Saldo berhasil dibuat' );
        }

        $apb = APB::create ( [ 
            'tanggal'     => $request->tanggal,
            'quantity'    => $request->quantity,
            'id_alat'     => $request->id_alat,
            'id_saldo'    => $saldo->id,
            'dokumentasi' => $dokumentasiPath
        ] );

        $saldo->current_quantity -= $request->quantity;
        $saldo->save ();

        return back ()->with ( 'success', 'APB berhasil dibuat' );
    }

    public function show ( $id )
    {
        $atb = APB::find ( $id );
        if ( ! $atb )
        {
            return response ()->json ( [ 'message' => 'APB not found',], 404 );
        }
        return response ()->json ( [ 'data' => $atb,] );
    }

    public function showByID ( ATB $atb )
    {
        $atb->load ( 'komponen' )->load ( 'saldo.apb.alat' )->load ( "proyek" );
        $kodeMapping = [ 
            'A1'  => 'A1: CABIN',
            'A2'  => 'A2: ENGINE SYSTEM',
            'A3'  => 'A3: TRANSMISSION SYSTEM',
            'A4'  => 'A4: CHASSIS & SWING MACHINERY',
            'A5'  => 'A5: DIFFERENTIAL SYSTEM',
            'A6'  => 'A6: ELECTRICAL SYSTEM',
            'A7'  => 'A7: HYDRAULIC/PNEUMATIC SYSTEM',
            'A8'  => 'A8: STEERING SYSTEM',
            'A9'  => 'A9: BRAKE SYSTEM',
            'A10' => 'A10: SUSPENSION',
            'A11' => 'A11: ATTACHMENT',
            'A12' => 'A12: UNDERCARRIAGE',
            'A13' => 'A13: FINAL DRIVE',
            'A14' => 'A14: FREIGHT COST',
            'B11' => 'B11: Oil Filter',
            'B12' => 'B12: Fuel Filter',
            'B13' => 'B13: Air Filter',
            'B14' => 'B14: Hydraulic Filter',
            'B15' => 'B15: Transmission Filter',
            'B16' => 'B16: Differential Filter',
            'B21' => 'B21: Engine Oil',
            'B22' => 'B22: Hydraulic Oil',
            'B23' => 'B23: Transmission Oil',
            'B24' => 'B24: Final Drive Oil',
            'B25' => 'B25: Swing & Damper Oil',
            'B26' => 'B26: Differential Oil',
            'B27' => 'B27: Grease',
            'B28' => 'B28: Brake & Power Steering Fluid',
            'B29' => 'B29: Coolant',
            'B3'  => 'B3: Tyre',
            'C1'  => 'C1: Workshop',
        ];
        if ( $atb->komponen && isset ( $kodeMapping[ $atb->komponen->kode ] ) )
        {
            $atb->komponen->kode = $kodeMapping[ $atb->komponen->kode ];
        }
        return response ()->json ( [ 'data' => $atb ] );
    }

    public function getAPBbyID ( APB $apb, $filterProyekId = null )
    {
        // Dapatkan user yang sedang login
        $user = Auth::user ();

        // Admin bisa mengakses semua proyek, sedangkan pegawai hanya bisa mengakses proyek yang diassign
        if ( $user->role === 'Admin' )
        {
            // Cek apakah filter proyek ID ada
            $proyeksQuery = Proyek::with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" );

            // Jika ada filterProyekId, tambahkan kondisi untuk memfilter berdasarkan ID tersebut
            if ( $filterProyekId )
            {
                $proyeksQuery->where ( 'id', $filterProyekId );
            }

            $proyeks = $proyeksQuery->get ();
        }
        else
        {
            // Pegawai hanya dapat mengakses proyek yang diassign kepada mereka
            $proyeksQuery = $user->proyek ()
                ->with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" );

            // Jika ada filterProyekId, tambahkan kondisi untuk memfilter berdasarkan ID tersebut
            if ( $filterProyekId )
            {
                $proyeksQuery->where ( 'id', $filterProyekId );
            }

            $proyeks = $proyeksQuery->get ();

            // Validasi jika proyek yang diakses adalah proyek yang terkait dengan user
            if ( ! $proyeks->pluck ( 'id' )->contains ( $apb->saldo->atb->id_proyek ) )
            {
                abort ( 403, 'Anda tidak memiliki akses ke proyek ini.' );
            }
        }

        $apb->load ( 'alat' )->load ( 'saldo.atb.proyek' );
        $kodeMapping = [ 
            'A1'  => 'A1: CABIN',
            'A2'  => 'A2: ENGINE SYSTEM',
            'A3'  => 'A3: TRANSMISSION SYSTEM',
            'A4'  => 'A4: CHASSIS & SWING MACHINERY',
            'A5'  => 'A5: DIFFERENTIAL SYSTEM',
            'A6'  => 'A6: ELECTRICAL SYSTEM',
            'A7'  => 'A7: HYDRAULIC/PNEUMATIC SYSTEM',
            'A8'  => 'A8: STEERING SYSTEM',
            'A9'  => 'A9: BRAKE SYSTEM',
            'A10' => 'A10: SUSPENSION',
            'A11' => 'A11: ATTACHMENT',
            'A12' => 'A12: UNDERCARRIAGE',
            'A13' => 'A13: FINAL DRIVE',
            'A14' => 'A14: FREIGHT COST',
            'B11' => 'B11: Oil Filter',
            'B12' => 'B12: Fuel Filter',
            'B13' => 'B13: Air Filter',
            'B14' => 'B14: Hydraulic Filter',
            'B15' => 'B15: Transmission Filter',
            'B16' => 'B16: Differential Filter',
            'B21' => 'B21: Engine Oil',
            'B22' => 'B22: Hydraulic Oil',
            'B23' => 'B23: Transmission Oil',
            'B24' => 'B24: Final Drive Oil',
            'B25' => 'B25: Swing & Damper Oil',
            'B26' => 'B26: Differential Oil',
            'B27' => 'B27: Grease',
            'B28' => 'B28: Brake & Power Steering Fluid',
            'B29' => 'B29: Coolant',
            'B3'  => 'B3: Tyre',
            'C1'  => 'C1: Workshop',
        ];

        if ( $apb->komponen && isset ( $kodeMapping[ $apb->komponen->kode ] ) )
        {
            $apb->komponen->kode = $kodeMapping[ $apb->komponen->kode ];
        }

        return response ()->json ( [ 'data' => $apb ] );
    }

    public function getAPB ( APB $apb )
    {
        // Dapatkan user yang sedang login
        $user = Auth::user ();

        // Admin bisa mengakses semua proyek, sedangkan pegawai hanya bisa mengakses proyek yang diassign
        if ( $user->role === 'Admin' )
        {
            $proyeks = Proyek::with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();
        }
        else
        {
            $proyeks = $user->proyek ()
                ->with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            // Validasi jika proyek yang diakses adalah proyek yang terkait dengan user
            if ( ! $proyeks->pluck ( 'id' )->contains ( $apb->saldo->atb->id_proyek ) )
            {
                abort ( 403, 'Anda tidak memiliki akses ke proyek ini.' );
            }
        }

        $apb->load ( 'alat' )->load ( 'saldo.atb.komponen' );

        $kodeMapping = [ 
            'A1'  => 'A1: CABIN',
            'A2'  => 'A2: ENGINE SYSTEM',
            'A3'  => 'A3: TRANSMISSION SYSTEM',
            'A4'  => 'A4: CHASSIS & SWING MACHINERY',
            'A5'  => 'A5: DIFFERENTIAL SYSTEM',
            'A6'  => 'A6: ELECTRICAL SYSTEM',
            'A7'  => 'A7: HYDRAULIC/PNEUMATIC SYSTEM',
            'A8'  => 'A8: STEERING SYSTEM',
            'A9'  => 'A9: BRAKE SYSTEM',
            'A10' => 'A10: SUSPENSION',
            'A11' => 'A11: ATTACHMENT',
            'A12' => 'A12: UNDERCARRIAGE',
            'A13' => 'A13: FINAL DRIVE',
            'A14' => 'A14: FREIGHT COST',
            'B11' => 'B11: Oil Filter',
            'B12' => 'B12: Fuel Filter',
            'B13' => 'B13: Air Filter',
            'B14' => 'B14: Hydraulic Filter',
            'B15' => 'B15: Transmission Filter',
            'B16' => 'B16: Differential Filter',
            'B21' => 'B21: Engine Oil',
            'B22' => 'B22: Hydraulic Oil',
            'B23' => 'B23: Transmission Oil',
            'B24' => 'B24: Final Drive Oil',
            'B25' => 'B25: Swing & Damper Oil',
            'B26' => 'B26: Differential Oil',
            'B27' => 'B27: Grease',
            'B28' => 'B28: Brake & Power Steering Fluid',
            'B29' => 'B29: Coolant',
            'B3'  => 'B3: Tyre',
            'C1'  => 'C1: Workshop',
        ];

        if ( $apb->komponen && isset ( $kodeMapping[ $apb->komponen->kode ] ) )
        {
            $apb->komponen->kode = $kodeMapping[ $apb->komponen->kode ];
        }

        return response ()->json ( [ 'data' => $apb ] );
    }

    public function update ( Request $request, $id )
    {
        // Temukan APB berdasarkan ID
        $apb = APB::find ( $id );
        if ( ! $apb )
        {
            return back ()->with ( 'error', 'APB not found' );
        }

        $request->validate ( [ 
            'id_atb'      => 'required|exists:atb,id',
            'id_alat'     => 'required|exists:alat,id',
            'tanggal'     => 'required|date',
            'quantity'    => 'required|numeric|min:1',
            'dokumentasi' => 'nullable|mimes:jpeg,jpg,png,heic,heif|max:2048', // Validasi untuk dokumentasi
        ] );

        // Ambil data ATB terkait
        $atb   = ATB::find ( $request->id_atb );
        $saldo = $atb->saldo;

        // Validasi quantity
        if ( $request->quantity > $saldo->current_quantity + $apb->quantity )
        {
            return back ()->withErrors ( [ 'quantity' => 'Quantity tidak boleh lebih dari current quantity' ] );
        }

        // Ambil id_proyek dari alat yang digunakan
        $alat = Alat::find ( $request->id_alat );

        // Upload dokumentasi baru jika ada
        $dokumentasiPath = $apb->dokumentasi; // Simpan path dokumentasi lama
        if ( $request->hasFile ( 'dokumentasi' ) )
        {
            // Hapus dokumentasi lama jika ada
            if ( $dokumentasiPath )
            {
                Storage::disk ( 'private' )->delete ( $dokumentasiPath );
            }

            // Simpan dokumentasi baru ke folder 'dokumentasi/apb' di disk private
            $dokumentasiPath = $request->file ( 'dokumentasi' )->store ( 'dokumentasi/apb', 'private' );
        }

        // Update data APB
        $apb->update ( [ 
            'tanggal'     => $request->tanggal,
            'quantity'    => $request->quantity,
            'id_alat'     => $request->id_alat,
            'id_saldo'    => $saldo->id,
            'dokumentasi' => $dokumentasiPath, // Simpan path dokumentasi
        ] );

        // Update current_quantity di saldo
        $saldo->current_quantity += $apb->quantity - $request->quantity;
        $saldo->save ();

        return back ()->with ( 'success', 'Berhasil mengubah data APB' );
    }

    public function destroy ( $id )
    {
        // Temukan APB berdasarkan ID
        $apb = APB::find ( $id );
        if ( ! $apb )
        {
            return back ()->with ( 'error', 'APB not found' );
        }

        // Ambil data ATB terkait
        $saldo = Saldo::where ( 'id', $apb->id_saldo )->first ();

        // Kembalikan current_quantity di saldo
        if ( $saldo )
        {
            $saldo->update ( [ 'current_quantity' => $saldo->current_quantity + $apb->quantity ] );
        }

        // Hapus dokumentasi jika ada
        if ( $apb->dokumentasi )
        {
            Storage::disk ( 'private' )->delete ( $apb->dokumentasi );
        }

        // Hapus APB
        $apb->delete ();

        return back ()->with ( 'success', 'Berhasil menghapus data APB' );
    }

    public function fetchData ( Request $request )
    {
        $startDate = Carbon::parse ( $request->start_date )->startOfDay ();
        $endDate   = Carbon::parse ( $request->end_date )->endOfDay ();
        $proyekId  = $request->id_proyek;
        $tipe      = $request->tipe;

        $atbListQuery = ATB::with ( [ 'komponen.first_group', 'komponen.second_group', 'proyek', 'saldo.apb.alat' ] )
            ->where ( 'id_proyek', $proyekId )
            ->whereHas ( 'saldo.apb', function ($query) use ($startDate, $endDate)
            {
                $query->whereBetween ( 'tanggal', [ $startDate, $endDate ] );
            } );

        // Jika tipe adalah "Mutasi Saldo", tambahkan kondisi khusus seperti di showApbPage
        if ( $tipe == "Mutasi Saldo" )
        {
            $atbListQuery = ATB::whereHas ( 'saldo.apb', function ($query)
            {
                $query->whereNotNull ( 'id_tujuan_proyek' );
            } );
            return response ()->json ( [ 'data' => $atbListQuery ] );

        }
        else
        {
            // Jika bukan "Mutasi Saldo", gunakan logika whereHas yang ada
            $atbListQuery->when ( $tipe, function ($query) use ($tipe)
            {
                $query->whereHas ( 'saldo.atb', function ($query) use ($tipe)
                {
                    $query->where ( 'tipe', $tipe );
                } );
            } );
        }

        // Melanjutkan query dengan penambahan whereBetween tanggal pada saldo.apb
        $atbListQuery->with ( [ 
            'saldo.apb' => function ($query) use ($startDate, $endDate)
            {
                $query->whereBetween ( 'tanggal', [ $startDate, $endDate ] )
                    ->orderBy ( 'tanggal', 'asc' );
            }
        ] );

        // Eksekusi query
        $atbList = $atbListQuery->get ();

        // Load data master untuk setiap ATB
        $atbList->load ( 'masterData' );

        return response ()->json ( [ 'data' => $atbList ] );
    }

    public function showDokumentasi ( $filename )
    {
        // Periksa apakah pengguna saat ini login
        if ( ! auth ()->check () )
        {
            abort ( 403, 'Unauthorized access' );
        }

        // Path folder private/dokumentasi/apb
        $filePath = 'dokumentasi/apb/' . $filename;

        // Periksa apakah file ada di storage
        if ( ! Storage::disk ( 'private' )->exists ( $filePath ) )
        {
            abort ( 404, 'File not found' );
        }
        // Kembalikan gambar sebagai respons
        return response ()->file ( Storage::disk ( 'private' )->path ( $filePath ) );
    }
}