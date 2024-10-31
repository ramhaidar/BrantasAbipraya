<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ATB;
use App\Models\User;
use App\Models\Saldo;
use App\Models\Proyek;
use App\Models\Komponen;
use App\Exports\ATBExport;
use App\Imports\ATBImport;
use App\Models\FirstGroup;
use App\Models\MasterData;
use App\Models\SecondGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ATBController extends Controller
{
    public function hutang_unit_alat ( Request $request )
    {
        return $this->showAtbPage (
            "Hutang Unit Alat",
            "Data ATB Hutang Unit Alat",
            $request->id_proyek
        );
    }

    public function panjar_unit_alat ( Request $request )
    {
        return $this->showAtbPage (
            "Panjar Unit Alat",
            "Data ATB Panjar Unit Alat",
            $request->id_proyek
        );
    }

    public function mutasi_proyek ( Request $request )
    {
        return $this->showAtbPage (
            "Mutasi Proyek",
            "Data ATB Mutasi Proyek",
            $request->id_proyek
        );
    }

    public function panjar_proyek ( Request $request )
    {
        return $this->showAtbPage (
            "Panjar Proyek",
            "Data ATB Panjar Proyek",
            $request->id_proyek
        );
    }

    private function showAtbPage ( $tipe, $pageTitle, $id_proyek )
    {
        $user = Auth::user ();

        $proyeks        = [];
        $proyek         = null;
        $masterDataList = [];

        $masterDataList = MasterData::all ();

        if ( $user->role === 'Admin' )
        {
            $proyek  = Proyek::findOrFail ( $id_proyek );
            $proyeks = Proyek::with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            // $masterDataList = MasterData::all ();
        }
        elseif ( $user->role === 'Boss' )
        {
            $proyeks = $user->proyek ()
                ->with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            $proyek = $proyeks->where ( 'id', $id_proyek )->first ();

            $usersInProyek = $proyeks->pluck ( 'users.*.id' )->flatten ();

            // $masterDataList = MasterData::whereIn ( 'id_user', $usersInProyek )->get ();
        }
        elseif ( $user->role === 'Pegawai' )
        {
            $proyeks = $user->proyek ()
                ->with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            $proyek = $proyeks->where ( 'id', $id_proyek )->first ();

            // $masterDataList = MasterData::where ( 'id_user', $user->id )->get ();
        }

        if ( ! $proyek )
        {
            abort ( 404, 'Proyek tidak ditemukan.' );
        }

        // if ( $tipe != "Mutasi Proyek" )
        // {
        $atb = ATB::with ( [ 
            'komponen.first_group',
            'masterData',
        ] )
            ->orderBy ( 'tanggal', 'asc' )
            ->where ( 'tipe', $tipe )
            ->where ( 'id_proyek', $id_proyek )
            ->get ();
        // }
        // else
        // {
        //     $atb = ATB::with ( [ 
        //         'komponen.first_group',
        //         'masterData',
        //     ] )
        //         ->orderBy ( 'tanggal', 'asc' )
        //         ->where ( 'tipe', "Hutang Unit Alat" )
        //         ->whereNotNull ( 'id_asal_proyek' )
        //         ->where ( 'id_proyek', $id_proyek )
        //         ->get ();
        // }

        $komponen = Komponen::with ( [ "first_group", "second_group" ] )
            ->get ()
            ->sortBy ( "second_group.name" )
            ->sortBy ( "first_group.name" );

        $totalNilai        = 0;
        $totalPerbaikan    = 0;
        $totalPemeliharaan = 0;
        $totalWorkshop     = 0;

        foreach ( $atb as $x )
        {
            $x->totalNilai = $x->komponen ? $x->net : 0;
            $totalNilai += $x->totalNilai;

            if ( $x->komponen && $x->komponen->first_group->name == "PERBAIKAN" )
            {
                $x->totalPerbaikan = $x->net;
                $totalPerbaikan += $x->totalPerbaikan;
            }
            else
            {
                $x->totalPerbaikan = 0;
            }

            if ( $x->komponen && $x->komponen->first_group->name == "PEMELIHARAAN" )
            {
                $x->totalPemeliharaan = $x->net;
                $totalPemeliharaan += $x->totalPemeliharaan;
            }
            else
            {
                $x->totalPemeliharaan = 0;
            }

            if ( $x->komponen && $x->komponen->first_group->name == "WAREHOUSE" )
            {
                $x->totalWorkshop = $x->net;
                $totalWorkshop += $x->totalWorkshop;
            }
            else
            {
                $x->totalWorkshop = 0;
            }
        }

        $totalNilaiFormatted        = "Rp" . number_format ( $totalNilai, 0, ",", "." );
        $totalPerbaikanFormatted    = "Rp" . number_format ( $totalPerbaikan, 0, ",", "." );
        $totalPemeliharaanFormatted = "Rp" . number_format ( $totalPemeliharaan, 0, ",", "." );
        $totalWorkshopFormatted     = "Rp" . number_format ( $totalWorkshop, 0, ",", "." );

        return view ( "dashboard.atb.atb", [ 
            "proyek"            => $proyek,
            "proyeks"           => $proyeks,
            "page"              => $pageTitle,
            "atbList"           => $atb,
            "komponen"          => $komponen,
            "totalNilai"        => $totalNilaiFormatted,
            "totalPerbaikan"    => $totalPerbaikanFormatted,
            "totalPemeliharaan" => $totalPemeliharaanFormatted,
            "totalWorkshop"     => $totalWorkshopFormatted,
            "masterDataList"    => $masterDataList,
        ] );
    }

    public function store ( Request $request )
    {
        $request->validate ( [ 
            'tipe'        => [ 
                'required',
                'in:hutang-unit-alat,panjar-unit-alat,mutasi-proyek,panjar-proyek',
            ],
            'asal_proyek' => $request->tipe == 'mutasi-proyek' ? [ 'required', 'string', 'max:255' ] : [ 'nullable' ],
            'tanggal'     => [ 'required', 'date' ],
            'kode'        => [ 'required', 'string', 'max:255' ],
            'master_data' => [ 'required', 'exists:master_data,id' ],
            'quantity'    => [ 'required', 'integer' ],
            'satuan'      => [ 'required', 'in:PCS,SET,BTL,LTR,KG,BTG,PAIL,MTR' ],
            'harga'       => [ 'required', 'numeric' ],
            'dokumentasi' => [ 'nullable', 'mimes:png,jpg,jpeg,heic,heif', 'max:2048' ],
        ] );

        $tipe = match ( $request->tipe )
        {
            'hutang-unit-alat' => 'Hutang Unit Alat',
            'panjar-unit-alat' => 'Panjar Unit Alat',
            'mutasi-proyek' => 'Mutasi Proyek',
            'panjar-proyek' => 'Panjar Proyek',
            default => null,
        };

        $quantity = $request->quantity;
        $harga    = $request->harga;
        $net      = $quantity * $harga;
        $ppn      = $net * 0.11;
        $bruto    = $net + $ppn;

        $masterData = MasterData::findOrFail ( $request->master_data );

        $first_group  = FirstGroup::firstOrCreate ( [ 'name' => $this->getFirstGroupName ( $request->kode ) ] );
        $second_group = $this->getSecondGroup ( $request->kode );

        $komponen = Komponen::create ( [ 
            'kode'            => $request->kode,
            'first_group_id'  => $first_group->id ?? null,
            'second_group_id' => $second_group->id ?? null,
        ] );

        $dokumentasiPath = null;
        if ( $request->hasFile ( 'dokumentasi' ) )
        {
            $dokumentasiPath = $request->file ( 'dokumentasi' )->store ( 'dokumentasi/atb', 'private' );
        }

        $atb = ATB::create ( [ 
            'tipe'           => $tipe,
            'tanggal'        => $request->tanggal,
            'quantity'       => $quantity,
            'satuan'         => $request->satuan,
            'harga'          => $harga,
            'net'            => $net,
            'ppn'            => $ppn,
            'bruto'          => $bruto,
            'id_komponen'    => $komponen->id,
            'id_proyek'      => $request->id_proyek,
            'asal_proyek'    => $request->asal_proyek,
            'dokumentasi'    => $dokumentasiPath,
            'id_master_data' => $masterData->id,
        ] );

        $saldo = Saldo::create ( [ 
            'current_quantity' => $quantity,
            'net'              => $harga * $quantity,
        ] );

        $atb->update ( [ 
            'id_saldo' => $saldo->id,
        ] );

        return back ()->with ( 'success', 'Data ATB berhasil disimpan' );
    }

    private function getFirstGroupName ( $kode )
    {
        if ( preg_match ( '/^A(1[0-4]?|[1-9])$/', $kode ) )
        {
            return 'PERBAIKAN';
        }
        elseif ( $kode === 'B3' )
        {
            return 'PEMELIHARAAN';
        }
        elseif ( $kode === 'C1' )
        {
            return 'WAREHOUSE';
        }
        elseif ( $kode >= 'B11' && $kode <= 'B29' )
        {
            return 'PEMELIHARAAN';
        }
        return null;
    }

    private function getSecondGroup ( $kode )
    {
        if ( $kode >= 'B11' && $kode <= 'B16' )
        {
            return SecondGroup::firstOrCreate ( [ 'name' => 'MAINTENANCE KIT' ] );
        }
        elseif ( $kode >= 'B21' && $kode <= 'B29' )
        {
            return SecondGroup::firstOrCreate ( [ 'name' => 'OIL & LUBRICANTS' ] );
        }
        return null;
    }

    public function show ( $id )
    {
        $atb = ATB::find ( $id );
        if ( ! $atb )
        {
            return response ()->json ( [ "message" => "ATB not found" ], 404 );
        }
        return response ()->json ( [ "data" => $atb ] );
    }

    public function showByID ( ATB $atb )
    {
        $komponen    = Komponen::all ()
            ->whereNotNull ( "name_for_atb" )
            ->sortBy ( "SecondGroup.name" )
            ->sortBy ( "FirstGroup.name" );
        $atbWithUser = User::where ( "id", $atb->created_by )
            ->get ()
            ->first ();
        $id_komponen = $atb->komponen->id;
        $index       = array_search (
            $id_komponen,
            array_column ( $komponen->toArray (), "id" )
        );
        $kodeMapping = [ 
            "A1"  => "A1: CABIN",
            "A2"  => "A2: ENGINE SYSTEM",
            "A3"  => "A3: TRANSMISSION SYSTEM",
            "A4"  => "A4: CHASSIS & SWING MACHINERY",
            "A5"  => "A5: DIFFERENTIAL SYSTEM",
            "A6"  => "A6: ELECTRICAL SYSTEM",
            "A7"  => "A7: HYDRAULIC/PNEUMATIC SYSTEM",
            "A8"  => "A8: STEERING SYSTEM",
            "A9"  => "A9: BRAKE SYSTEM",
            "A10" => "A10: SUSPENSION",
            "A11" => "A11: ATTACHMENT",
            "A12" => "A12: UNDERCARRIAGE",
            "A13" => "A13: FINAL DRIVE",
            "A14" => "A14: FREIGHT COST",
            "B11" => "B11: Oil Filter",
            "B12" => "B12: Fuel Filter",
            "B13" => "B13: Air Filter",
            "B14" => "B14: Hydraulic Filter",
            "B15" => "B15: Transmission Filter",
            "B16" => "B16: Differential Filter",
            "B21" => "B21: Engine Oil",
            "B22" => "B22: Hydraulic Oil",
            "B23" => "B23: Transmission Oil",
            "B24" => "B24: Final Drive Oil",
            "B25" => "B25: Swing & Damper Oil",
            "B26" => "B26: Differential Oil",
            "B27" => "B27: Grease",
            "B28" => "B28: Brake & Power Steering Fluid",
            "B29" => "B29: Coolant",
            "B3"  => "B3: Tyre",
            "C1"  => "C1: Workshop",
        ];
        if ( $atb->komponen && isset ( $kodeMapping[ $atb->komponen->kode ] ) )
        {
            $atb->komponen->kode = $kodeMapping[ $atb->komponen->kode ];
        }
        $atb->index_komponen = $index;
        $atb->user           = $atbWithUser;
        $atb->load ( 'proyek' );
        $atb->load ( 'masterData' );
        $atb->load ( 'asalProyek' );

        return response ()->json ( [ "data" => $atb ] );
    }

    public function update ( Request $request, $id )
    {
        $request->validate ( [ 
            'dokumentasi' => [ 'nullable', 'mimes:png,jpg,jpeg,heic,heif', 'max:2048' ],
        ] );

        $atb = ATB::find ( $id );
        if ( ! $atb )
        {
            return response ()->json ( [ 'message' => 'ATB not found' ], 404 );
        }

        $tipe = match ( $request->tipe )
        {
            'hutang-unit-alat' => 'Hutang Unit Alat',
            'panjar-unit-alat' => 'Panjar Unit Alat',
            'mutasi-proyek' => 'Mutasi Proyek',
            'panjar-proyek' => 'Panjar Proyek',
            default => null,
        };

        $quantity = $request->quantity;
        $harga    = $request->harga;
        $net      = $quantity * $harga;
        $ppn      = $net * 0.11;
        $bruto    = $net + $ppn;

        $first_group  = FirstGroup::firstOrCreate ( [ 'name' => $request->kode === 'B3' ? 'PEMELIHARAAN' : 'PERBAIKAN' ] );
        $second_group = null;

        if ( preg_match ( '/^A(1[0-4]?|[1-9])$/', $request->kode ) )
        {
            $first_group = FirstGroup::firstOrCreate ( [ 'name' => 'PERBAIKAN' ] );
        }
        elseif ( $request->kode === 'B3' )
        {
            $first_group = FirstGroup::firstOrCreate ( [ 'name' => 'PEMELIHARAAN' ] );
        }
        elseif ( $request->kode === 'C1' )
        {
            $first_group = FirstGroup::firstOrCreate ( [ 'name' => 'WAREHOUSE' ] );
        }
        elseif ( $request->kode >= 'B11' && $request->kode <= 'B29' )
        {
            $first_group = FirstGroup::firstOrCreate ( [ 'name' => 'PEMELIHARAAN' ] );
            if ( $request->kode >= 'B11' && $request->kode <= 'B16' )
            {
                $second_group = SecondGroup::firstOrCreate ( [ 'name' => 'MAINTENANCE KIT' ] );
            }
            elseif ( $request->kode >= 'B21' && $request->kode <= 'B29' )
            {
                $second_group = SecondGroup::firstOrCreate ( [ 'name' => 'OIL & LUBRICANTS' ] );
            }
        }

        $komponen = $atb->komponen;
        $komponen->update ( [ 
            'kode'            => $request->kode,
            'first_group_id'  => $first_group->id ?? null,
            'second_group_id' => $second_group->id ?? null,
        ] );

        if ( $request->filled ( 'master_data' ) )
        {
            $masterData = MasterData::find ( $request->master_data );
            if ( $masterData )
            {
                $supplier    = $masterData->supplier;
                $sparepart   = $masterData->sparepart;
                $part_number = $masterData->part_number;
            }
        }
        else
        {
            $supplier    = $request->supplier;
            $sparepart   = $request->sparepart;
            $part_number = $request->part_number;
        }

        if ( $request->hasFile ( 'dokumentasi' ) )
        {
            if ( $atb->dokumentasi )
            {
                Storage::disk ( 'private' )->delete ( $atb->dokumentasi );
            }

            $dokumentasiPath = $request->file ( 'dokumentasi' )->store ( 'dokumentasi/atb', 'private' );

            $atb->dokumentasi = $dokumentasiPath;
        }

        $atb->update ( [ 
            'tipe'           => $tipe,
            'tanggal'        => $request->tanggal,
            'supplier'       => $supplier,
            'sparepart'      => $sparepart,
            'part_number'    => $part_number,
            'quantity'       => $quantity,
            'satuan'         => $request->satuan,
            'harga'          => $harga,
            'net'            => $net,
            'ppn'            => $ppn,
            'bruto'          => $bruto,
            'asal_proyek'    => $request->asal_proyek,
            'id_komponen'    => $komponen->id,
            'id_master_data' => $request->master_data,
        ] );

        return back ()->with ( 'success', 'Berhasil mengubah data ATB' );
    }

    public function destroy ( $id )
    {
        $atb = ATB::find ( $id );
        if ( ! $atb )
        {
            return response ()->json ( [ 'message' => 'ATB not found' ], 404 );
        }

        if ( $atb->dokumentasi )
        {
            Storage::disk ( 'private' )->delete ( $atb->dokumentasi );
        }

        $msg = "ATB " . ( $atb->komponen->kode ?? '' ) . " berhasil dihapus";
        $atb->delete ();

        return back ()->with ( 'success', $msg );
    }

    public function import ( Request $request )
    {
        $request->validate ( [ 
            'file' => 'required|mimes:xls,xlsx'
        ] );

        Excel::import ( new ATBImport, $request->file ( 'file' )->store ( 'temp' ) );

        return redirect ()->back ()->with ( 'success', 'Data ATB berhasil diimpor!' );
    }

    public function showImportForm ()
    {
        return view ( 'importForm' );
    }


    public function export ( Request $request )
    {
        $proyekId  = $request->input ( 'proyek' );
        $tipe      = $request->input ( 'tipe' );
        $startDate = $request->input ( 'start_date' );
        $endDate   = $request->input ( 'end_date' );

        $proyek     = Proyek::findOrFail ( $proyekId );
        $namaProyek = $proyek->nama_proyek;

        $fileName = "ATB-$tipe-$namaProyek.xlsx";

        return Excel::download ( new ATBExport( $proyekId, $tipe, $startDate, $endDate ), $fileName );
    }

    public function fetchData ( Request $request )
    {
        $startDate = Carbon::parse ( $request->start_date )->startOfDay ();
        $endDate   = Carbon::parse ( $request->end_date )->endOfDay ();
        $proyekId  = $request->id_proyek;
        $tipe      = $request->tipe;

        $atbList = ATB::with ( 'komponen.first_group', 'komponen.second_group' )
            ->with ( 'proyek' )
            ->where ( 'id_proyek', $proyekId )
            ->whereBetween ( 'tanggal', [ $startDate, $endDate ] )
            ->when ( $tipe, function ($query) use ($tipe)
            {
                $query->where ( 'tipe', $tipe );
            } )
            ->orderBy ( 'tanggal', 'asc' )
            ->get ();

        $atbList->load ( 'masterData' );
        $atbList->load ( 'asalProyek' );

        return response ()->json ( [ 'data' => $atbList ] );
    }

    public function showDokumentasi ( $filename )
    {
        if ( ! auth ()->check () )
        {
            abort ( 403, 'Unauthorized access' );
        }

        $filePath = 'dokumentasi/atb/' . $filename;

        if ( ! Storage::disk ( 'private' )->exists ( $filePath ) )
        {
            abort ( 404, 'File not found' );
        }

        return response ()->file ( Storage::disk ( 'private' )->path ( $filePath ) );
    }

}
