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
