<?php

namespace App\Http\Controllers;

use App\Models\APB;
use App\Models\Saldo;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function summary ( Request $request )
    {
        // Dapatkan user yang sedang login
        $user = Auth::user ();

        // Admin bisa melihat semua proyek, sedangkan pegawai hanya bisa melihat proyek yang diassign
        if ( $user->role === 'Admin' )
        {
            // Admin dapat mengakses semua proyek
            $proyek  = Proyek::findOrFail ( $request->id_proyek );
            $proyeks = Proyek::with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();
        }
        else
        {
            // Pegawai hanya dapat mengakses proyek yang diassign kepada mereka
            $proyeks = $user->proyek ()
                ->with ( "users" )
                ->orderBy ( "created_at", "asc" )
                ->orderBy ( "id", "asc" )
                ->get ();

            // Validasi jika proyek yang diakses adalah proyek yang terkait dengan user
            if ( ! $proyeks->pluck ( 'id' )->contains ( $request->id_proyek ) )
            {
                abort ( 403, 'Anda tidak memiliki akses ke proyek ini.' );
            }

            $proyek = $proyeks->where ( 'id', $request->id_proyek )->first ();
        }

        // Lanjutkan dengan kode selanjutnya...

        $atbs               = $proyek->atbs ()->with ( 'saldo', 'komponen' )->get ();
        $dataPerKategoriATB = $atbs->groupBy ( 'id_komponen' );

        $detailDataATB = [];
        foreach ( $dataPerKategoriATB as $ATB_ID => $atbGroup )
        {
            $komponenName = $atbGroup->first ()->komponen->kode ?? 'Unknown';
            $totalNet     = $atbGroup->sum ( 'net' );

            $detailDataATB[] = [ 
                'id_atb'      => $ATB_ID,
                'kode'        => $komponenName,
                'total_net'   => $totalNet,
                'tipe'        => $atbGroup->first ()->tipe,
                'suku_cadang' => $atbGroup->first ()->komponen->first_group->name,
                'sumber'      => $atbGroup->first ()->komponen->second_group->name ?? null,
            ];
        }

        $apbs = APB::whereIn ( 'id_saldo', function ($query) use ($proyek)
        {
            $query->select ( 'id_saldo' )
                ->from ( 'atb' )
                ->where ( 'id_proyek', $proyek->id );
        } )->with ( 'alat', 'saldo.atb' )->get ();

        $dataPerKategoriAPB = $apbs->groupBy ( 'id_alat' );

        $detailDataAPB = [];
        foreach ( $dataPerKategoriAPB as $APB_ID => $apbGroup )
        {
            $alatName      = $apbGroup->first ()->alat->kode_alat ?? 'Unknown';
            $totalQuantity = $apbGroup->sum ( 'quantity' );
            $totalNet      = $apbGroup->sum ( function ($apb)
            {
                return $apb->quantity * $apb->saldo->atb->harga;
            } );

            $detailDataAPB[] = [ 
                'id_apb'         => $APB_ID,
                'kode_alat'      => $alatName,
                'kode'           => $apbGroup->first ()->saldo->atb->komponen->kode ?? 'Unknown',
                'total_quantity' => $totalQuantity,
                'tipe'           => $apbGroup->first ()->saldo->atb->tipe,
                // 'jenis_alat'     => $apbGroup->first ()->alat->jenis_alat,
                'suku_cadang'    => $apbGroup->first ()->saldo->atb->komponen->first_group->name ?? null,
                'total_net'      => $totalNet,
                'sumber'         => $apbGroup->first ()->saldo->atb->komponen->second_group->name ?? null,
            ];
        }

        $saldos = Saldo::whereIn ( 'id', function ($query) use ($proyek)
        {
            $query->select ( 'id_saldo' )
                ->from ( 'atb' )
                ->where ( 'id_proyek', $proyek->id );
        } )->with ( 'atb.komponen' )->get ();

        $dataPerKategoriSaldo = $saldos->groupBy ( 'atb.id_komponen' );

        $detailDataSaldo = [];
        foreach ( $dataPerKategoriSaldo as $komponenID => $saldoGroup )
        {
            $komponenName = $saldoGroup->first ()->atb->komponen->kode ?? 'Unknown';
            $totalNet     = $saldoGroup->sum ( 'net' );

            $detailDataSaldo[] = [ 
                'id_komponen' => $komponenID,
                'kode'        => $komponenName,
                'total_net'   => $totalNet,
                'tipe'        => $saldoGroup->first ()->atb->tipe,
                'suku_cadang' => $saldoGroup->first ()->atb->komponen->first_group->name ?? null,
                'sumber'      => $saldoGroup->first ()->atb->komponen->second_group->name ?? null,
            ];
        }

        $totalPenerimaan  = $atbs->sum ( 'net' );
        $totalPengeluaran = $atbs->sum ( 'saldo.net' );
        // $totalSaldo       = $totalPengeluaran - $totalPenerimaan;

        return view ( "dashboard.laporan.summary", [ 
            "headerPage"       => "Laporan",
            "page"             => "Laporan Summary",
            "proyek"           => $proyek,
            "proyeks"          => $proyeks,
            "totalPenerimaan"  => $totalPenerimaan,
            "totalPengeluaran" => $totalPengeluaran,
            // "totalSaldo"       => $totalSaldo,
            "detailDataATB"    => $detailDataATB,
            "detailDataAPB"    => $detailDataAPB,
            "detailDataSaldo"  => $detailDataSaldo,
        ] );
    }

    public function fetchData ( Request $request )
    {
        // Implementasikan perubahan yang sama seperti pada fungsi summary
        $user = Auth::user ();

        // Admin bisa mengakses semua proyek, sedangkan pegawai hanya proyek yang di-assign
        if ( $user->role === 'Admin' )
        {
            $proyek  = Proyek::findOrFail ( $request->id_proyek );
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

            if ( ! $proyeks->pluck ( 'id' )->contains ( $request->id_proyek ) )
            {
                abort ( 403, 'Anda tidak memiliki akses ke proyek ini.' );
            }

            $proyek = $proyeks->where ( 'id', $request->id_proyek )->first ();
        }

        $startDate = $request->input ( 'start_date' );
        $endDate   = $request->input ( 'end_date' );

        // Filter ATB data based on the date range and project ID
        $atbs = $proyek->atbs ()
            ->with ( 'saldo', 'komponen' )
            ->whereBetween ( 'tanggal', [ $startDate, $endDate ] )
            ->get ();

        // Group ATB data by component ID
        $dataPerKategoriATB = $atbs->groupBy ( 'id_komponen' );

        // Prepare detailed ATB data
        $detailDataATB = [];
        foreach ( $dataPerKategoriATB as $ATB_ID => $atbGroup )
        {
            $komponenName = $atbGroup->first ()->komponen->kode ?? 'Unknown';
            $totalNet     = $atbGroup->sum ( 'net' );

            $detailDataATB[] = [ 
                'id_atb'      => $ATB_ID,
                'kode'        => $komponenName,
                'total_net'   => $totalNet,
                'tipe'        => $atbGroup->first ()->tipe,
                'suku_cadang' => $atbGroup->first ()->komponen->first_group->name,
                'sumber'      => $atbGroup->first ()->komponen->second_group->name ?? null,
            ];
        }

        // Filter APB data based on the date range and project ID
        $apbs = APB::whereIn ( 'id_saldo', function ($query) use ($proyek)
        {
            $query->select ( 'id_saldo' )
                ->from ( 'atb' )
                ->where ( 'id_proyek', $proyek->id );
        } )->with ( 'alat', 'saldo.atb' )
            ->whereBetween ( 'tanggal', [ $startDate, $endDate ] )
            ->get ();

        // Group APB data by equipment ID
        $dataPerKategoriAPB = $apbs->groupBy ( 'id_alat' );

        // Prepare detailed APB data
        $detailDataAPB = [];
        foreach ( $dataPerKategoriAPB as $APB_ID => $apbGroup )
        {
            $alatName      = $apbGroup->first ()->alat->kode_alat ?? 'Unknown';
            $totalQuantity = $apbGroup->sum ( 'quantity' );
            $totalNet      = $apbGroup->sum ( function ($apb)
            {
                return $apb->quantity * $apb->saldo->atb->harga;
            } );

            $detailDataAPB[] = [ 
                'id_apb'         => $APB_ID,
                'kode_alat'      => $alatName,
                'kode'           => $apbGroup->first ()->saldo->atb->komponen->kode ?? 'Unknown',
                'total_quantity' => $totalQuantity,
                'tipe'           => $apbGroup->first ()->saldo->atb->tipe,
                // 'jenis_alat'     => $apbGroup->first ()->alat->jenis_alat,
                'suku_cadang'    => $apbGroup->first ()->saldo->atb->komponen->first_group->name ?? null,
                'total_net'      => $totalNet,
                'sumber'         => $apbGroup->first ()->saldo->atb->komponen->second_group->name ?? null,
            ];
        }

        // Filter Saldo data based on the date range and project ID
        // Filter Saldo data based on the date range and project ID
        $saldos = Saldo::whereIn ( 'id', function ($query) use ($proyek)
        {
            $query->select ( 'id_saldo' )
                ->from ( 'atb' )
                ->where ( 'id_proyek', $proyek->id );
        } )->whereHas ( 'atb', function ($query) use ($startDate, $endDate)
        {
            // Filter berdasarkan 'tanggal' dari ATB
            $query->whereBetween ( 'tanggal', [ $startDate, $endDate ] );
        } )
            ->with ( 'atb.komponen' )
            ->get ();

        // Group Saldo data by component ID
        $dataPerKategoriSaldo = $saldos->groupBy ( 'atb.id_komponen' );

        // Prepare detailed Saldo data
        $detailDataSaldo = [];
        foreach ( $dataPerKategoriSaldo as $komponenID => $saldoGroup )
        {
            $komponenName = $saldoGroup->first ()->atb->komponen->kode ?? 'Unknown';
            $totalNet     = $saldoGroup->sum ( 'net' );

            $detailDataSaldo[] = [ 
                'id_komponen' => $komponenID,
                'kode'        => $komponenName,
                'total_net'   => $totalNet,
                'tipe'        => $saldoGroup->first ()->atb->tipe,
                'suku_cadang' => $saldoGroup->first ()->atb->komponen->first_group->name ?? null,
                'sumber'      => $saldoGroup->first ()->atb->komponen->second_group->name ?? null,
            ];
        }

        // Calculate total penerimaan and pengeluaran
        $totalPenerimaan  = $atbs->sum ( 'net' );
        $totalPengeluaran = $atbs->sum ( 'saldo.net' );

        // Return the view with the same variables as in the summary function
        return view ( 'dashboard.laporan.partials.summary_table', [ 
            "page"             => "Summary Laporan",
            "proyek"           => $proyek,
            "proyeks"          => $proyeks,
            "totalPenerimaan"  => $totalPenerimaan,
            "totalPengeluaran" => $totalPengeluaran,
            "detailDataATB"    => $detailDataATB,
            "detailDataAPB"    => $detailDataAPB,
            "detailDataSaldo"  => $detailDataSaldo,
        ] );
    }


    public function LNPB ( Request $request )
    {
        $user = Auth::user ();

        if ( $user->role === 'Admin' )
        {
            $proyek  = Proyek::findOrFail ( $request->id_proyek );
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

            if ( ! $proyeks->pluck ( 'id' )->contains ( $request->id_proyek ) )
            {
                abort ( 403, 'Anda tidak memiliki akses ke proyek ini.' );
            }

            $proyek = $proyeks->where ( 'id', $request->id_proyek )->first ();
        }

        return view ( "dashboard.laporan.lnpb", [ 
            "page"    => "Data LNPB",
            "proyek"  => $proyek,
            "proyeks" => $proyeks,
        ] );
    }
}
