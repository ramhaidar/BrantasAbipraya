<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterDataSupplier extends Model
{
    protected $table = 'master_data_supplier'; //nama tabel master data

    protected $fillable = [
        'id',
        'supplier',
    ];
}