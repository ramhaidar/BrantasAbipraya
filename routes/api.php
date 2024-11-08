<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MasterDataAlatController;
use App\Http\Controllers\Api\MasterDataSupplierController;
use App\Http\Controllers\Api\MasterDataSparepartController;
use App\Http\Controllers\Api\LinkSupplierSparepartController;

Route::get ( '/user', function (Request $request)
{
    return $request->user ();
} )->middleware ( 'auth:sanctum' );

// MasterData Sparepart API
Route::apiResource ( 'master-data-spareparts', MasterDataSparepartController::class)->names ( [ 
    'index'   => 'spareparts.index',
    'store'   => 'spareparts.store',
    'show'    => 'spareparts.show',
    'update'  => 'spareparts.update',
    'destroy' => 'spareparts.destroy',
] );

// MasterData Supplier API
Route::apiResource ( 'master-data-suppliers', MasterDataSupplierController::class)->names ( [ 
    'index'   => 'suppliers.index',
    'store'   => 'suppliers.store',
    'show'    => 'suppliers.show',
    'update'  => 'suppliers.update',
    'destroy' => 'suppliers.destroy',
] );

// Link Supplier Sparepart API
Route::apiResource ( 'link-supplier-spareparts', LinkSupplierSparepartController::class)->names ( [ 
    'index'   => 'link-suppliers.index',
    'store'   => 'link-suppliers.store',
    'show'    => 'link-suppliers.show',
    'update'  => 'link-suppliers.update',
    'destroy' => 'link-suppliers.destroy',
] );