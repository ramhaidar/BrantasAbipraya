<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class APB extends Model
{
    use HasFactory;

    protected $table = 'apb';

    protected $fillable = [ 
        'tipe',
        'tanggal',
        'mekanik',
        'quantity',
        'status',
        'id_saldo',
        'id_proyek',
        'id_tujuan_proyek',
        'id_master_data_sparepart',
        'id_master_data_supplier',
        'id_alat_proyek'
    ];

    protected $casts = [ 
        'id'                       => 'integer',
        'tipe'                     => 'string',
        'tanggal'                  => 'date',
        'mekanik'                  => 'string',
        'quantity'                 => 'integer',
        'status'                   => 'string',
        'id_saldo'                 => 'integer',
        'id_proyek'                => 'integer',
        'id_tujuan_proyek'         => 'integer',
        'id_master_data_sparepart' => 'integer',
        'id_master_data_supplier'  => 'integer',
        'id_alat_proyek'           => 'integer'
    ];

    public function saldo () : BelongsTo
    {
        return $this->belongsTo ( Saldo::class, 'id_saldo' );
    }

    public function proyek () : BelongsTo
    {
        return $this->belongsTo ( Proyek::class, 'id_proyek' );
    }

    public function masterDataSparepart () : BelongsTo
    {
        return $this->belongsTo ( MasterDataSparepart::class, 'id_master_data_sparepart' );
    }

    public function masterDataSupplier () : BelongsTo
    {
        return $this->belongsTo ( MasterDataSupplier::class, 'id_master_data_supplier' );
    }

    public function alatProyek () : BelongsTo
    {
        return $this->belongsTo ( AlatProyek::class, 'id_alat_proyek' );
    }

    public function tujuanProyek () : BelongsTo
    {
        return $this->belongsTo ( Proyek::class, 'id_tujuan_proyek' );
    }

    public function atbMutasi () : HasOne
    {
        return $this->hasOne ( ATB::class, 'id_apb_mutasi' );
    }
}
