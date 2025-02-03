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
    public function index ( Request $request )
    {
        $proyeks = Proyek::with ( "users" )
            ->orderBy ( "updated_at", "desc" )
            ->orderBy ( "id", "desc" )
            ->get ();

        $user      = Auth::user ();
        $id_proyek = $request->query ( 'id_proyek' );

        // Base queries
        $atbQuery   = ATB::query ();
        $apbQuery   = APB::with ( 'saldo' );
        $saldoQuery = Saldo::query ();

        // Apply project filter if provided
        if ( $id_proyek )
        {
            // Check authorization
            if ( $user->role !== 'Admin' && ! $user->proyek ()->where ( 'proyek.id', $id_proyek )->exists () )
            {
                abort ( 403, 'Unauthorized access to this project' );
            }

            $atbQuery->where ( 'id_proyek', $id_proyek );
            $apbQuery->where ( 'id_proyek', $id_proyek ); // Added APB project filter
            $saldoQuery->whereHas ( 'atb', function ($query) use ($id_proyek)
            {
                $query->where ( 'id_proyek', $id_proyek );
            } );
        }
        elseif ( $user->role !== 'Admin' )
        {
            // If not admin, only show user's projects
            $userProyekIds = $user->proyek ()->pluck ( 'id' )->toArray ();
            $atbQuery->whereIn ( 'id_proyek', $userProyekIds );
            $apbQuery->whereIn ( 'id_proyek', $userProyekIds ); // Added APB projects filter
            $saldoQuery->whereHas ( 'atb', function ($query) use ($userProyekIds)
            {
                $query->whereIn ( 'id_proyek', $userProyekIds );
            } );
        }

        // Calculate ATB total
        $totalATB = $atbQuery->get ()->sum ( function ($atb)
        {
            $total = 0;
            switch ($atb->tipe)
            {
                case 'hutang-unit-alat':
                case 'panjar-unit-alat':
                case 'mutasi-proyek':
                case 'panjar-proyek':
                    $total = $atb->quantity * $atb->harga;
                    break;
            }
            return $total;
        } );

        // Calculate APB total by type, excluding pending/rejected mutations
        $totalAPB = $apbQuery->get ()->sum ( function ($apb)
        {
            $total = 0;
            switch ($apb->tipe)
            {
                case 'hutang-unit-alat':
                case 'panjar-unit-alat':
                case 'panjar-proyek':
                    $total = $apb->quantity * $apb->saldo->harga;
                    break;
                case 'mutasi-proyek':
                    if ( ! in_array ( $apb->status, [ 'pending', 'rejected' ] ) )
                    {
                        $total = $apb->quantity * $apb->saldo->harga;
                    }
                    break;
            }
            return $total;
        } );

        // Calculate Saldo total by type
        $totalSaldo = $saldoQuery->get ()->sum ( function ($saldo)
        {
            $total = 0;
            switch ($saldo->tipe)
            {
                case 'hutang-unit-alat':
                case 'panjar-unit-alat':
                case 'mutasi-proyek':
                case 'panjar-proyek':
                    $total = $saldo->quantity * $saldo->harga;
                    break;
            }
            return $total;
        } );

        return view ( 'dashboard', [ 
            'headerPage'      => 'Dashboard Utama',
            'page'            => 'Dashboard',
            'proyeks'         => $proyeks,
            'selectedProject' => $id_proyek,
            'totalATB'        => $totalATB,
            'totalAPB'        => $totalAPB,
            'totalSaldo'      => $totalSaldo,
        ] );
    }
}