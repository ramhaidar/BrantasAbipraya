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
        'satuan',
        'quantity',
        'harga',
        'id_atb',
        'id_proyek',
        'id_asal_proyek',
        'id_spb',
        'id_master_data_sparepart',
        'id_master_data_supplier',
    ];

    protected $casts = [ 
        'id'                       => 'integer',
        'tipe'                     => 'string',
        'satuan'                   => 'string',
        'quantity'                 => 'integer',
        'harga'                    => 'integer',
        'id_atb'                   => 'integer',
        'id_proyek'                => 'integer',
        'id_asal_proyek'           => 'integer',
        'id_spb'                   => 'integer',
        'id_master_data_sparepart' => 'integer',
        'id_master_data_supplier'  => 'integer',
        'created_at'               => 'datetime',
        'updated_at'               => 'datetime',
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

    public function atb ()
    {
        return $this->belongsTo ( ATB::class, 'id_atb' );
    }

    public function apb ()
    {
        return $this->hasOne ( APB::class, 'id_saldo' );
    }
}
