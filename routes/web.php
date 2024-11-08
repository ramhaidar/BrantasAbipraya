<?php
use App\Http\Controllers\APBController;
use App\Http\Controllers\ATBController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MasterDataAlatController;
use App\Http\Controllers\MasterDataSparepartController;
use App\Http\Controllers\MasterDataSupplierController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\SaldoController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

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

Route::get (
    '/master-data-alat',
    [ MasterDataAlatController::class, 'render' ]
)
    ->middleware ( [ 
        CheckRole::class . ':Admin,Pegawai,Boss',
    ] )
    ->name ( 'master_data_alat' );

Route::post ( '/master-data-alat', [ MasterDataAlatController::class, 'store' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss',
] )
    ->name ( 'master_data_alat.store' );

Route::get ( '/master-data-alat/actions/{id}', [ MasterDataAlatController::class, 'show' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss',
] )

    ->name ( 'master_data_alat.show' );
Route::post ( '/master-data-alat/actions/{id}', [ MasterDataAlatController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss',
] )

    ->name ( 'master_data_alat.update' );
Route::delete ( '/master-data-alat/actions/{id}', [ MasterDataAlatController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss',
] )
    ->name ( 'master_data_alat.destroy' );

Route::get ( '/master-data-supplier', [ MasterDataSupplierController::class, 'index' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss',
] )
    ->name ( 'master_data_supplier' );
Route::post ( '/master-data-supplier', [ MasterDataSupplierController::class, 'store' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss',
] )
    ->name ( 'master_data_supplier.store' );
Route::get ( '/master-data-supplier/actions/{id}', [ MasterDataSupplierController::class, 'show' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss',
] )
    ->name ( 'master_data_supplier.show' );
Route::post ( '/master-data-supplier/actions/{id}', [ MasterDataSupplierController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss',
] )
    ->name ( 'master_data_supplier.update' );
Route::delete ( '/master-data-supplier/actions/{id}', [ MasterDataSupplierController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss',
] )
    ->name ( 'master_data_supplier.destroy' );

Route::get ( '/master-data-sparepart', [ MasterDataSparepartController::class, 'index' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss',
] )
    ->name ( 'master_data_sparepart' );
Route::post ( '/master-data-sparepart', [ MasterDataSparepartController::class, 'store' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss',
] )
    ->name ( 'master_data_sparepart.store' );
Route::get ( '/master-data-sparepart/actions/{id}', [ MasterDataSparepartController::class, 'show' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss',
] )
    ->name ( 'master_data_sparepart.show' );
Route::post ( '/master-data-sparepart/actions/{id}', [ MasterDataSparepartController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss',
] )
    ->name ( 'master_data_sparepart.update' );
Route::delete ( '/master-data-sparepart/actions/{id}', [ MasterDataSparepartController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss',
] )
    ->name ( 'master_data_sparepart.destroy' );

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

// Rute Proyek [ProyekController]
Route::prefix ( 'proyek' )
    ->middleware ( [ CheckRole::class . ':Admin,Pegawai,Boss' ] )
    ->group ( function ()
    {
        Route::get ( '/', [ ProyekController::class, 'index' ] )
            ->name ( 'proyek' );

        Route::get ( '/add', [ ProyekController::class, 'data_baru_proyek' ] )
            ->name ( 'proyek.new' );

        Route::post ( '/store', [ ProyekController::class, 'store' ] )
            ->name ( 'proyek.post.store' );

        Route::get ( '/actions/{id}', [ ProyekController::class, 'showByID' ] )
            ->name ( 'proyek.show' );

        Route::post ( '/edit/actions/{id}', [ ProyekController::class, 'update' ] )
            ->name ( 'proyek.post.update' );

        Route::delete ( '/delete/actions/{id}', [ ProyekController::class, 'destroy' ] )
            ->name ( 'proyek.delete.destroy' );
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

// Pagination Handler
Route::get (
    '/master-data-alat/data',
    [ MasterDataAlatController::class, 'getData' ]
)->middleware ( 'auth' )
    ->name ( 'master-data.alat.getData' );

Route::get (
    '/master-data-sparepart/data',
    [ MasterDataSparepartController::class, 'getData' ]
)->middleware ( 'auth' )
    ->name ( 'master-data.sparepart.getData' );

Route::get (
    '/master-data-supplier/data',
    [ MasterDataSupplierController::class, 'getData' ]
)->middleware ( 'auth' )
    ->name ( 'master-data.supplier.getData' );

Route::get ( '/test', function ()
{
    return view ( 'test' );
} );

Route::middleware ( 'auth' )->group ( function ()
{
    Route::get (
        'master-data-alats/{id}',
        [ MasterDataAlatController::class, 'show' ]
    )->name ( 'master_data_alat.show' );
    Route::post (
        'master-data-alats',
        [ MasterDataAlatController::class, 'store' ]
    )->name ( 'master_data_alat.store' );
    Route::put (
        'master-data-alats/{id}',
        [ MasterDataAlatController::class, 'update' ]
    )->name ( 'master_data_alat.update' );
    Route::delete (
        'master-data-alats/{id}',
        [ MasterDataAlatController::class, 'destroy' ]
    )->name ( 'master_data_alat.destroy' );
} );