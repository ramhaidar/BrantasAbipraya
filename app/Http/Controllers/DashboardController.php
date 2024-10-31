<?php
namespace App\Http\Controllers;
use App\Models\APB;
use App\Models\ATB;
use App\Models\Alat;
use App\Models\User;
use App\Models\Saldo;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class DashboardController extends Controller
{
    public function index ()
    {
        $user = Auth::user ();
        if ( $user->role === 'Admin' )
        {
            $proyeks                = Proyek::with ( "users" )->orderByDesc ( "updated_at" )->get ();
            $totalHargaBarangMasuk  = ATB::get ()->sum ( function ($atb)
            {
                return $atb->net * $atb->quantity;
            } );
            $totalHargaBarangKeluar = APB::with ( 'saldo' )->get ()->sum ( function ($apb)
            {
                return $apb->saldo->net * $apb->quantity;
            } );
            $totalSemuaUser         = User::whereIn ( 'role', [ 'Pegawai', 'Boss' ] )->count ();
            $atbsHabis              = ATB::with ( 'saldo.apb', 'proyek', 'komponen' )->get ()->map ( function ($atb)
            {
                $totalQuantityAPB        = $atb->saldo->apb->sum ( 'quantity' );
                $atb->remaining_quantity = $atb->quantity - $totalQuantityAPB;
                return $atb;
            } )->filter ( function ($atb)
            {
                return $atb->remaining_quantity <= 10;
            } );
            $totalHargaSemuaBarang = Saldo::get ()->sum ( function ($saldo)
            {
                return $saldo->net * $saldo->current_quantity;
            } );
            $totalSemuaAlat        = Alat::count ();
        }
        elseif ( $user->role === 'Boss' )
        {
            $proyeks               = $user->proyek ()->with ( "users" )->get ();
            $totalProyek           = $proyeks->count ();
            $proyekIds             = $proyeks->pluck ( 'id' )->toArray ();
            $totalHargaBarangMasuk = ATB::whereIn ( 'id_proyek', $proyekIds )->get ()->sum ( function ($atb)
            {
                return ( $atb->harga ?? 0 ) * ( $atb->quantity ?? 0 );
            } );

            $saldoIds               = ATB::whereIn ( 'id_proyek', $proyekIds )->pluck ( 'id_saldo' )->toArray ();
            $totalHargaBarangKeluar = APB::whereIn ( 'id_saldo', $saldoIds )->with ( 'saldo' )->get ()->sum ( function ($apb)
            {
                return $apb->saldo->net * $apb->quantity;
            } );
            $atbsHabis              = ATB::whereIn ( 'id_proyek', $proyekIds )->with ( 'saldo.apb', 'proyek', 'komponen' )->get ()->map ( function ($atb)
            {
                $totalQuantityAPB        = $atb->saldo->apb->sum ( 'quantity' );
                $atb->remaining_quantity = $atb->quantity - $totalQuantityAPB;
                return $atb;
            } )->filter ( function ($atb)
            {
                return $atb->remaining_quantity <= 10;
            } );
            $totalHargaSemuaBarang = Saldo::whereIn ( 'id', $saldoIds )->get ()->sum ( function ($saldo)
            {
                return $saldo->net * $saldo->current_quantity;
            } );
            $totalSemuaAlat        = Alat::whereIn ( 'id_user', function ($query) use ($proyekIds)
            {
                $query->select ( 'id_user' )->from ( 'user_proyek' )->whereIn ( 'id_proyek', $proyekIds );
            } )->count ();
        }
        elseif ( $user->role === 'Pegawai' )
        {
            $proyeks                = $user->proyek ()->with ( "users" )->orderBy ( "created_at", "asc" )->orderBy ( "id", "asc" )->get ();
            $proyekIds              = $proyeks->pluck ( 'id' )->toArray ();
            $totalHargaBarangMasuk  = ATB::whereIn ( 'id_proyek', $proyekIds )->get ()->sum ( function ($atb)
            {
                return $atb->net * $atb->quantity;
            } );
            $saldoIds               = ATB::whereIn ( 'id_proyek', $proyekIds )->pluck ( 'id_saldo' )->toArray ();
            $totalHargaBarangKeluar = APB::whereIn ( 'id_saldo', $saldoIds )->with ( 'saldo' )->get ()->sum ( function ($apb)
            {
                return $apb->saldo->net * $apb->quantity;
            } );
            $atbsHabis              = ATB::with ( [ 'saldo.apb', 'proyek', 'komponen', 'masterData' ] )->get ()->map ( function ($atb)
            {
                $totalQuantityAPB        = $atb->saldo->apb->sum ( 'quantity' );
                $atb->remaining_quantity = $atb->quantity - $totalQuantityAPB;
                return $atb;
            } )->filter ( function ($atb)
            {
                return $atb->masterData && $atb->remaining_quantity < $atb->masterData->buffer_stock;
            } );
            $totalHargaSemuaBarang = Saldo::whereIn ( 'id', $saldoIds )->get ()->sum ( function ($saldo)
            {
                return $saldo->net * $saldo->current_quantity;
            } );
            $totalSemuaAlat        = Alat::where ( 'id_user', $user->id )->count ();
        }
        return view ( 'home', [ 'page' => 'Dashboard', 'proyeks' => $proyeks, 'totalHargaBarangMasuk' => $totalHargaBarangMasuk, 'totalHargaBarangKeluar' => $totalHargaBarangKeluar, 'totalHargaSemuaBarang' => $totalHargaSemuaBarang, 'totalSemuaUser' => $totalSemuaUser ?? 0, 'atbsHabis' => $atbsHabis, 'totalSemuaAlat' => $totalSemuaAlat, 'totalProyek' => $totalProyek ?? 0,] );
    }

    public function filterByProyek ( $id )
    {
        $user      = Auth::user ();
        $projectId = $id;
        if ( $projectId === 'all' )
        {
            if ( $user->role === 'Admin' )
            {
                $totalHargaBarangMasuk  = ATB::get ()->sum ( function ($atb)
                {
                    return $atb->net * $atb->quantity;
                } );
                $totalHargaBarangKeluar = APB::with ( 'saldo' )->get ()->sum ( function ($apb)
                {
                    return $apb->saldo->net * $apb->quantity;
                } );

                // Memperbaiki perhitungan total user dengan role Pegawai dan Boss saja
                $totalSemuaUser = User::whereIn ( 'role', [ 'Pegawai', 'Boss' ] )->count ();

                $atbsHabis = ATB::with ( 'saldo.apb', 'proyek', 'komponen', 'masterData' )->get ()->map ( function ($atb)
                {
                    $totalQuantityAPB        = $atb->saldo->apb->sum ( 'quantity' );
                    $atb->remaining_quantity = $atb->quantity - $totalQuantityAPB;
                    return $atb;
                } )->filter ( function ($atb)
                {
                    return $atb->remaining_quantity <= 10;
                } )->values ()->toArray ();

                $totalHargaSemuaBarang = Saldo::get ()->sum ( function ($saldo)
                {
                    return $saldo->net * $saldo->current_quantity;
                } );
                $totalSemuaAlat        = Alat::count ();
                $totalProyek           = Proyek::count ();
            }
            elseif ( $user->role === 'Boss' )
            {
                $proyeks                = $user->proyek ()->with ( "users" )->get ();
                $proyekIds              = $proyeks->pluck ( 'id' )->toArray ();
                $totalProyek            = $proyeks->count ();
                $totalHargaBarangMasuk  = ATB::whereIn ( 'id_proyek', $proyekIds )->get ()->sum ( function ($atb)
                {
                    return $atb->net * $atb->quantity;
                } );
                $saldoIds               = ATB::whereIn ( 'id_proyek', $proyekIds )->pluck ( 'id_saldo' )->toArray ();
                $totalHargaBarangKeluar = APB::whereIn ( 'id_saldo', $saldoIds )->with ( 'saldo' )->get ()->sum ( function ($apb)
                {
                    return $apb->saldo->net * $apb->quantity;
                } );
                $atbsHabis              = ATB::whereIn ( 'id_proyek', $proyekIds )->with ( 'saldo.apb', 'proyek', 'komponen', 'masterData' )->get ()->map ( function ($atb)
                {
                    $totalQuantityAPB        = $atb->saldo->apb->sum ( 'quantity' );
                    $atb->remaining_quantity = $atb->quantity - $totalQuantityAPB;
                    return $atb;
                } )->filter ( function ($atb)
                {
                    return $atb->remaining_quantity <= 10;
                } )->values ()->toArray ();
                $totalHargaSemuaBarang = Saldo::whereIn ( 'id', $saldoIds )->get ()->sum ( function ($saldo)
                {
                    return $saldo->net * $saldo->current_quantity;
                } );
                $totalSemuaAlat        = Alat::whereIn ( 'id_user', function ($query) use ($proyekIds)
                {
                    $query->select ( 'id_user' )->from ( 'user_proyek' )->whereIn ( 'id_proyek', $proyekIds );
                } )->count ();
            }
            elseif ( $user->role === 'Pegawai' )
            {
                $proyeks                = $user->proyek ()->with ( "users" )->get ();
                $proyekIds              = $proyeks->pluck ( 'id' )->toArray ();
                $totalHargaBarangMasuk  = ATB::whereIn ( 'id_proyek', $proyekIds )->get ()->sum ( function ($atb)
                {
                    return $atb->net * $atb->quantity;
                } );
                $saldoIds               = ATB::whereIn ( 'id_proyek', $proyekIds )->pluck ( 'id_saldo' )->toArray ();
                $totalHargaBarangKeluar = APB::whereIn ( 'id_saldo', $saldoIds )->with ( 'saldo' )->get ()->sum ( function ($apb)
                {
                    return $apb->saldo->net * $apb->quantity;
                } );
                $atbsHabis              = ATB::whereIn ( 'id_proyek', $proyekIds )->with ( 'saldo.apb', 'proyek', 'komponen', 'masterData' )->get ()->map ( function ($atb)
                {
                    $totalQuantityAPB        = $atb->saldo->apb->sum ( 'quantity' );
                    $atb->remaining_quantity = $atb->quantity - $totalQuantityAPB;
                    return $atb;
                } )->filter ( function ($atb)
                {
                    return $atb->masterData && $atb->remaining_quantity < $atb->masterData->buffer_stock;
                } )->values ()->toArray ();
                $totalHargaSemuaBarang = Saldo::whereIn ( 'id', $saldoIds )->get ()->sum ( function ($saldo)
                {
                    return $saldo->net * $saldo->current_quantity;
                } );
                $totalSemuaAlat        = Alat::where ( 'id_user', $user->id )->count ();
            }
        }
        else
        {
            if ( $user->role === 'Admin' )
            {
                $totalHargaBarangMasuk  = ATB::where ( 'id_proyek', $projectId )->get ()->sum ( function ($atb)
                {
                    return $atb->net * $atb->quantity;
                } );
                $saldoIds               = ATB::where ( 'id_proyek', $projectId )->pluck ( 'id_saldo' )->toArray ();
                $totalHargaBarangKeluar = APB::whereIn ( 'id_saldo', $saldoIds )->with ( 'saldo' )->get ()->sum ( function ($apb)
                {
                    return $apb->saldo->net * $apb->quantity;
                } );
                $totalHargaSemuaBarang  = Saldo::whereIn ( 'id', $saldoIds )->get ()->sum ( function ($saldo)
                {
                    return $saldo->net * $saldo->current_quantity;
                } );
                $atbsHabis              = ATB::where ( 'id_proyek', $projectId )->with ( 'saldo.apb', 'proyek', 'komponen', 'masterData' )->get ()->map ( function ($atb)
                {
                    $totalQuantityAPB        = $atb->saldo->apb->sum ( 'quantity' );
                    $atb->remaining_quantity = $atb->quantity - $totalQuantityAPB;
                    return $atb;
                } )->filter ( function ($atb)
                {
                    return $atb->remaining_quantity <= 10;
                } )->values ()->toArray ();
                $totalSemuaAlat = Alat::where ( 'id_proyek', $projectId )->count ();
                $totalSemuaUser = User::whereHas ( 'proyek', function ($query) use ($projectId)
                {
                    $query->where ( 'proyek.id', $projectId );
                } )->count ();
                $totalProyek    = Proyek::where ( 'proyek.id', $projectId )->count ();
            }
            elseif ( $user->role === 'Boss' )
            {
                $proyeks   = $user->proyek ()->where ( 'proyek.id', $projectId )->with ( "users" )->get ();
                $proyekIds = $proyeks->pluck ( 'id' )->toArray ();
                if ( $proyeks->isEmpty () )
                {
                    return response ()->json ( [ 'error' => 'Anda tidak memiliki akses ke proyek ini' ], 403 );
                }
                $totalHargaBarangMasuk  = ATB::where ( 'id_proyek', $projectId )->get ()->sum ( function ($atb)
                {
                    return $atb->net * $atb->quantity;
                } );
                $saldoIds               = ATB::where ( 'id_proyek', $projectId )->pluck ( 'id_saldo' )->toArray ();
                $totalHargaBarangKeluar = APB::whereIn ( 'id_saldo', $saldoIds )->with ( 'saldo' )->get ()->sum ( function ($apb)
                {
                    return $apb->saldo->net * $apb->quantity;
                } );
                $atbsHabis              = ATB::where ( 'id_proyek', $projectId )->with ( 'saldo.apb', 'proyek', 'komponen', 'masterData' )->get ()->map ( function ($atb)
                {
                    $totalQuantityAPB        = $atb->saldo->apb->sum ( 'quantity' );
                    $atb->remaining_quantity = $atb->quantity - $totalQuantityAPB;
                    return $atb;
                } )->filter ( function ($atb)
                {
                    return $atb->remaining_quantity <= 10;
                } )->values ()->toArray ();
                $totalHargaSemuaBarang = Saldo::whereIn ( 'id', $saldoIds )->get ()->sum ( function ($saldo)
                {
                    return $saldo->net * $saldo->current_quantity;
                } );
                $totalSemuaAlat        = Alat::whereIn ( 'id_user', function ($query) use ($proyekIds)
                {
                    $query->select ( 'id_user' )->from ( 'user_proyek' )->whereIn ( 'id_proyek', $proyekIds );
                } )->count ();
                $totalSemuaUser        = User::whereHas ( 'proyek', function ($query) use ($projectId)
                {
                    $query->where ( 'proyek.id', $projectId );
                } )->count ();
                $totalProyek           = Proyek::where ( 'proyek.id', $projectId )->count ();
            }
            elseif ( $user->role === 'Pegawai' )
            {
                $proyeks   = $user->proyek ()->where ( 'proyek.id', $projectId )->with ( "users" )->get ();
                $proyekIds = $proyeks->pluck ( 'id' )->toArray ();
                if ( $proyeks->isEmpty () )
                {
                    return response ()->json ( [ 'error' => 'Anda tidak memiliki akses ke proyek ini' ], 403 );
                }
                $totalHargaBarangMasuk  = ATB::where ( 'id_proyek', $projectId )->get ()->sum ( function ($atb)
                {
                    return $atb->net * $atb->quantity;
                } );
                $saldoIds               = ATB::where ( 'id_proyek', $projectId )->pluck ( 'id_saldo' )->toArray ();
                $totalHargaBarangKeluar = APB::whereIn ( 'id_saldo', $saldoIds )->with ( 'saldo' )->get ()->sum ( function ($apb)
                {
                    return $apb->saldo->net * $apb->quantity;
                } );
                $atbsHabis              = ATB::where ( 'id_proyek', $projectId )->with ( 'saldo.apb', 'proyek', 'komponen', 'masterData' )->get ()->map ( function ($atb)
                {
                    $totalQuantityAPB        = $atb->saldo->apb->sum ( 'quantity' );
                    $atb->remaining_quantity = $atb->quantity - $totalQuantityAPB;
                    return $atb;
                } )->filter ( function ($atb)
                {
                    return $atb->masterData && $atb->remaining_quantity < $atb->masterData->buffer_stock;
                } )->values ()->toArray ();
                $totalHargaSemuaBarang = Saldo::whereIn ( 'id', $saldoIds )->get ()->sum ( function ($saldo)
                {
                    return $saldo->net * $saldo->current_quantity;
                } );
                $totalSemuaAlat        = Alat::whereIn ( 'id_user', function ($query) use ($proyekIds)
                {
                    $query->select ( 'id_user' )->from ( 'user_proyek' )->whereIn ( 'id_proyek', $proyekIds );
                } )->count ();
                $totalSemuaUser        = User::whereHas ( 'proyek', function ($query) use ($projectId)
                {
                    $query->where ( 'proyek.id', $projectId );
                } )->count ();
                $totalProyek           = Proyek::where ( 'proyek.id', $projectId )->count ();
            }
        }
        return response ()->json ( [ 'totalHargaBarangMasuk' => number_format ( $totalHargaBarangMasuk, 0, ',', '.' ), 'totalHargaBarangKeluar' => number_format ( $totalHargaBarangKeluar, 0, ',', '.' ), 'totalHargaSemuaBarang' => number_format ( $totalHargaSemuaBarang, 0, ',', '.' ), 'totalSemuaUser' => $totalSemuaUser ?? 0, 'totalProyek' => $totalProyek ?? 0, 'totalSemuaAlat' => $totalSemuaAlat, 'atbsHabis' => $atbsHabis ?? [] ] );
    }
}