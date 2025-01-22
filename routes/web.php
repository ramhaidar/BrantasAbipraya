<?php
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APBController;
use App\Http\Controllers\ATBController;
use App\Http\Controllers\SPBController;
use App\Http\Controllers\TestController;
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
use App\Http\Controllers\LaporanLNPBTotalController;
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
use App\Http\Controllers\LaporanLNPBBulanBerjalanController;

Route::get (
    '/test',
    [ TestController::class, 'index' ]
)->name ( 'test' );

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
Route::prefix('users')->middleware([CheckRole::class . ':Admin,Pegawai,Boss'])->group(function () {
    Route::get('/', [UserController::class, 'index'])
        ->name('users');
    Route::get('/{user}', [UserController::class, 'showByID'])
        ->where('user', '[0-9]+')
        ->name('api.showUserByID');
    Route::put('/edit/{user}', [UserController::class, 'update']) // Changed from post to put
        ->where('user', '[0-9]+')
        ->name('user.put.update');
    Route::post('/add', [UserController::class, 'store'])
        ->name('user.post.store');
    Route::delete('/delete/{user}', [UserController::class, 'destroy'])
        ->where('user', '[0-9]+')
        ->name('user.delete.destroy');
});

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

            Route::patch ( '/accept/{id}', [ ATBController::class, 'acceptMutasi' ] )
                ->name ( 'mutasi.accept' );

            Route::patch ( '/reject/{id}', [ ATBController::class, 'rejectMutasi' ] )
                ->name ( 'mutasi.reject' );
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
Route::middleware ( [ CheckRole::class . ':Admin,Pegawai,Boss' ] )
    ->prefix ( 'apb' )
    ->as ( 'apb.' )
    ->group ( function ()
    {

        Route::get ( '/', [ APBController::class, 'index' ] )
            ->name ( 'apb' );

        Route::get ( '/getlinkSpbDetailSpbs/{id}', [ APBController::class, 'getlinkSpbDetailSpbs' ] )
            ->name ( 'getlinkSpbDetailSpbs' );

        // Hutang Unit Alat Routes
        Route::prefix ( 'hutang_unit_alat' )
            ->group ( function ()
        {
            Route::get ( '/', [ APBController::class, 'hutang_unit_alat' ] )
                ->name ( 'hutang_unit_alat' );

            Route::get ( '/add', [ APBController::class, 'data_baru_hutang_unit_alat' ] )
                ->name ( 'new.hutang_unit_alat' );

            Route::get ( '/{apb}', [ APBController::class, 'showByID' ] )
                ->name ( 'show.hutang_unit_alat_by_id' );

            Route::post ( '/edit/actions/{id}', [ APBController::class, 'update' ] )
                ->name ( 'post.update.hutang_unit_alat' );

            Route::delete ( '/actions/{id}', [ APBController::class, 'destroy' ] )
                ->name ( 'delete.destroy.hutang_unit_alat' );
        } );

        // Panjar Unit Alat Routes 
        Route::prefix ( 'panjar_unit_alat' )->group ( function ()
        {
            Route::get ( '/', [ APBController::class, 'panjar_unit_alat' ] )
                ->name ( 'panjar_unit_alat' );

            Route::get ( '/add', [ APBController::class, 'data_baru_panjar_unit_alat' ] )
                ->name ( 'new.panjar_unit_alat' );

            Route::get ( '/{apb}', [ APBController::class, 'showByID' ] )
                ->name ( 'show.panjar_unit_alat_by_id' );

            Route::post ( '/edit/actions/{id}', [ APBController::class, 'update' ] )
                ->name ( 'post.update.panjar_unit_alat' );

            Route::delete ( '/actions/{id}', [ APBController::class, 'destroy' ] )
                ->name ( 'delete.destroy.panjar_unit_alat' );
        } );

        // Mutasi Proyek Routes
        Route::prefix ( 'mutasi_proyek' )->group ( function ()
        {
            Route::get ( '/', [ APBController::class, 'mutasi_proyek' ] )
                ->name ( 'mutasi_proyek' );

            Route::get ( '/add', [ APBController::class, 'data_baru_mutasi_proyek' ] )
                ->name ( 'new.mutasi_proyek' );

            Route::get ( '/{apb}', [ APBController::class, 'showByID' ] )
                ->name ( 'show.mutasi_proyek_by_id' );

            Route::post ( '/edit/actions/{id}', [ APBController::class, 'update' ] )
                ->name ( 'post.update.mutasi_proyek' );

            Route::delete ( '/actions/{id}', [ APBController::class, 'destroy' ] )
                ->name ( 'delete.destroy.mutasi_proyek' );
        } );

        // Panjar Proyek Routes
        Route::prefix ( 'panjar_proyek' )->group ( function ()
        {
            Route::get ( '/', [ APBController::class, 'panjar_proyek' ] )
                ->name ( 'panjar_proyek' );

            Route::get ( '/add', [ APBController::class, 'data_baru_panjar_proyek' ] )
                ->name ( 'new.panjar_proyek' );

            Route::get ( '/{apb}', [ APBController::class, 'showByID' ] )
                ->name ( 'show.panjar_proyek_by_id' );

            Route::post ( '/edit/actions/{id}', [ APBController::class, 'update' ] )
                ->name ( 'post.update.panjar_proyek' );

            Route::delete ( '/actions/{id}', [ APBController::class, 'destroy' ] )
                ->name ( 'delete.destroy.panjar_proyek' );
        } );

        // General APB Routes
        Route::get ( '/add', [ APBController::class, 'data_baru_apb' ] )
            ->name ( 'new' );

        Route::post ( '/store', [ APBController::class, 'store' ] )
            ->name ( 'post.store' );

        Route::post ( '/mutasi_store', [ APBController::class, 'mutasi_store' ] )
            ->name ( 'post.mutasi' );

        Route::post ( '/actions/{id}', [ APBController::class, 'update' ] )
            ->name ( 'post.update' );

        Route::delete ( '/actions/{id}', [ APBController::class, 'destroy' ] )
            ->name ( 'destroy' );

        Route::delete ( '/mutasi_delete/{id}', [ APBController::class, 'mutasi_destroy' ] )
            ->name ( 'destroy.mutasi' );

        Route::get ( '/delapb/actions/{id}', [ APBController::class, 'destroy' ] )
            ->name ( 'del.test' );

        Route::get ( '/export', [ APBController::class, 'export' ] )
            ->name ( 'export' );

        Route::post ( '/import', [ APBController::class, 'import' ] )
            ->name ( 'import.post' );

        Route::get ( '/import', [ APBController::class, 'showImportForm' ] )
            ->name ( 'import' );

        Route::get ( '/stt/{id}', [ APBController::class, 'getStt' ] )
            ->where ( 'id', '[0-9]+' )
            ->name ( 'stt' );

        Route::get ( '/dokumentasi/{id}', [ APBController::class, 'getDokumentasi' ] )
            ->where ( 'id', '[0-9]+' )
            ->name ( 'dokumentasi' );
    } );

// Rute Saldo [SaldoController]
Route::middleware ( [ CheckRole::class . ':Admin,Pegawai,Boss' ] )
    ->prefix ( 'saldo' )
    ->as ( 'saldo.' )
    ->group ( function ()
    {

        Route::get ( '/', [ SaldoController::class, 'index' ] )
            ->name ( 'index' );

        Route::get ( '/getlinkSpbDetailSpbs/{id}', [ SaldoController::class, 'getlinkSpbDetailSpbs' ] )
            ->name ( 'getlinkSpbDetailSpbs' );

        // Hutang Unit Alat Routes
        Route::prefix ( 'hutang_unit_alat' )
            ->group ( function ()
        {
            Route::get ( '/', [ SaldoController::class, 'hutang_unit_alat' ] )
                ->name ( 'hutang_unit_alat' );

            Route::get ( '/add', [ SaldoController::class, 'data_baru_hutang_unit_alat' ] )
                ->name ( 'new.hutang_unit_alat' );

            Route::get ( '/{saldo}', [ SaldoController::class, 'showByID' ] )
                ->name ( 'show.hutang_unit_alat_by_id' );

            Route::post ( '/edit/actions/{id}', [ SaldoController::class, 'update' ] )
                ->name ( 'post.update.hutang_unit_alat' );

            Route::delete ( '/actions/{id}', [ SaldoController::class, 'destroy' ] )
                ->name ( 'delete.destroy.hutang_unit_alat' );
        } );

        // Panjar Unit Alat Routes
        Route::prefix ( 'panjar_unit_alat' )->group ( function ()
        {
            Route::get ( '/', [ SaldoController::class, 'panjar_unit_alat' ] )
                ->name ( 'panjar_unit_alat' );

            Route::get ( '/add', [ SaldoController::class, 'data_baru_panjar_unit_alat' ] )
                ->name ( 'new.panjar_unit_alat' );

            Route::get ( '/{saldo}', [ SaldoController::class, 'showByID' ] )
                ->name ( 'show.panjar_unit_alat_by_id' );

            Route::post ( '/edit/actions/{id}', [ SaldoController::class, 'update' ] )
                ->name ( 'post.update.panjar_unit_alat' );

            Route::delete ( '/actions/{id}', [ SaldoController::class, 'destroy' ] )
                ->name ( 'delete.destroy.panjar_unit_alat' );
        } );

        // Mutasi Proyek Routes
        Route::prefix ( 'mutasi_proyek' )->group ( function ()
        {
            Route::get ( '/', [ SaldoController::class, 'mutasi_proyek' ] )
                ->name ( 'mutasi_proyek' );

            Route::get ( '/add', [ SaldoController::class, 'data_baru_mutasi_proyek' ] )
                ->name ( 'new.mutasi_proyek' );

            Route::get ( '/{saldo}', [ SaldoController::class, 'showByID' ] )
                ->name ( 'show.mutasi_proyek_by_id' );

            Route::post ( '/edit/actions/{id}', [ SaldoController::class, 'update' ] )
                ->name ( 'post.update.mutasi_proyek' );

            Route::delete ( '/actions/{id}', [ SaldoController::class, 'destroy' ] )
                ->name ( 'delete.destroy.mutasi_proyek' );
        } );

        // Panjar Proyek Routes
        Route::prefix ( 'panjar_proyek' )->group ( function ()
        {
            Route::get ( '/', [ SaldoController::class, 'panjar_proyek' ] )
                ->name ( 'panjar_proyek' );

            Route::get ( '/add', [ SaldoController::class, 'data_baru_panjar_proyek' ] )
                ->name ( 'new.panjar_proyek' );

            Route::get ( '/{saldo}', [ SaldoController::class, 'showByID' ] )
                ->name ( 'show.panjar_proyek_by_id' );

            Route::post ( '/edit/actions/{id}', [ SaldoController::class, 'update' ] )
                ->name ( 'post.update.panjar_proyek' );

            Route::delete ( '/actions/{id}', [ SaldoController::class, 'destroy' ] )
                ->name ( 'delete.destroy.panjar_proyek' );
        } );

        // General Saldo Routes
        Route::get ( '/add', [ SaldoController::class, 'data_baru_saldo' ] )
            ->name ( 'new' );

        Route::post ( '/store', [ SaldoController::class, 'store' ] )
            ->name ( 'post.store' );

        Route::post ( '/actions/{id}', [ SaldoController::class, 'update' ] )
            ->name ( 'post.update' );

        Route::delete ( '/actions/{id}', [ SaldoController::class, 'destroy' ] )
            ->name ( 'destroy' );

        Route::get ( '/delsaldo/actions/{id}', [ SaldoController::class, 'destroy' ] )
            ->name ( 'del.test' );

        Route::get ( '/export', [ SaldoController::class, 'export' ] )
            ->name ( 'export' );

        Route::post ( '/import', [ SaldoController::class, 'import' ] )
            ->name ( 'import.post' );

        Route::get ( '/import', [ SaldoController::class, 'showImportForm' ] )
            ->name ( 'import' );

        Route::get ( '/stt/{id}', [ SaldoController::class, 'getStt' ] )
            ->where ( 'id', '[0-9]+' )
            ->name ( 'stt' );

        Route::get ( '/dokumentasi/{id}', [ SaldoController::class, 'getDokumentasi' ] )
            ->where ( 'id', '[0-9]+' )
            ->name ( 'dokumentasi' );
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

Route::get (
    '/spareparts-by-supplier-and-category/{supplier_id}/{kategori_id}',
    [ MasterDataSparepartController::class, 'getSparepartsBySupplierAndCategory' ]
)->where ( [ 
            'supplier_id' => '[0-9]+',
            'kategori_id' => '[0-9]+'
        ] )
    ->name ( 'spareparts-by-supplier-and-category' );

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

            // General RKB Approval Routes
            Route::post (
                '/approve/vp/{id}',
                [ EvaluasiDetailRKBGeneralController::class, 'approveVP' ]
            )->where ( 'id', '[0-9]+' )->name ( 'approve.vp' );

            Route::post (
                '/approve/svp/{id}',
                [ EvaluasiDetailRKBGeneralController::class, 'approveSVP' ]
            )->where ( 'id', '[0-9]+' )->name ( 'approve.svp' );

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

            // Urgent RKB Approval Routes
            Route::post (
                '/approve/vp/{id}',
                [ EvaluasiDetailRKBUrgentController::class, 'approveVP' ]
            )->where ( 'id', '[0-9]+' )->name ( 'approve.vp' );

            Route::post (
                '/approve/svp/{id}',
                [ EvaluasiDetailRKBUrgentController::class, 'approveSVP' ]
            )->where ( 'id', '[0-9]+' )->name ( 'approve.svp' );

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

// Rute Laporan LNPB Bulan Berjalan [LaporanLNPBBulanBerjalanController]
Route::middleware ( 'auth' )
    ->prefix ( 'laporan-lnpb-bulan-berjalan' )
    ->as ( 'laporan.lnpb.bulan_berjalan.' )
    ->group ( function ()
    {
        Route::get (
            '/',
            [ LaporanLNPBBulanBerjalanController::class, 'index' ]
        )->name ( 'index' );

        Route::get (
            '{id}',
            [ LaporanLNPBBulanBerjalanController::class, 'show' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'show' );

        Route::post (
            '/',
            [ LaporanLNPBBulanBerjalanController::class, 'store' ]
        )->name ( 'store' );

        Route::put (
            '{id}',
            [ LaporanLNPBBulanBerjalanController::class, 'update' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'update' );

        Route::delete (
            '{id}',
            [ LaporanLNPBBulanBerjalanController::class, 'destroy' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'destroy' );
    } );

// Rute Laporan LNPB Total [LaporanLNPBTotalController] 
Route::middleware ( 'auth' )
    ->prefix ( 'laporan-lnpb-total' )
    ->as ( 'laporan.lnpb.total.' )
    ->group ( function ()
    {
        Route::get (
            '/',
            [ LaporanLNPBTotalController::class, 'index' ]
        )->name ( 'index' );

        Route::get (
            '{id}',
            [ LaporanLNPBTotalController::class, 'show' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'show' );

        Route::post (
            '/',
            [ LaporanLNPBTotalController::class, 'store' ]
        )->name ( 'store' );

        Route::put (
            '{id}',
            [ LaporanLNPBTotalController::class, 'update' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'update' );

        Route::delete (
            '{id}',
            [ LaporanLNPBTotalController::class, 'destroy' ]
        )->where ( 'id', '[0-9]+' )
            ->name ( 'destroy' );
    } );

// Rute Laporan Semua Proyek [LaporanLNPBBulanBerjalanController & LaporanLNPBTotalController] 
Route::get (
    '/laporan/semua-bulan-berjalan',
    [ LaporanLNPBBulanBerjalanController::class, 'semuaBulanBerjalan_index' ]
)->name ( 'laporan.semua.bulanberjalan' );

Route::get (
    '/laporan/semua-total',
    [ LaporanLNPBTotalController::class, 'semuaTotal_index' ]
)->name ( 'laporan.semua.total' );