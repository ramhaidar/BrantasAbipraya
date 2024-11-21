<?php
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APBController;
use App\Http\Controllers\ATBController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SaldoController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RKBUrgentController;
use App\Http\Controllers\RKBGeneralController;
use App\Http\Controllers\MasterDataAlatController;
use App\Http\Controllers\DetailRKBGeneralController;
use App\Http\Controllers\MasterDataSupplierController;
use App\Http\Controllers\MasterDataSparepartController;

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
        ->name ( 'api.showUserByID' );
    Route::post ( '/edit/{user}', [ UserController::class, 'update' ] )
        ->name ( 'user.post.update' );
    Route::post ( '/add', [ UserController::class, 'store' ] )
        ->name ( 'user.post.store' );
    Route::delete ( '/delete/{user}', [ UserController::class, 'destroy' ] )
        ->name ( 'user.delete.destroy' );
} );

// Rute ATB [ATBController]
Route::prefix ( 'atb' )
    ->middleware ( [ CheckRole::class . ':Admin,Pegawai,Boss' ] )
    ->group ( function ()
    {

        Route::get ( '/', [ ATBController::class, 'index' ] )
            ->name ( 'atb' );

        // Hutang Unit Alat Routes
        Route::prefix ( 'hutang_unit_alat' )->group ( function ()
        {
            Route::get ( '/', [ ATBController::class, 'hutang_unit_alat' ] )
                ->name ( 'atb_hutang_unit_alat' );

            Route::get ( '/add', [ ATBController::class, 'data_baru_hutang_unit_alat' ] )
                ->name ( 'atb.new.hutang_unit_alat' );

            Route::get ( '/{atb}', [ ATBController::class, 'showByID' ] )
                ->name ( 'atb.show.hutang_unit_alat_by_id' );

            Route::post ( '/edit/actions/{id}', [ ATBController::class, 'update' ] )
                ->name ( 'atb.post.update.hutang_unit_alat' );

            Route::delete ( '/actions/{id}', [ ATBController::class, 'destroy' ] )
                ->name ( 'atb.delete.destroy.hutang_unit_alat' );
        } );

        // Panjar Unit Alat Routes
        Route::prefix ( 'panjar_unit_alat' )->group ( function ()
        {
            Route::get ( '/', [ ATBController::class, 'panjar_unit_alat' ] )
                ->name ( 'atb_panjar_unit_alat' );

            Route::get ( '/add', [ ATBController::class, 'data_baru_panjar_unit_alat' ] )
                ->name ( 'atb.new.panjar_unit_alat' );

            Route::get ( '/{atb}', [ ATBController::class, 'showByID' ] )
                ->name ( 'atb.show.panjar_unit_alat_by_id' );

            Route::post ( '/edit/actions/{id}', [ ATBController::class, 'update' ] )
                ->name ( 'atb.post.update.panjar_unit_alat' );

            Route::delete ( '/actions/{id}', [ ATBController::class, 'destroy' ] )
                ->name ( 'atb.delete.destroy.panjar_unit_alat' );
        } );

        // Mutasi Proyek Routes
        Route::prefix ( 'mutasi_proyek' )->group ( function ()
        {
            Route::get ( '/', [ ATBController::class, 'mutasi_proyek' ] )
                ->name ( 'atb_mutasi_proyek' );

            Route::get ( '/add', [ ATBController::class, 'data_baru_mutasi_proyek' ] )
                ->name ( 'atb.new.mutasi_proyek' );

            Route::get ( '/{atb}', [ ATBController::class, 'showByID' ] )
                ->name ( 'atb.show.mutasi_proyek_by_id' );

            Route::post ( '/edit/actions/{id}', [ ATBController::class, 'update' ] )
                ->name ( 'atb.post.update.mutasi_proyek' );

            Route::delete ( '/actions/{id}', [ ATBController::class, 'destroy' ] )
                ->name ( 'atb.delete.destroy.mutasi_proyek' );
        } );

        // Panjar Proyek Routes
        Route::prefix ( 'panjar_proyek' )->group ( function ()
        {
            Route::get ( '/', [ ATBController::class, 'panjar_proyek' ] )
                ->name ( 'atb_panjar_proyek' );

            Route::get ( '/add', [ ATBController::class, 'data_baru_panjar_proyek' ] )
                ->name ( 'atb.new.panjar_proyek' );

            Route::get ( '/{atb}', [ ATBController::class, 'showByID' ] )
                ->name ( 'atb.show.panjar_proyek_by_id' );

            Route::post ( '/edit/actions/{id}', [ ATBController::class, 'update' ] )
                ->name ( 'atb.post.update.panjar_proyek' );

            Route::delete ( '/actions/{id}', [ ATBController::class, 'destroy' ] )
                ->name ( 'atb.delete.destroy.panjar_proyek' );
        } );

        // General ATB Routes
        Route::get ( '/add', [ ATBController::class, 'data_baru_atb' ] )
            ->name ( 'atb.new' );

        Route::post ( '/store', [ ATBController::class, 'store' ] )
            ->name ( 'atb.post.store' );

        Route::post ( '/actions/{id}', [ ATBController::class, 'update' ] )
            ->name ( 'atb.post.update' );

        Route::delete ( '/actions/{id}', [ ATBController::class, 'destroy' ] )
            ->name ( 'atb.delete.destroy' );

        Route::get ( '/delatb/actions/{id}', [ ATBController::class, 'destroy' ] )
            ->name ( 'atb.del.test' );

        Route::get ( '/export', [ ATBController::class, 'export' ] )
            ->name ( 'atb.export' );

        Route::post ( '/import', [ ATBController::class, 'import' ] )
            ->name ( 'atb.import.post' );

        Route::get ( '/import', [ ATBController::class, 'showImportForm' ] )
            ->name ( 'atb.import' );
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

Route::get ( '/dashboard/proyek/actions/{id}', [ DashboardController::class, 'filterByProyek' ] )
    ->middleware ( [ 
        CheckRole::class . ':Admin,Pegawai,Boss',
    ] )
    ->name ( 'dashboard.filter' );

// Route untuk mengakses dokumentasi [Security Purpose]
Route::get (
    '/dokumentasi/atb/{filename}',
    [ ATBController::class, 'showDokumentasi' ]
)
    ->name ( 'atb.dokumentasi' )
    ->middleware ( 'auth' );

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
        '/rkb-urgent/data',
        [ RKBUrgentController::class, 'getData' ]
    )
        ->name ( 'rkb_urgent.getData' );
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
            ->name ( 'proyek.update' );

        Route::delete (
            '{id}',
            [ ProyekController::class, 'destroy' ]
        )
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
        )->name ( 'master_data_alat.show' );

        Route::post (
            '/',
            [ MasterDataAlatController::class, 'store' ]
        )->name ( 'master_data_alat.store' );

        Route::put (
            '{id}',
            [ MasterDataAlatController::class, 'update' ]
        )->name ( 'master_data_alat.update' );

        Route::delete (
            '{id}',
            [ MasterDataAlatController::class, 'destroy' ]
        )->name ( 'master_data_alat.destroy' );
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
        )->name ( 'master_data_sparepart.show' );

        Route::post (
            '/',
            [ MasterDataSparepartController::class, 'store' ]
        )->name ( 'master_data_sparepart.store' );

        Route::put (
            '{id}',
            [ MasterDataSparepartController::class, 'update' ]
        )->name ( 'master_data_sparepart.update' );

        Route::delete (
            '{id}',
            [ MasterDataSparepartController::class, 'destroy' ]
        )->name ( 'master_data_sparepart.destroy' );
    } );

Route::get (
    '/spareparts-by-category/{id}',
    [ MasterDataSparepartController::class, 'getSparepartsByCategory' ]
)->name ( 'spareparts-by-category' );


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
        )->name ( 'master_data_supplier.show' );

        Route::post (
            '/',
            [ MasterDataSupplierController::class, 'store' ]
        )->name ( 'master_data_supplier.store' );

        Route::put (
            '{id}',
            [ MasterDataSupplierController::class, 'update' ]
        )->name ( 'master_data_supplier.update' );

        Route::delete (
            '{id}',
            [ MasterDataSupplierController::class, 'destroy' ]
        )->name ( 'master_data_supplier.destroy' );
    } );

// Rute RKB General [RKBGeneralController]
Route::middleware ( 'auth' )
    ->prefix ( 'rkb-general' )
    ->group ( function ()
    {
        Route::get (
            '/',
            [ RKBGeneralController::class, 'index' ]
        )->name ( 'rkb_general.index' );

        Route::get (
            '/detail/{id}',
            [ DetailRKBGeneralController::class, 'index' ]
        )->name ( 'rkb_general.detail.index' );

        Route::get (
            '{id}',
            [ RKBGeneralController::class, 'show' ]
        )->name ( 'rkb_general.show' );

        Route::post (
            '/',
            [ RKBGeneralController::class, 'store' ]
        )->name ( 'rkb_general.store' );

        Route::put (
            '{id}',
            [ RKBGeneralController::class, 'update' ]
        )->name ( 'rkb_general.update' );

        Route::delete (
            '{id}',
            [ RKBGeneralController::class, 'destroy' ]
        )->name ( 'rkb_general.destroy' );

        Route::post (
            '/finalize/{id}',
            [ RKBGeneralController::class, 'finalize' ]
        )->name ( 'rkb_general.finalize' );

        // Rute Detail RKB General [DetailRKBGeneralController]
        Route::post (
            '/detail',
            [ DetailRKBGeneralController::class, 'store' ]
        )->name ( 'rkb_general.detail.store' );

        Route::get (
            '/detail/show/{id}',
            [ DetailRKBGeneralController::class, 'show' ]
        )->name ( 'rkb_general.detail.show' );

        Route::put (
            '/detail/{id}',
            [ DetailRKBGeneralController::class, 'update' ]
        )->name ( 'rkb_general.detail.update' );

        Route::delete (
            '/detail/{id}',
            [ DetailRKBGeneralController::class, 'destroy' ]
        )->name ( 'rkb_general.detail.destroy' );
    } );


// Rute RKB Urgent [RKBUrgentController]
Route::middleware ( 'auth' )
    ->prefix ( 'rkb-urgent' )
    ->group ( function ()
    {
        Route::get (
            '/',
            [ RKBUrgentController::class, 'index' ]
        )->name ( 'rkb_urgent.index' );

        Route::get (
            '{id}',
            [ RKBUrgentController::class, 'show' ]
        )->name ( 'rkb_urgent.show' );

        Route::post (
            '/',
            [ RKBUrgentController::class, 'store' ]
        )->name ( 'rkb_urgent.store' );

        Route::put (
            '{id}',
            [ RKBUrgentController::class, 'update' ]
        )->name ( 'rkb_urgent.update' );

        Route::delete (
            '{id}',
            [ RKBUrgentController::class, 'destroy' ]
        )->name ( 'rkb_urgent.destroy' );
    } );
