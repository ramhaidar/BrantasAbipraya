<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    use HasFactory;

    protected $table = 'saldo';

    protected $fillable = [ 
        'tipe',
        'quantity',
        'harga',
        'id_proyek',
        'id_asal_proyek',
        'id_spb',
        'id_master_data_sparepart',
        'id_master_data_supplier',
    ];

    protected $casts = [ 
        'id'         => 'integer',
        'quantity'   => 'integer',
        'harga'      => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function proyek ()
    {
        return $this->belongsTo ( Proyek::class, 'id_proyek' );
    }

    public function asalProyek ()
    {
        return $this->belongsTo ( Proyek::class, 'id_asal_proyek' );
    }

    public function spb ()
    {
        return $this->belongsTo ( Spb::class, 'id_spb' );
    }

    public function masterDataSparepart ()
    {
        return $this->belongsTo ( MasterDataSparepart::class, 'id_master_data_sparepart' );
    }

    public function masterDataSupplier ()
    {
        return $this->belongsTo ( MasterDataSupplier::class, 'id_master_data_supplier' );
    }
}
