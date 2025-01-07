<?php
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APBController;
use App\Http\Controllers\ATBController;
use App\Http\Controllers\SPBController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SaldoController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetailSPBController;
use App\Http\Controllers\RKBUrgentController;
use App\Http\Controllers\SPBProyekController;
use App\Http\Controllers\AlatProyekController;
use App\Http\Controllers\RiwayatSPBController;
use App\Http\Controllers\RKBGeneralController;
use App\Http\Controllers\MasterDataAlatController;
use App\Http\Controllers\DetailRKBUrgentController;
use App\Http\Controllers\DetailSPBProyekController;
use App\Http\Controllers\DetailRKBGeneralController;
use App\Http\Controllers\EvaluasiRKBUrgentController;
use App\Http\Controllers\LampiranRKBUrgentController;
use App\Http\Controllers\TimelineRKBUrgentController;
use App\Http\Controllers\EvaluasiRKBGeneralController;
use App\Http\Controllers\MasterDataSupplierController;
use App\Http\Controllers\MasterDataSparepartController;
use App\Http\Controllers\LampiranEvaluasiUrgentController;
use App\Http\Controllers\TimelineEvaluasiUrgentController;
use App\Http\Controllers\EvaluasiDetailRKBUrgentController;
use App\Http\Controllers\EvaluasiDetailRKBGeneralController;

Route::get ( '/test', function ()
{
    return view ( 'test' );
} );

// Rute untuk Halaman Landing (HomePage)
Route::get ( '/', function ()
{
    return view ( '/prelogin' );
} )
    ->middleware ( 'guest' );

// Rute Login [SessionController]
Route::middleware ( 'guest' )->group ( function ()
{
    Route::get ( '/login', [ SessionController::class, 'index' ] )
        ->name ( 'login' );
    Route::post ( '/login', [ SessionController::class, 'login' ] )->
        name ( 'login.post' );
} );

// Rute Logout [SessionController]
Route::middleware ( [ CheckRole::class . ':Admin,Pegawai,Boss' ] )->group ( function ()
{
    Route::post ( '/logout', [ SessionController::class, 'logout' ] )
        ->name ( 'logout.post' );

    Route::get ( '/logout', [ SessionController::class, 'logout' ] )
        ->name ( 'logout.post' );
} );

// Rute Dashboard [DashboardController]
Route::get ( '/dashboard', [ DashboardController::class, 'index' ] )
    ->middleware ( [ 
        CheckRole::class . ':Admin,Pegawai,Boss',
    ] )
    ->name ( 'dashboard' );

// Rute Users [UserController]
Route::prefix ( 'users' )->middleware ( [ CheckRole::class . ':Admin,Pegawai,Boss' ] )->group ( function ()
{
    Route::get ( '/', [ UserController::class, 'index' ] )
        ->name ( 'users' );
    Route::get ( '/{user}', [ UserController::class, 'showByID' ] )
        ->where ( 'user', '[0-9]+' )
        ->name ( 'api.showUserByID' );
    Route::post ( '/edit/{user}', [ UserController::class, 'update' ] )
        ->where ( 'user', '[0-9]+' )
        ->name ( 'user.post.update' );
    Route::post ( '/add', [ UserController::class, 'store' ] )
        ->name ( 'user.post.store' );
    Route::delete ( '/delete/{user}', [ UserController::class, 'destroy' ] )
        ->where ( 'user', '[0-9]+' )
        ->name ( 'user.delete.destroy' );
} );

// Rute ATB [ATBController]
Route::middleware ( [ CheckRole::class . ':Admin,Pegawai,Boss' ] )
    ->prefix ( 'atb' )
    ->as ( 'atb.' )
    ->group ( function ()
    {

        Route::get ( '/', [ ATBController::class, 'index' ] )
            ->name ( 'atb' );

        Route::get ( '/getlinkSpbDetailSpbs/{id}', [ ATBController::class, 'getlinkSpbDetailSpbs' ] )
            ->name ( 'getlinkSpbDetailSpbs' );

        // Hutang Unit Alat Routes
        Route::prefix ( 'hutang_unit_alat' )
            ->group ( function ()
        {
            Route::get ( '/', [ ATBController::class, 'hutang_unit_alat' ] )
                ->name ( 'hutang_unit_alat' );

            Route::get ( '/add', [ ATBController::class, 'data_baru_hutang_unit_alat' ] )
                ->name ( 'new.hutang_unit_alat' );

            Route::get ( '/{atb}', [ ATBController::class, 'showByID' ] )
                ->name ( 'show.hutang_unit_alat_by_id' );

            Route::post ( '/edit/actions/{id}', [ ATBController::class, 'update' ] )
                ->name ( 'post.update.hutang_unit_alat' );

            Route::delete ( '/actions/{id}', [ ATBController::class, 'destroy' ] )
                ->name ( 'delete.destroy.hutang_unit_alat' );
        } );

        // Panjar Unit Alat Routes
        Route::prefix ( 'panjar_unit_alat' )->group ( function ()
        {
            Route::get ( '/', [ ATBController::class, 'panjar_unit_alat' ] )
                ->name ( 'panjar_unit_alat' );

            Route::get ( '/add', [ ATBController::class, 'data_baru_panjar_unit_alat' ] )
                ->name ( 'new.panjar_unit_alat' );

            Route::get ( '/{atb}', [ ATBController::class, 'showByID' ] )
                ->name ( 'show.panjar_unit_alat_by_id' );

            Route::post ( '/edit/actions/{id}', [ ATBController::class, 'update' ] )
                ->name ( 'post.update.panjar_unit_alat' );

            Route::delete ( '/actions/{id}', [ ATBController::class, 'destroy' ] )
                ->name ( 'delete.destroy.panjar_unit_alat' );
        } );

        // Mutasi Proyek Routes
        Route::prefix ( 'mutasi_proyek' )->group ( function ()
        {
            Route::get ( '/', [ ATBController::class, 'mutasi_proyek' ] )
                ->name ( 'mutasi_proyek' );

            Route::get ( '/add', [ ATBController::class, 'data_baru_mutasi_proyek' ] )
                ->name ( 'new.mutasi_proyek' );

            Route::get ( '/{atb}', [ ATBController::class, 'showByID' ] )
                ->name ( 'show.mutasi_proyek_by_id' );

            Route::post ( '/edit/actions/{id}', [ ATBController::class, 'update' ] )
                ->name ( 'post.update.mutasi_proyek' );

            Route::delete ( '/actions/{id}', [ ATBController::class, 'destroy' ] )
                ->name ( 'delete.destroy.mutasi_proyek' );
        } );

        // Panjar Proyek Routes
        Route::prefix ( 'panjar_proyek' )->group ( function ()
        {
            Route::get ( '/', [ ATBController::class, 'panjar_proyek' ] )
                ->name ( 'panjar_proyek' );

            Route::get ( '/add', [ ATBController::class, 'data_baru_panjar_proyek' ] )
                ->name ( 'new.panjar_proyek' );

            Route::get ( '/{atb}', [ ATBController::class, 'showByID' ] )
                ->name ( 'show.panjar_proyek_by_id' );

            Route::post ( '/edit/actions/{id}', [ ATBController::class, 'update' ] )
                ->name ( 'post.update.panjar_proyek' );

            Route::delete ( '/actions/{id}', [ ATBController::class, 'destroy' ] )
                ->name ( 'delete.destroy.panjar_proyek' );
        } );

        // General ATB Routes
        Route::get ( '/add', [ ATBController::class, 'data_baru_atb' ] )
            ->name ( 'new' );

        Route::post ( '/store', [ ATBController::class, 'store' ] )
            ->name ( 'post.store' );

        Route::post ( '/actions/{id}', [ ATBController::class, 'update' ] )
            ->name ( 'post.update' );

        Route::delete ( '/actions/{id}', [ ATBController::class, 'destroy' ] )
            ->name ( 'destroy' );

        Route::get ( '/delatb/actions/{id}', [ ATBController::class, 'destroy' ] )
            ->name ( 'del.test' );

        Route::get ( '/export', [ ATBController::class, 'export' ] )
            ->name ( 'export' );

        Route::post ( '/import', [ ATBController::class, 'import' ] )
            ->name ( 'import.post' );

        Route::get ( '/import', [ ATBController::class, 'showImportForm' ] )
            ->name ( 'import' );

        Route::get ( '/stt/{id}', [ ATBController::class, 'getStt' ] )
            ->where ( 'id', '[0-9]+' )
            ->name ( 'stt' );

        Route::get ( '/dokumentasi/{id}', [ ATBController::class, 'getDokumentasi' ] )
            ->where ( 'id', '[0-9]+' )
            ->name ( 'dokumentasi' );
    } );

// Rute APB [APBController]
Route::prefix ( 'apb' )
    ->middleware ( [ CheckRole::class . ':Admin,Pegawai,Boss' ] )
    ->group ( function ()
    {
        Route::get ( '/', [ APBController::class, 'index' ] )
            ->name ( 'apb' );

        Route::get ( '/ex_panjar_unit_alat', [ APBController::class, 'ex_panjar_unit_alat' ] )
            ->name ( 'apb_ex_panjar_unit_alat' );

        Route::get ( '/ex_panjar_unit_alat/add', [ APBController::class, 'data_baru_ex_panjar_unit_alat' ] )
            ->name ( 'data_baru_ex_panjar_unit_alat' );

        Route::get ( '/ex_panjar_unit_alat/{atb}', [ APBController::class, 'showByID' ] )
            ->name ( 'apb.showAPBByID' );

        Route::get ( '/ex_panjar_unit_alat/apb/{apb}', [ APBController::class, 'getAPBbyID' ] )
            ->name ( 'apb.showAPBByID' );

        Route::post ( '/ex_panjar_unit_alat/edit/actions/{id}', [ APBController::class, 'update' ] )
            ->name ( 'apb.post.update.ex_panjar_unit_alat' );

        Route::delete ( '/ex_panjar_unit_alat/actions/{id}', [ APBController::class, 'destroy' ] )
            ->name ( 'apb.delete.destroy.ex_panjar_unit_alat' );

        Route::get ( '/ex_panjar_proyek', [ APBController::class, 'ex_panjar_proyek' ] )
            ->name ( 'apb_ex_panjar_proyek' );

        Route::get ( '/ex_panjar_proyek/add', [ APBController::class, 'data_baru_ex_panjar_proyek' ] )
            ->name ( 'data_baru_ex_panjar_proyek' );

        Route::get ( '/ex_panjar_proyek/{atb}', [ APBController::class, 'showByID' ] )
            ->name ( 'apb.showAPBByID' );

        Route::get ( '/ex_panjar_proyek/apb/{apb}', [ APBController::class, 'getAPBbyID' ] )
            ->name ( 'apb.showAPBByID' );

        Route::post ( '/ex_panjar_proyek/edit/actions/{id}', [ APBController::class, 'update' ] )
            ->name ( 'apb.post.update.ex_panjar_proyek' );

        Route::delete ( '/ex_panjar_proyek/actions/{id}', [ APBController::class, 'destroy' ] )
            ->name ( 'apb.delete.destroy.ex_panjar_proyek' );

        Route::get ( '/ex_unit_alat', [ APBController::class, 'ex_unit_alat' ] )
            ->name ( 'apb_ex_unit_alat' );

        Route::get ( '/ex_unit_alat/add', [ APBController::class, 'data_baru_ex_unit_alat' ] )
            ->name ( 'data_baru_ex_unit_alat' );

        Route::get ( '/ex_unit_alat/{atb}', [ APBController::class, 'showByID' ] )
            ->name ( 'apb.showAPBByID' );

        Route::get ( '/ex_unit_alat/apb/{apb}', [ APBController::class, 'getAPBbyID' ] )
            ->name ( 'apb.showAPBByID' );

        Route::post ( '/ex_unit_alat/edit/actions/{id}', [ APBController::class, 'update' ] )
            ->name ( 'apb.post.update.ex_unit_alat' );

        Route::delete ( '/ex_unit_alat/actions/{id}', [ APBController::class, 'destroy' ] )
            ->name ( 'apb.delete.destroy.ex_unit_alat' );

        Route::get ( '/ex_mutasi_saldo', [ APBController::class, 'ex_mutasi_saldo' ] )
            ->name ( 'apb_ex_mutasi_saldo' );

        Route::get ( '/ex_mutasi_saldo/add', [ APBController::class, 'data_baru_ex_mutasi_saldo' ] )
            ->name ( 'data_baru_ex_mutasi_saldo' );

        Route::get ( '/ex_mutasi_saldo/{atb}', [ APBController::class, 'showByID' ] )
            ->name ( 'apb.showAPBByID' );

        Route::get ( '/ex_mutasi_saldo/apb/{apb}', [ APBController::class, 'getAPBbyID' ] )
            ->name ( 'apb.showAPBByID' );

        Route::post ( '/ex_mutasi_saldo/edit/actions/{id}', [ APBController::class, 'update' ] )
            ->name ( 'apb.post.update.ex_mutasi_saldo' );

        Route::delete ( '/ex_mutasi_saldo/actions/{id}', [ APBController::class, 'destroy' ] )
            ->name ( 'apb.delete.destroy.ex_mutasi_saldo' );

        Route::get ( '/add', [ APBController::class, 'data_baru_apb' ] )
            ->name ( 'data_baru_apb' );

        Route::post ( '/store', [ APBController::class, 'store' ] )
            ->name ( 'apb.post.store' );

        Route::get ( '/actions/{id}', [ APBController::class, 'show' ] )
            ->name ( 'apb.show' );

        Route::put ( '/actions/{id}', [ APBController::class, 'update' ] )
            ->name ( 'apb.post.update' );

        Route::delete ( '/actions/{id}', [ APBController::class, 'destroy' ] )
            ->name ( 'apb.delete.destroy' );

        Route::get ( '/delapb/actions/{id}', [ APBController::class, 'destroy' ] )
            ->name ( 'apb.del.test' );
    } );

// Rute Saldo [SaldoController]
Route::prefix ( 'saldo' )
    ->middleware ( [ CheckRole::class . ':Admin,Pegawai,Boss' ] )
    ->group ( function ()
    {
        Route::get ( '/', [ SaldoController::class, 'index' ] )
            ->name ( 'saldo' );

        Route::get ( '/ex_panjar_unit_alat', [ SaldoController::class, 'ex_panjar_unit_alat' ] )
            ->name ( 'saldo_ex_panjar_unit_alat' );

        Route::get ( '/ex_panjar_unit_alat/add', [ SaldoController::class, 'data_baru_ex_panjar_unit_alat' ] )
            ->name ( 'saldo.new.ex_panjar_unit_alat' );

        Route::get ( '/ex_panjar_unit_alat/{saldo}', [ SaldoController::class, 'showByID' ] )
            ->name ( 'saldo.show.ex_panjar_unit_alat_by_id' );

        Route::post ( '/ex_panjar_unit_alat/edit/actions/{id}', [ SaldoController::class, 'update' ] )
            ->name ( 'saldo.post.update.ex_panjar_unit_alat' );

        Route::delete ( '/ex_panjar_unit_alat/actions/{id}', [ SaldoController::class, 'destroy' ] )
            ->name ( 'saldo.delete.destroy.ex_panjar_unit_alat' );

        Route::get ( '/ex_panjar_proyek', [ SaldoController::class, 'ex_panjar_proyek' ] )
            ->name ( 'saldo_ex_panjar_proyek' );

        Route::get ( '/ex_panjar_proyek/add', [ SaldoController::class, 'data_baru_ex_panjar_proyek' ] )
            ->name ( 'saldo.new.ex_panjar_proyek' );

        Route::get ( '/ex_panjar_proyek/{saldo}', [ SaldoController::class, 'showByID' ] )
            ->name ( 'saldo.show.ex_panjar_proyek_by_id' );

        Route::post ( '/ex_panjar_proyek/edit/actions/{id}', [ SaldoController::class, 'update' ] )
            ->name ( 'saldo.post.update.ex_panjar_proyek' );

        Route::delete ( '/ex_panjar_proyek/actions/{id}', [ SaldoController::class, 'destroy' ] )
            ->name ( 'saldo.delete.destroy.ex_panjar_proyek' );

        Route::get ( '/ex_unit_alat', [ SaldoController::class, 'ex_unit_alat' ] )
            ->name ( 'saldo_ex_unit_alat' );

        Route::get ( '/ex_unit_alat/add', [ SaldoController::class, 'data_baru_ex_unit_alat' ] )
            ->name ( 'saldo.new.ex_unit_alat' );

        Route::get ( '/ex_unit_alat/{saldo}', [ SaldoController::class, 'showByID' ] )
            ->name ( 'saldo.show.ex_unit_alat_by_id' );

        Route::post ( '/ex_unit_alat/edit/actions/{id}', [ SaldoController::class, 'update' ] )
            ->name ( 'saldo.post.update.ex_unit_alat' );

        Route::delete ( '/ex_unit_alat/actions/{id}', [ SaldoController::class, 'destroy' ] )
            ->name ( 'saldo.delete.destroy.ex_unit_alat' );

        Route::get ( '/ex_mutasi_saldo', [ SaldoController::class, 'ex_mutasi_saldo' ] )
            ->name ( 'saldo_ex_mutasi_saldo' );

        Route::get ( '/ex_mutasi_saldo/add', [ SaldoController::class, 'data_baru_ex_mutasi_saldo' ] )
            ->name ( 'saldo.new.ex_mutasi_saldo' );

        Route::get ( '/ex_mutasi_saldo/{saldo}', [ SaldoController::class, 'showByID' ] )
            ->name ( 'saldo.show.ex_mutasi_saldo_by_id' );

        Route::post ( '/ex_mutasi_saldo/edit/actions/{id}', [ SaldoController::class, 'update' ] )
            ->name ( 'saldo.post.update.ex_mutasi_saldo' );

        Route::delete ( '/ex_mutasi_saldo/actions/{id}', [ SaldoController::class, 'destroy' ] )
            ->name ( 'saldo.delete.destroy.ex_mutasi_saldo' );

        // General Saldo Routes
        Route::get ( '/add', [ SaldoController::class, 'data_baru_saldo' ] )
            ->name ( 'saldo.new' );

        Route::post ( '/store', [ SaldoController::class, 'store' ] )
            ->name ( 'saldo.store' );

        Route::get ( '/actions/{id}', [ SaldoController::class, 'show' ] )
            ->name ( 'saldo.show' );

        Route::put ( '/actions/{id}', [ SaldoController::class, 'update' ] )
            ->name ( 'saldo.post.update' );

        Route::delete ( '/actions/{id}', [ SaldoController::class, 'destroy' ] )
            ->name ( 'saldo.delete.destroy' );

        Route::get ( '/delsaldo/actions/{id}', [ SaldoController::class, 'destroy' ] )
            ->name ( 'saldo.del.test' );

        Route::get ( '/export', [ SaldoController::class, 'exportSaldo' ] )
            ->name ( 'saldo.export' );
    } );

Route::get ( '/laporan/summary', [ LaporanController::class, 'summary' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss',
] )
    ->name ( 'laporan.summary' );
Route::get ( '/laporan/lnpb', [ LaporanController::class, 'LNPB' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss',
] )
    ->name ( 'laporan.lnpb' );

Route::prefix ( 'ajax' )->group ( function ()
{
    Route::get ( '/atb/fetch-data', [ ATBController::class, 'fetchData' ] )->name ( 'atb.fetchData' );
} );

Route::prefix ( 'ajax' )->group ( function ()
{
    Route::get ( '/apb/fetch-data', [ APBController::class, 'fetchData' ] )->name ( 'apb.fetchData' );
} );

Route::prefix ( 'ajax' )->group ( function ()
{
    Route::get ( '/saldo/fetch-data', [ SaldoController::class, 'fetchData' ] )->name ( 'saldo.fetchData' );
} );

Route::prefix ( 'ajax' )->group ( function ()
{
    Route::get ( '/summary/fetch-data', [ LaporanController::class, 'fetchData' ] )->name ( 'summary.fetchData' );
} );

// Route::get ( '/dashboard/proyek/actions/{id}', [ DashboardController::class, 'filterByProyek' ] )
//     ->where ( 'id', '[0-9]+' )
//     ->middleware ( [ 
//         CheckRole::class . ':Admin,Pegawai,Boss',
//     ] )
//     ->name ( 'dashboard.filter' );

// Route untuk mengakses dokumentasi [Security Purpose]
// Route::get (
//     '/dokumentasi/atb/{filename}',
//     [ ATBController::class, 'showDokumentasi' ]
// )
//     ->name ( 'atb.dokumentasi' )
//     ->middleware ( 'auth' );

Route::get (
    '/apb/dokumentasi/{filename}',
    [ APBController::class, 'showDokumentasi' ]
)
    ->name ( 'apb.dokumentasi' )
    ->middleware ( 'auth' );

// Rute Pagination Handler
Route::prefix ( 'pagination' )->middleware ( 'auth' )->group ( function ()
{
    Route::get (
        '/master-data-alat/data',
        [ MasterDataAlatController::class, 'getData' ]
    )
        ->name ( 'master-data.alat.getData' );
    Route::get (
        '/master-data-sparepart/data',
        [ MasterDataSparepartController::class, 'getData' ]
    )
        ->name (
            'master-data.sparepart.getData'
        );
    Route::get (
        '/master-data-supplier/data',
        [ MasterDataSupplierController::class, 'getData' ]
    )
        ->name ( 'master-data.supplier.getData' );

    Route::get (
        '/proyek/data',
        [ ProyekController::class, 'getData' ]
    )
        ->name ( 'proyek.getData' );

    Route::get (
        '/rkb-general/data',
        [ RKBGeneralController::class, 'getData' ]
    )
        ->name ( 'rkb_general.getData' );

    Route::get (
        '/rkb-general/detail/{id_rkb}',
        [ DetailRKBGeneralController::class, 'getData' ]
    )
        ->name ( 'detail_rkb_general.getData' );

    Route::get (
        '/evaluasi-rkb-general/data',
        [ EvaluasiRKBGeneralController::class, 'getData' ]
    )
        ->name ( 'evaluasi_rkb_general.getData' );

    Route::get (
        '/evaluasi-rkb-general/detail/{id_rkb}',
        [ EvaluasiDetailRKBGeneralController::class, 'getData' ]
    )
        ->name ( 'evaluasi_detail_rkb_general.getData' );

    Route::get (
        '/rkb-urgent/data',
        [ RKBUrgentController::class, 'getData' ]
    )
        ->name ( 'rkb_urgent.getData' );

    Route::get (
        '/rkb-urgent/detail/{id_rkb}',
        [ DetailRKBUrgentController::class, 'getData' ]
    )
        ->name ( 'detail_rkb_urgent.getData' );

    Route::get (
        '/evaluasi-rkb-urgent/data',
        [ EvaluasiRKBUrgentController::class, 'getData' ]
    )
        ->name ( 'evaluasi_rkb_urgent.getData' );

    Route::get (
        '/evaluasi-rkb-urgent/detail/{id_rkb}',
        [ EvaluasiDetailRKBUrgentController::class, 'getData' ]
    )
        ->name ( 'evaluasi_detail_rkb_urgent.getData' );

    Route::get (
        '/spb/data',
        [ SPBController::class, 'getData' ]
    )
        ->name ( 'spb.getData' );

    Route::get (
        '/spb/proyek/data',
        [ SPBProyekController::class, 'getData' ]
    )
        ->name ( 'spb.proyek.getData' );
} );

// Rute Proyek [ProyekController]
Route::prefix ( 'proyek' )
    ->middleware ( [ CheckRole::class . ':Admin,Pegawai,Boss' ] )
    ->group ( function ()
    {
        Route::get (
            '/',
            [ ProyekController::class, 'index' ]
        )
            ->name ( 'proyek.index' );

        Route::get (
            '{id}',
            [ ProyekController::class, 'show' ]
        )
            ->where ( 'id', '[0-9]+' )
            ->name ( 'proyek.show' );

        Route::post (
            '/',
            [ ProyekController::class, 'store' ]
        )
            ->name ( 'proyek.store' );

        Route::put (
            '{id}',
            [ ProyekController::class, 'update' ]
        )
            ->where ( 'id', '[0-9]+' )
            ->name ( 'proyek.update' );

        Route::delete (
            '{id}',
            [ ProyekController::class, 'destroy' ]
        )
            ->where ( 'id', '[0-9]+' )
            ->name ( 'proyek.destroy' );
    } );

// Rute Master Data Alat [MasterDataAlatController]
Route::middleware ( 'auth' )
    ->prefix ( 'master-data-alat' )
    ->group ( function ()
    {
        Route::get (
            '/',
            [ MasterDataAlatController::class, 'index' ]
        )->name ( 'master_data_alat.index' );

        Route::get (
            '{id}',
            [ MasterDataAlatController::class, 'show' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'master_data_alat.show' );

        Route::post (
            '/',
            [ MasterDataAlatController::class, 'store' ]
        )->name ( 'master_data_alat.store' );

        Route::put (
            '{id}',
            [ MasterDataAlatController::class, 'update' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'master_data_alat.update' );

        Route::delete (
            '{id}',
            [ MasterDataAlatController::class, 'destroy' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'master_data_alat.destroy' );
    } );

// Rute Master Data Sparepart [MasterDataSparepartController]
Route::middleware ( 'auth' )
    ->prefix ( 'master-data-sparepart' )
    ->group ( function ()
    {
        Route::get (
            '/',
            [ MasterDataSparepartController::class, 'index' ]
        )->name ( 'master_data_sparepart.index' );

        Route::get (
            '{id}',
            [ MasterDataSparepartController::class, 'show' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'master_data_sparepart.show' );

        Route::post (
            '/',
            [ MasterDataSparepartController::class, 'store' ]
        )->name ( 'master_data_sparepart.store' );

        Route::put (
            '{id}',
            [ MasterDataSparepartController::class, 'update' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'master_data_sparepart.update' );

        Route::delete (
            '{id}',
            [ MasterDataSparepartController::class, 'destroy' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'master_data_sparepart.destroy' );
    } );

Route::get (
    '/spareparts-by-category/{id}',
    [ MasterDataSparepartController::class, 'getSparepartsByCategory' ]
)->where ( 'id', '[0-9]+' )
    ->name ( 'spareparts-by-category' );

// Rute Master Data Supplier [MasterDataSupplierController]
Route::middleware ( 'auth' )
    ->prefix ( 'master-data-supplier' )
    ->group ( function ()
    {
        Route::get (
            '/',
            [ MasterDataSupplierController::class, 'index' ]
        )->name ( 'master_data_supplier.index' );

        Route::get (
            '{id}',
            [ MasterDataSupplierController::class, 'show' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'master_data_supplier.show' );

        Route::post (
            '/',
            [ MasterDataSupplierController::class, 'store' ]
        )->name ( 'master_data_supplier.store' );

        Route::put (
            '{id}',
            [ MasterDataSupplierController::class, 'update' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'master_data_supplier.update' );

        Route::delete (
            '{id}',
            [ MasterDataSupplierController::class, 'destroy' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'master_data_supplier.destroy' );
    } );

// Rute RKB General [RKBGeneralController]
Route::middleware ( 'auth' )
    ->prefix ( 'rkb-general' )
    ->as ( 'rkb_general.' )
    ->group ( function ()
    {
        Route::get (
            '/',
            [ RKBGeneralController::class, 'index' ]
        )->name ( 'index' );

        Route::get (
            '/{id}',
            [ RKBGeneralController::class, 'show' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'show' );

        Route::post (
            '/',
            [ RKBGeneralController::class, 'store' ]
        )->name ( 'store' );

        Route::put (
            '/{id}',
            [ RKBGeneralController::class, 'update' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'update' );

        Route::delete (
            '/{id}',
            [ RKBGeneralController::class, 'destroy' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'destroy' );

        Route::post (
            '/finalize/{id}',
            [ RKBGeneralController::class, 'finalize' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'finalize' );

        // Rute Detail RKB General [DetailRKBGeneralController]
        Route::prefix ( 'detail' )
            ->as ( 'detail.' )
            ->group ( function ()
        {
            Route::get (
                '/{id}',
                [ DetailRKBGeneralController::class, 'index' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'index' );

            Route::get (
                '/show/{id}',
                [ DetailRKBGeneralController::class, 'show' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'show' );

            Route::post (
                '/',
                [ DetailRKBGeneralController::class, 'store' ]
            )->name ( 'store' );

            Route::put (
                '/{id}',
                [ DetailRKBGeneralController::class, 'update' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'update' );

            Route::delete (
                '/{id}',
                [ DetailRKBGeneralController::class, 'destroy' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'destroy' );
        } );
    } );

// Rute RKB Urgent [RKBUrgentController]
Route::middleware ( 'auth' )
    ->prefix ( 'rkb-urgent' )
    ->as ( 'rkb_urgent.' )
    ->group ( function ()
    {
        Route::get (
            '/',
            [ RKBUrgentController::class, 'index' ]
        )->name ( 'index' );

        Route::get (
            '/{id}',
            [ RKBUrgentController::class, 'show' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'show' );

        Route::post (
            '/',
            [ RKBUrgentController::class, 'store' ]
        )->name ( 'store' );

        Route::put (
            '/{id}',
            [ RKBUrgentController::class, 'update' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'update' );

        Route::delete (
            '/{id}',
            [ RKBUrgentController::class, 'destroy' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'destroy' );

        Route::post (
            '/finalize/{id}',
            [ RKBUrgentController::class, 'finalize' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'finalize' );

        // Rute Detail RKB Urgent [DetailRKBUrgentController]
        Route::prefix ( 'detail' )
            ->as ( 'detail.' )
            ->group ( function ()
        {
            Route::get (
                '/{id}',
                [ DetailRKBUrgentController::class, 'index' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'index' );

            Route::get (
                '/show/{id}',
                [ DetailRKBUrgentController::class, 'show' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'show' );

            Route::post (
                '/',
                [ DetailRKBUrgentController::class, 'store' ]
            )->name ( 'store' );

            Route::put (
                '/{id}',
                [ DetailRKBUrgentController::class, 'update' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'update' );

            Route::delete (
                '/{id}',
                [ DetailRKBUrgentController::class, 'destroy' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'destroy' );

            Route::get (
                '/{id}/dokumentasi',
                [ DetailRKBUrgentController::class, 'getDokumentasi' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'dokumentasi' );

            // Rute Timeline RKB Urgent [TimelineRKBUrgentController]
            Route::prefix ( 'timeline' )
                ->as ( 'timeline.' )
                ->group ( function ()
            {
                Route::get (
                    '/{id}',
                    [ TimelineRKBUrgentController::class, 'index' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'index' );

                Route::post (
                    '/',
                    [ TimelineRKBUrgentController::class, 'store' ]
                )->name ( 'store' );

                Route::get (
                    '/show/{id}',
                    [ TimelineRKBUrgentController::class, 'show' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'show' );

                Route::put (
                    '/{id}',
                    [ TimelineRKBUrgentController::class, 'update' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'update' );

                Route::delete (
                    '/{id}',
                    [ TimelineRKBUrgentController::class, 'destroy' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'destroy' );
            } );

            // Rute Lampiran RKB Urgent [LampiranRKBUrgentController]
            Route::prefix ( 'lampiran' )
                ->as ( 'lampiran.' )
                ->group ( function ()
            {
                Route::post (
                    '/',
                    [ LampiranRKBUrgentController::class, 'store' ]
                )->name ( 'store' );

                Route::get (
                    '/show/{id}',
                    [ LampiranRKBUrgentController::class, 'show' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'show' );

                Route::put (
                    '/{id}',
                    [ LampiranRKBUrgentController::class, 'update' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'update' );

                Route::delete (
                    '/{id}',
                    [ LampiranRKBUrgentController::class, 'destroy' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'destroy' );
            } );
        } );
    } );

// Rute Evaluasi RKB General [EvaluasiRKBGeneralController]
Route::middleware ( 'auth' )
    ->prefix ( 'evaluasi-rkb-general' )
    ->as ( 'evaluasi_rkb_general.' )
    ->group ( function ()
    {
        Route::get (
            '/',
            [ EvaluasiRKBGeneralController::class, 'index' ]
        )->name ( 'index' );

        Route::get (
            '/{id}',
            [ EvaluasiRKBGeneralController::class, 'show' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'show' );

        Route::post (
            '/',
            [ EvaluasiRKBGeneralController::class, 'store' ]
        )->name ( 'store' );

        Route::put (
            '/{id}',
            [ EvaluasiRKBGeneralController::class, 'update' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'update' );

        Route::delete (
            '/{id}',
            [ EvaluasiRKBGeneralController::class, 'destroy' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'destroy' );

        // Rute Detail RKB General [DetailRKBGeneralController]
        Route::prefix ( 'detail' )
            ->as ( 'detail.' )
            ->group ( function ()
        {
            Route::get (
                '/{id}',
                [ EvaluasiDetailRKBGeneralController::class, 'index' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'index' );

            Route::get (
                '/show/{id}',
                [ EvaluasiDetailRKBGeneralController::class, 'show' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'show' );

            Route::post (
                '/',
                [ EvaluasiDetailRKBGeneralController::class, 'store' ]
            )->name ( 'store' );

            Route::put (
                '/{id}',
                [ EvaluasiDetailRKBGeneralController::class, 'update' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'update' );

            Route::delete (
                '/{id}',
                [ EvaluasiDetailRKBGeneralController::class, 'destroy' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'destroy' );

            Route::post (
                '/approve/{id}',
                [ EvaluasiDetailRKBGeneralController::class, 'approve' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'approve' );

            Route::post (
                '/evaluate/{id}',
                [ EvaluasiDetailRKBGeneralController::class, 'evaluate' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'evaluate' );

        } );
    } );

// Rute Evaluasi RKB Urgent [EvaluasiRKBUrgentController]
Route::middleware ( 'auth' )
    ->prefix ( 'evaluasi-rkb-urgent' )
    ->as ( 'evaluasi_rkb_urgent.' )
    ->group ( function ()
    {
        Route::get (
            '/',
            [ EvaluasiRKBUrgentController::class, 'index' ]
        )->name ( 'index' );

        Route::get (
            '/{id}',
            [ EvaluasiRKBUrgentController::class, 'show' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'show' );

        Route::post (
            '/',
            [ EvaluasiRKBUrgentController::class, 'store' ]
        )->name ( 'store' );

        Route::put (
            '/{id}',
            [ EvaluasiRKBUrgentController::class, 'update' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'update' );

        Route::delete (
            '/{id}',
            [ EvaluasiRKBUrgentController::class, 'destroy' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'destroy' );

        // Rute Detail RKB Urgent [DetailRKBUrgentController]
        Route::prefix ( 'detail' )
            ->as ( 'detail.' )
            ->group ( function ()
        {
            Route::get (
                '/{id}',
                [ EvaluasiDetailRKBUrgentController::class, 'index' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'index' );

            Route::get (
                '/show/{id}',
                [ EvaluasiDetailRKBUrgentController::class, 'show' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'show' );

            Route::post (
                '/',
                [ EvaluasiDetailRKBUrgentController::class, 'store' ]
            )->name ( 'store' );

            Route::put (
                '/{id}',
                [ EvaluasiDetailRKBUrgentController::class, 'update' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'update' );

            Route::delete (
                '/{id}',
                [ EvaluasiDetailRKBUrgentController::class, 'destroy' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'destroy' );

            Route::post (
                '/approve/{id}',
                [ EvaluasiDetailRKBUrgentController::class, 'approve' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'approve' );

            Route::post (
                '/evaluate/{id}',
                [ EvaluasiDetailRKBUrgentController::class, 'evaluate' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'evaluate' );

            Route::get (
                '/{id}/dokumentasi',
                [ EvaluasiDetailRKBUrgentController::class, 'getDokumentasi' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'dokumentasi' );

            // Rute Timeline Evaluasi Urgent [TimelineEvaluasiUrgentController]
            Route::prefix ( 'timeline' )
                ->as ( 'timeline.' )
                ->group ( function ()
            {
                Route::get (
                    '/{id}',
                    [ TimelineEvaluasiUrgentController::class, 'index' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'index' );

                Route::post (
                    '/',
                    [ TimelineEvaluasiUrgentController::class, 'store' ]
                )->name ( 'store' );

                Route::get (
                    '/show/{id}',
                    [ TimelineEvaluasiUrgentController::class, 'show' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'show' );

                Route::put (
                    '/{id}',
                    [ TimelineEvaluasiUrgentController::class, 'update' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'update' );

                Route::delete (
                    '/{id}',
                    [ TimelineEvaluasiUrgentController::class, 'destroy' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'destroy' );
            } );

            // Rute Lampiran Evaluasi Urgent [LampiranEvaluasiUrgentController]
            Route::prefix ( 'lampiran' )
                ->as ( 'lampiran.' )
                ->group ( function ()
            {
                Route::post (
                    '/',
                    [ LampiranEvaluasiUrgentController::class, 'store' ]
                )->name ( 'store' );

                Route::get (
                    '/show/{id}',
                    [ LampiranEvaluasiUrgentController::class, 'show' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'show' );

                Route::put (
                    '/{id}',
                    [ LampiranEvaluasiUrgentController::class, 'update' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'update' );

                Route::delete (
                    '/{id}',
                    [ LampiranEvaluasiUrgentController::class, 'destroy' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'destroy' );
            } );
        } );
    } );

// Rute SPB (Surat Pemesanan Barang) Supplier [SPBController]
Route::middleware ( 'auth' )
    ->prefix ( 'spb' )
    ->as ( 'spb.' )
    ->group ( function ()
    {
        Route::get (
            '/',
            [ SPBController::class, 'index' ]
        )->name ( 'index' );

        Route::get (
            '/{id}',
            [ SPBController::class, 'show' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'show' );

        Route::post (
            '/',
            [ SPBController::class, 'store' ]
        )->name ( 'store' );

        Route::put (
            '/{id}',
            [ SPBController::class, 'update' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'update' );

        Route::delete (
            '/destroy/{id}',
            [ SPBController::class, 'destroy' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'destroy' );

        Route::delete (
            '/addendum/{id}',
            [ SPBController::class, 'addendum' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'addendum' );

        // Rute SPB Proyek [SPBProyekController]
        Route::prefix ( 'proyek' )
            ->as ( 'proyek.' )
            ->group ( function ()
        {
            Route::get (
                '/',
                [ SPBProyekController::class, 'index' ]
            )->name ( 'index' );

            Route::get (
                '/show/{id}',
                [ SPBProyekController::class, 'show' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'show' );

            Route::post (
                '/',
                [ SPBProyekController::class, 'store' ]
            )->name ( 'store' );

            Route::delete (
                '/{id}',
                [ SPBProyekController::class, 'destroy' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'destroy' );
            Route::get (
                '/{id}/export-pdf',
                [ SPBProyekController::class, 'exportPDF' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'export-pdf' );

            Route::prefix ( 'detail' )
                ->as ( 'detail.' )
                ->group ( function ()
                {
                    Route::get (
                        '/{id}',
                        [ DetailSPBProyekController::class, 'index' ]
                    )->where ( 'id', '[0-9]+' )
                        ->name ( 'index' );

                    Route::get (
                        '/show/{id}',
                        [ DetailSPBProyekController::class, 'show' ]
                    )->where ( 'id', '[0-9]+' )
                        ->name ( 'show' );

                    Route::post (
                        '/',
                        [ DetailSPBProyekController::class, 'store' ]
                    )->name ( 'store' );

                    Route::put (
                        '/{id}',
                        [ DetailSPBProyekController::class, 'update' ]
                    )->where ( 'id', '[0-9]+' )
                        ->name ( 'update' );

                    Route::delete (
                        '/{id}',
                        [ DetailSPBProyekController::class, 'destroy' ]
                    )->where ( 'id', '[0-9]+' )
                        ->name ( 'destroy' );
                } );
        } );

        // Rute Detail SPB [DetailSPBController]
        Route::prefix ( 'detail' )
            ->as ( 'detail.' )
            ->group ( function ()
        {
            Route::get (
                '/{id}',
                [ DetailSPBController::class, 'index' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'index' );

            Route::get (
                '/show/{id}',
                [ DetailSPBController::class, 'show' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'show' );

            Route::post (
                '/',
                [ DetailSPBController::class, 'store' ]
            )->name ( 'store' );

            Route::put (
                '/{id}',
                [ DetailSPBController::class, 'update' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'update' );

            Route::delete (
                '/{id}',
                [ DetailSPBController::class, 'destroy' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'destroy' );

            Route::post (
                '/approve/{id}',
                [ DetailSPBController::class, 'approve' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'approve' );

            Route::get (
                '/getSparepart/{idSupplier}',
                [ DetailSPBController::class, 'getSparepart' ]
            )->where ( 'id', '[0-9]+' )
                ->name ( 'getSparepart' );

            // Rute Riwayat SPB [RiwayatSPBController]
            Route::prefix ( 'riwayat' )
                ->as ( 'riwayat.' )
                ->group ( function ()
            {
                Route::get (
                    '/{id}',
                    [ RiwayatSPBController::class, 'index' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'index' );

                Route::get (
                    '/show/{id}',
                    [ RiwayatSPBController::class, 'show' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'show' );

                Route::post (
                    '/',
                    [ RiwayatSPBController::class, 'store' ]
                )->name ( 'store' );

                Route::delete (
                    '/{id}',
                    [ RiwayatSPBController::class, 'destroy' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'destroy' );
                Route::get (
                    '/{id}/export-pdf',
                    [ RiwayatSPBController::class, 'exportPDF' ]
                )->where ( 'id', '[0-9]+' )
                    ->name ( 'export-pdf' );
            } );
        } );
    } );


// Rute Alat Proyek [AlatProyekController]
Route::middleware ( 'auth' )
    ->prefix ( 'alat' )
    ->as ( 'alat.' )
    ->group ( function ()
    {
        Route::get (
            '/',
            [ AlatProyekController::class, 'index' ]
        )->where ( 'id_proyek', '[0-9]+' )
            ->name ( 'index' );

        Route::get (
            '{id}',
            [ AlatProyekController::class, 'show' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'show' );

        Route::post (
            '/',
            [ AlatProyekController::class, 'store' ]
        )->name ( 'store' );

        Route::put (
            '{id}',
            [ AlatProyekController::class, 'update' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'update' );

        Route::delete (
            '{id}',
            [ AlatProyekController::class, 'destroy' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'destroy' );
    } );