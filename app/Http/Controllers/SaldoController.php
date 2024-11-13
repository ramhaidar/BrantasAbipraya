<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ATB;
use App\Models\User;
use App\Models\Saldo;
use App\Models\Proyek;
use App\Models\Komponen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SaldoExport;

class SaldoController extends Controller
{
    public function ex_panjar_unit_alat ( Request $request )
    {
        return $this->showSaldoPage (
            'Panjar Unit Alat',
            'Data Saldo EX Panjar Unit Alat',
            $request->id_proyek
        );
    }

    public function ex_unit_alat ( Request $request )
    {
        return $this->showSaldoPage (
            'Hutang Unit Alat',
            'Data Saldo EX Unit Alat',
            $request->id_proyek
        );
    }

    public function ex_panjar_proyek ( Request $request )
    {
        return $this->showSaldoPage (
            'Panjar Proyek',
            'Data Saldo EX Panjar Proyek',
            $request->id_proyek
        );
    }

    private function showSaldoPage ( $tipe, $pageTitle, $id_proyek )
    {
        // Dapatkan user yang sedang login
        $user = Auth::user ();

        // Admin bisa mengakses semua proyek, sedangkan pegawai hanya bisa mengakses proyek yang diassign
        if ( $user->role === 'Admin' )
        {
            // Admin dapat mengakses semua proyek
            $proyek  = Proyek::findOrFail ( $id_proyek );
            $proyeks = Proyek::with ( "users" )
                ->orderBy ( "updated_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();
        }
        else
        {
            // Pegawai hanya dapat mengakses proyek yang diassign kepada mereka
            $proyeks = $user->proyek ()
                ->with ( "users" )
                ->orderBy ( "updated_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            // Validasi jika proyek yang diakses adalah proyek yang terkait dengan user
            if ( ! $proyeks->pluck ( 'id' )->contains ( $id_proyek ) )
            {
                abort ( 403, 'Anda tidak memiliki akses ke proyek ini.' );
            }

            $proyek = $proyeks->where ( 'id', $id_proyek )->first ();
        }

        // Menentukan query saldo berdasarkan tipe
        if ( $tipe == 'Hutang Unit Alat' )
        {
            $saldo = Saldo::with ( [ 'atb.komponen', 'apb.alat', 'atb.masterData' ] )
                ->whereHas ( 'atb', function ($query) use ($id_proyek)
                {
                    $query->where ( function ($q) use ($id_proyek)
                    {
                        $q->where ( 'tipe', "Mutasi Proyek" )
                            ->orWhere ( 'tipe', "Hutang Unit Alat" );
                    } )
                        ->where ( 'id_proyek', $id_proyek );
                } )
                ->join ( 'atb', 'saldo.id', '=', 'atb.id_saldo' )
                ->orderBy ( 'atb.tanggal', 'asc' ) // Urutkan dari tanggal tertua (asc)
                ->select ( 'saldo.*' )
                ->get ();
        }
        else
        {
            $saldo = Saldo::with ( [ 'atb.komponen', 'apb.alat', 'atb.masterData' ] )
                ->whereHas ( 'atb', function ($query) use ($tipe, $id_proyek)
                {
                    $query->where ( 'tipe', $tipe )
                        ->where ( 'id_proyek', $id_proyek );
                } )
                ->join ( 'atb', 'saldo.id', '=', 'atb.id_saldo' )
                ->orderBy ( 'atb.tanggal', 'asc' ) // Urutkan dari tanggal tertua (asc)
                ->select ( 'saldo.*' )
                ->get ();
        }

        // Menghitung total NET berdasarkan saldo yang diambil
        $totalNet = 0;
        foreach ( $saldo as $item )
        {
            if ( $item->id_apb === null )
            {
                $totalNet += $item->atb->harga * $item->current_quantity;
            }
        }
        $totalNetFormatted = 'Rp' . number_format ( $totalNet, 0, ',', '.' );

        // Memuat data komponen untuk tampilan
        $komponen = Komponen::with ( [ 'first_group', 'second_group' ] )
            ->get ()
            ->sortBy ( 'second_group.name' )
            ->sortBy ( 'first_group.name' );

        $totalSaldo = 0;
        // Menghitung total saldo
        foreach ( $saldo as $x )
        {
            if ( $x->apb->isEmpty () )
            {
                $totalSaldo += $x->atb->harga * $x->current_quantity;
            }
        }
        $totalSaldoFormatted = 'Rp' . number_format ( $totalSaldo, 0, ',', '.' );

        // Menampilkan data ke view
        return view ( 'dashboard.saldo.saldo', [ 
            'proyek'     => $proyek,
            'proyeks'    => $proyeks,
            'headerPage' => $proyek->nama_proyek,
            'page'       => $pageTitle,
            'saldoList'  => $saldo,
            'komponen'   => $komponen,
            'totalSaldo' => $totalSaldoFormatted,
            'totalNet'   => $totalNetFormatted, // Kirim total net ke view
        ] );
    }


    public function showByID ( $id )
    {
        $atb = ATB::find ( $id );
        $atb->load ( 'komponen' )->load ( 'saldo.apb.alat' );
        $komponen    = Komponen::all ()->whereNotNull ( 'nama_proyek' )->sortBy ( 'SecondGroup.name' )->sortBy ( 'FirstGroup.name' );
        $atbWithUser = User::find ( $atb->created_by );
        $id_komponen = $atb->komponen->id;
        $komponenIds = $komponen->pluck ( 'id' )->toArray ();
        $index       = array_search ( $id_komponen, $komponenIds );
        $kodeMapping = [ 'A1' => 'A1: CABIN', 'A2' => 'A2: ENGINE SYSTEM', 'A3' => 'A3: TRANSMISSION SYSTEM', 'A4' => 'A4: CHASSIS & SWING MACHINERY', 'A5' => 'A5: DIFFERENTIAL SYSTEM', 'A6' => 'A6: ELECTRICAL SYSTEM', 'A7' => 'A7: HYDRAULIC/PNEUMATIC SYSTEM', 'A8' => 'A8: STEERING SYSTEM', 'A9' => 'A9: BRAKE SYSTEM', 'A10' => 'A10: SUSPENSION', 'A11' => 'A11: ATTACHMENT', 'A12' => 'A12: UNDERCARRIAGE', 'A13' => 'A13: FINAL DRIVE', 'A14' => 'A14: FREIGHT COST', 'B11' => 'B11: Oil Filter', 'B12' => 'B12: Fuel Filter', 'B13' => 'B13: Air Filter', 'B14' => 'B14: Hydraulic Filter', 'B15' => 'B15: Transmission Filter', 'B16' => 'B16: Differential Filter', 'B21' => 'B21: Engine Oil', 'B22' => 'B22: Hydraulic Oil', 'B23' => 'B23: Transmission Oil', 'B24' => 'B24: Final Drive Oil', 'B25' => 'B25: Swing & Damper Oil', 'B26' => 'B26: Differential Oil', 'B27' => 'B27: Grease', 'B28' => 'B28: Brake & Power Steering Fluid', 'B29' => 'B29: Coolant', 'B3' => 'B3: Tyre', 'C1' => 'C1: Workshop',];
        if ( $atb->komponen && isset ( $kodeMapping[ $atb->komponen->kode ] ) )
        {
            $atb->komponen->kode = $kodeMapping[ $atb->komponen->kode ];
        }
        $atb->index_komponen = $index;
        $atb->user           = $atbWithUser;
        return response ()->json ( [ 'data' => $atb ] );
    }

    public function fetchData ( Request $request )
    {
        $startDate = Carbon::parse ( $request->start_date )->startOfDay ();
        $endDate   = Carbon::parse ( $request->end_date )->endOfDay ();
        $proyekId  = $request->id_proyek;
        $tipe      = $request->tipe; // Ambil tipe dari request

        $atbList = ATB::with ( 'komponen.first_group', 'komponen.second_group', 'saldo', 'proyek' )
            ->where ( 'id_proyek', $proyekId )
            ->whereBetween ( 'tanggal', [ $startDate, $endDate ] )
            ->when ( $tipe, function ($query) use ($tipe)
            {
                if ( $tipe === 'Hutang Unit Alat' )
                {
                    // Jika tipe adalah "Hutang Unit Alat", sertakan juga tipe "Mutasi Proyek"
                    $query->where ( function ($q)
                    {
                        $q->where ( 'tipe', 'Hutang Unit Alat' )
                            ->orWhere ( 'tipe', 'Mutasi Proyek' );
                    } );
                }
                else
                {
                    // Terapkan filter berdasarkan tipe
                    $query->where ( 'tipe', $tipe );
                }
            } )
            ->orderBy ( 'tanggal', 'asc' ) // Urutkan berdasarkan tanggal tertua
            ->get ();

        $atbList->load ( 'masterData' );

        return response ()->json ( [ 'data' => $atbList ] );
    }

    public function exportSaldo ( Request $request )
    {
        $tipe      = $request->tipe;
        $id_proyek = $request->id_proyek;

        return Excel::download ( new SaldoExport( $tipe, $id_proyek ), 'saldo.xlsx' );
    }
}
