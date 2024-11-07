<?php
use App\Http\Controllers\MasterDataSparepartController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APBController;
use App\Http\Controllers\ATBController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\SaldoController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SessionContrroller;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerbaikanController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\PemeliharaanController;
use App\Http\Controllers\MasterDataAlatController;
use App\Http\Controllers\MasterDataSupplierController;

Route::get ( '/', function ()
{
    return view ( '/prelogin' );
} )->middleware ( 'guest' );

Route::get ( '/login', [ SessionContrroller::class, 'index' ] )
    ->middleware ( 'guest' )
    ->name ( 'login' );
Route::post ( '/login', [ SessionContrroller::class, 'login' ] )
    ->middleware ( 'guest' );
Route::post ( '/logout', [ SessionContrroller::class, 'logout' ] )
    ->middleware ( [ 
        CheckRole::class . ':Admin,Pegawai,Boss'
    ] );

Route::get ( '/recaptcha', [ SessionContrroller::class, 'reloadCaptcha' ] );

Route::get ( '/dashboard', [ DashboardController::class, 'index' ] )
    ->middleware ( [ 
        CheckRole::class . ':Admin,Pegawai,Boss'
    ] )
    ->name ( 'dashboard' );

Route::get ( '/users', [ UserController::class, 'index' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );
Route::get ( '/users/{user}', [ UserController::class, 'showByID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'api.showUserByID' );
Route::post ( '/users/edit/{user}', [ UserController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );
Route::post ( '/users/add', [ UserController::class, 'store' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );
Route::delete ( '/users/delete/{user}', [ UserController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );

Route::get ( '/atb', [ ATBController::class, 'index' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'atb' );
Route::get ( '/atb/hutang_unit_alat', [ ATBController::class, 'hutang_unit_alat' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'atb_hutang_unit_alat' );
Route::get ( '/atb/hutang_unit_alat/add', [ ATBController::class, 'data_baru_hutang_unit_alat' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'data_baru_hutang_unit_alat' );
Route::get ( '/atb/hutang_unit_alat/{atb}', [ ATBController::class, 'showByID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'atb.showATBByID' );
Route::post ( '/atb/hutang_unit_alat/edit/actions/{id}', [ ATBController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );
Route::delete ( '/atb/hutang_unit_alat/actions/{id}', [ ATBController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );

Route::get ( '/atb/panjar_unit_alat', [ ATBController::class, 'panjar_unit_alat' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'atb_panjar_unit_alat' );
Route::get ( '/atb/panjar_unit_alat/add', [ ATBController::class, 'data_baru_panjar_unit_alat' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'data_baru_panjar_unit_alat' );
Route::get ( '/atb/panjar_unit_alat/{atb}', [ ATBController::class, 'showByID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'atb.showATBByID' );
Route::post ( '/atb/panjar_unit_alat/edit/actions/{id}', [ ATBController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );
Route::delete ( '/atb/panjar_unit_alat/actions/{id}', [ ATBController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );

Route::get ( '/atb/mutasi_proyek', [ ATBController::class, 'mutasi_proyek' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'atb_mutasi_proyek' );
Route::get ( '/atb/mutasi_proyek/add', [ ATBController::class, 'data_baru_mutasi_proyek' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'data_baru_mutasi_proyek' );
Route::get ( '/atb/mutasi_proyek/{atb}', [ ATBController::class, 'showByID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'atb.showATBByID' );
Route::post ( '/atb/mutasi_proyek/edit/actions/{id}', [ ATBController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );
Route::delete ( '/atb/mutasi_proyek/actions/{id}', [ ATBController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );

Route::get ( '/atb/panjar_proyek', [ ATBController::class, 'panjar_proyek' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'atb_panjar_proyek' );
Route::get ( '/atb/panjar_proyek/add', [ ATBController::class, 'data_baru_panjar_proyek' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'data_baru_panjar_proyek' );
Route::get ( '/atb/panjar_proyek/{atb}', [ ATBController::class, 'showByID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'atb.showATBByID' );
Route::post ( '/atb/panjar_proyek/edit/actions/{id}', [ ATBController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );
Route::delete ( '/atb/panjar_proyek/actions/{id}', [ ATBController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );

Route::get ( '/atb/add', [ ATBController::class, 'data_baru_atb' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'data_baru_atb' );
Route::post ( '/atb/store', [ ATBController::class, 'store' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'atb.store' );
Route::put ( '/atb/actions/{id}', [ ATBController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'atb.update' );
Route::delete ( '/atb/actions/{id}', [ ATBController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'atb.destroy' );
Route::get ( '/delatb/actions/{id}', [ ATBController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'atb.del.test' );

Route::get ( '/atb/export', [ ATBController::class, 'export' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'atb.export' );
Route::post ( '/atb/import', [ ATBController::class, 'import' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'atb.import' );
Route::get ( '/atb/import', [ ATBController::class, 'showImportForm' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'atb.import.form' );
Route::get ( '/saldo/export', [ SaldoController::class, 'exportSaldo' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'saldo.export' );

Route::get ( '/nilai/{nilai}', [ NilaiController::class, 'showById' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'api.nilai.showById' );
Route::post ( '/nilai/{nilai}', [ NilaiController::class, 'update' ] )->middleware ( [ 'CheckRole:Admin,Pegawai' ] );

Route::get ( '/perbaikan/{perbaikan}', [ PerbaikanController::class, 'showById' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'api.perbaikan.showById' );
Route::post ( '/perbaikan/{perbaikan}', [ PerbaikanController::class, 'update' ] )->middleware ( [ 'CheckRole:Admin,Pegawai' ] );

Route::get ( '/pemeliharaan/{pemeliharaan}', [ PemeliharaanController::class, 'showById' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'api.pemeliharaan.showById' );
Route::post ( '/maintenance-kit/{pemeliharaan}', [ PemeliharaanController::class, 'updateMaintenanceKit' ] )->middleware ( [ 'CheckRole:Admin,Pegawai' ] )->name ( 'maintenanceKit.update' );
Route::post ( '/oil-lubricants/{pemeliharaan}', [ PemeliharaanController::class, 'updateOilLubricants' ] )->middleware ( [ 'CheckRole:Admin,Pegawai' ] )->name ( 'oilLubricants.update' );

Route::get ( '/apb', [ APBController::class, 'index' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb' );
Route::get ( '/apb/ex_panjar_unit_alat', [ APBController::class, 'ex_panjar_unit_alat' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb_ex_panjar_unit_alat' );
Route::get ( '/apb/ex_panjar_unit_alat/add', [ APBController::class, 'data_baru_ex_panjar_unit_alat' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'data_baru_ex_panjar_unit_alat' );
Route::get ( '/apb/ex_panjar_unit_alat/{atb}', [ APBController::class, 'showByID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb.showAPBByID' );
Route::get ( '/apb/ex_panjar_unit_alat/apb/{apb}', [ APBController::class, 'getAPBbyID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb.showAPBByID' );
Route::post ( '/apb/ex_panjar_unit_alat/edit/actions/{id}', [ APBController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );
Route::delete ( '/apb/ex_panjar_unit_alat/actions/{id}', [ APBController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );

Route::get ( '/apb/ex_panjar_proyek', [ APBController::class, 'ex_panjar_proyek' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb_ex_panjar_proyek' );
Route::get ( '/apb/ex_panjar_proyek/add', [ APBController::class, 'data_baru_ex_panjar_proyek' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'data_baru_ex_panjar_proyek' );
Route::get ( '/apb/ex_panjar_proyek/{atb}', [ APBController::class, 'showByID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb.showAPBByID' );
Route::get ( '/apb/ex_panjar_proyek/apb/{apb}', [ APBController::class, 'getAPBbyID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb.showAPBByID' );
Route::post ( '/apb/ex_panjar_proyek/edit/actions/{id}', [ APBController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );
Route::delete ( '/apb/ex_panjar_proyek/actions/{id}', [ APBController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );

Route::get ( '/apb/ex_unit_alat', [ APBController::class, 'ex_unit_alat' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb_ex_unit_alat' );
Route::get ( '/apb/ex_unit_alat/add', [ APBController::class, 'data_baru_ex_unit_alat' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'data_baru_ex_unit_alat' );
Route::get ( '/apb/ex_unit_alat/{atb}', [ APBController::class, 'showByID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb.showAPBByID' );
Route::get ( '/apb/ex_unit_alat/apb/{apb}', [ APBController::class, 'getAPBbyID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb.showAPBByID' );
Route::post ( '/apb/ex_unit_alat/edit/actions/{id}', [ APBController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );
Route::delete ( '/apb/ex_unit_alat/actions/{id}', [ APBController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );

Route::get ( '/apb/ex_mutasi_saldo', [ APBController::class, 'ex_mutasi_saldo' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb_ex_mutasi_saldo' );
Route::get ( '/apb/ex_mutasi_saldo/add', [ APBController::class, 'data_baru_ex_mutasi_saldo' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'data_baru_ex_mutasi_saldo' );
Route::get ( '/apb/ex_mutasi_saldo/{atb}', [ APBController::class, 'showByID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb.showAPBByID' );
Route::get ( '/apb/ex_mutasi_saldo/apb/{apb}', [ APBController::class, 'getAPBbyID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb.showAPBByID' );
Route::post ( '/apb/ex_mutasi_saldo/edit/actions/{id}', [ APBController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );
Route::delete ( '/apb/ex_mutasi_saldo/actions/{id}', [ APBController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );

Route::get ( '/apb/add', [ APBController::class, 'data_baru_apb' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'data_baru_apb' );
Route::post ( '/apb/store', [ APBController::class, 'store' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb.store' );
Route::get ( '/apb/actions/{id}', [ APBController::class, 'show' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb.show' );
Route::put ( '/apb/actions/{id}', [ APBController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb.update' );
Route::delete ( '/apb/actions/{id}', [ APBController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb.destroy' );
Route::get ( '/delapb/actions/{id}', [ APBController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'apb.del.test' );

Route::get ( '/master-data-alat', [ MasterDataAlatController::class, 'index' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'master_data_alat' );
Route::post ( '/master-data-alat', [ MasterDataAlatController::class, 'store' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'master_data_alat.store' );

Route::get ( '/master-data-alat/actions/{id}', [ MasterDataAlatController::class, 'show' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )

    ->name ( 'master_data_alat.show' );
Route::post ( '/master-data-alat/actions/{id}', [ MasterDataAlatController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )

    ->name ( 'master_data_alat.update' );
Route::delete ( '/master-data-alat/actions/{id}', [ MasterDataAlatController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'master_data_alat.destroy' );

Route::get ( '/master-data-supplier', [ MasterDataSupplierController::class, 'index' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'master_data_supplier' );
Route::post ( '/master-data-supplier', [ MasterDataSupplierController::class, 'store' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'master_data_supplier.store' );
Route::get ( '/master-data-supplier/actions/{id}', [ MasterDataSupplierController::class, 'show' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'master_data_supplier.show' );
Route::post ( '/master-data-supplier/actions/{id}', [ MasterDataSupplierController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'master_data_supplier.update' );
Route::delete ( '/master-data-supplier/actions/{id}', [ MasterDataSupplierController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'master_data_supplier.destroy' );

Route::get ( '/master-data-sparepart', [ MasterDataSparepartController::class, 'index' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'master_data_sparepart' );
Route::post ( '/master-data-sparepart', [ MasterDataSparepartController::class, 'store' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'master_data_sparepart.store' );
Route::get ( '/master-data-sparepart/actions/{id}', [ MasterDataSparepartController::class, 'show' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'master_data_sparepart.show' );
Route::post ( '/master-data-sparepart/actions/{id}', [ MasterDataSparepartController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'master_data_sparepart.update' );
Route::delete ( '/master-data-sparepart/actions/{id}', [ MasterDataSparepartController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'master_data_sparepart.destroy' );

Route::get ( '/master_data', [ MasterDataController::class, 'index' ] )
    ->middleware ( [ 
        CheckRole::class . ':Admin,Pegawai,Boss'
    ] )
    ->name ( 'master_data' );

Route::post ( '/master_data', [ MasterDataController::class, 'store' ] )
    ->middleware ( middleware: [ 
        CheckRole::class . ':Admin,Pegawai,Boss'
    ] )
    ->name ( 'master_data.store' );

Route::get ( '/master_data/actions/{id}', [ MasterDataController::class, 'show' ] )
    ->middleware ( [ 
        CheckRole::class . ':Admin,Pegawai,Boss'
    ] )
    ->name ( 'master_data.show' );

Route::put ( '/master_data/actions/{id}', [ MasterDataController::class, 'update' ] )
    ->middleware ( [ 
        CheckRole::class . ':Admin,Pegawai,Boss'
    ] )
    ->name ( 'master_data.update' );

Route::delete ( '/master_data/actions/{id}', [ MasterDataController::class, 'destroy' ] )
    ->middleware ( [ 
        CheckRole::class . ':Admin,Pegawai,Boss'
    ] )
    ->name ( 'master_data.destroy' );

Route::get ( '/saldo', [ SaldoController::class, 'index' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'saldo' );
Route::get ( '/saldo/ex_panjar_unit_alat', [ SaldoController::class, 'ex_panjar_unit_alat' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'saldo_ex_panjar_unit_alat' );
Route::get ( '/saldo/ex_panjar_unit_alat/add', [ SaldoController::class, 'data_baru_ex_panjar_unit_alat' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'data_baru_ex_panjar_unit_alat' );
Route::get ( '/saldo/ex_panjar_unit_alat/{saldo}', [ SaldoController::class, 'showByID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'saldo.showSaldoByID' );
Route::post ( '/saldo/ex_panjar_unit_alat/edit/actions/{id}', [ SaldoController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );
Route::delete ( '/saldo/ex_panjar_unit_alat/actions/{id}', [ SaldoController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );

Route::get ( '/saldo/ex_panjar_proyek', [ SaldoController::class, 'ex_panjar_proyek' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'saldo_ex_panjar_proyek' );
Route::get ( '/saldo/ex_panjar_proyek/add', [ SaldoController::class, 'data_baru_ex_panjar_proyek' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'data_baru_ex_panjar_proyek' );
Route::get ( '/saldo/ex_panjar_proyek/{saldo}', [ SaldoController::class, 'showByID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'saldo.showSaldoByID' );
Route::post ( '/saldo/ex_panjar_proyek/edit/actions/{id}', [ SaldoController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );
Route::delete ( '/saldo/ex_panjar_proyek/actions/{id}', [ SaldoController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );

Route::get ( '/saldo/ex_unit_alat', [ SaldoController::class, 'ex_unit_alat' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'saldo_ex_unit_alat' );
Route::get ( '/saldo/ex_unit_alat/add', [ SaldoController::class, 'data_baru_ex_unit_alat' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'data_baru_ex_unit_alat' );
Route::get ( '/saldo/ex_unit_alat/{saldo}', [ SaldoController::class, 'showByID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'saldo.showSaldoByID' );
Route::post ( '/saldo/ex_unit_alat/edit/actions/{id}', [ SaldoController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );
Route::delete ( '/saldo/ex_unit_alat/actions/{id}', [ SaldoController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );

Route::get ( '/saldo/ex_mutasi_saldo', [ SaldoController::class, 'ex_mutasi_saldo' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'saldo_ex_mutasi_saldo' );
Route::get ( '/saldo/ex_mutasi_saldo/add', [ SaldoController::class, 'data_baru_ex_mutasi_saldo' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'data_baru_ex_mutasi_saldo' );
Route::get ( '/saldo/ex_mutasi_saldo/{saldo}', [ SaldoController::class, 'showByID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'saldo.showSaldoByID' );
Route::post ( '/saldo/ex_mutasi_saldo/edit/actions/{id}', [ SaldoController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );
Route::delete ( '/saldo/ex_mutasi_saldo/actions/{id}', [ SaldoController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] );

Route::get ( '/saldo/add', [ SaldoController::class, 'data_baru_saldo' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'data_baru_saldo' );
Route::post ( '/saldo/store', [ SaldoController::class, 'store' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'saldo.store' );
Route::get ( '/saldo/actions/{id}', [ SaldoController::class, 'show' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'saldo.show' );
Route::put ( '/saldo/actions/{id}', [ SaldoController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'saldo.update' );
Route::delete ( '/saldo/actions/{id}', [ SaldoController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'saldo.destroy' );
Route::get ( '/delsaldo/actions/{id}', [ SaldoController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'saldo.del.test' );

Route::get ( '/proyek', [ ProyekController::class, 'index' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'proyek' );
Route::get ( '/proyek/add', [ ProyekController::class, 'data_baru_proyek' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'data_baru_proyek' );
Route::post ( '/proyek/store', [ ProyekController::class, 'store' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'proyek.store' );
Route::get ( '/proyek/actions/{id}', [ ProyekController::class, 'showByID' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'proyek.show' );
Route::post ( '/proyek/edit/actions/{id}', [ ProyekController::class, 'update' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'proyek.update' );
Route::delete ( '/proyek/delete/actions/{id}', [ ProyekController::class, 'destroy' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'proyek.destroy' );

Route::get ( '/laporan/summary', [ LaporanController::class, 'summary' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
] )
    ->name ( 'laporan.summary' );
Route::get ( '/laporan/lnpb', [ LaporanController::class, 'LNPB' ] )->middleware ( [ 
    CheckRole::class . ':Admin,Pegawai,Boss'
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
        CheckRole::class . ':Admin,Pegawai,Boss'
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