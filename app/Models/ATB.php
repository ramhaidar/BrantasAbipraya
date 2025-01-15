<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ATB extends Model
{
    use HasFactory;

    protected $table = 'atb';

    protected $fillable = [ 
        'tipe',
        'dokumentasi_foto',
        'surat_tanda_terima',
        'tanggal',
        'quantity',
        'harga',
        'id_proyek',
        'id_asal_proyek',
        'id_apb_mutasi', // Add this
        'id_spb',
        'id_detail_spb',
        'id_master_data_sparepart',
        'id_master_data_supplier',
    ];

    protected $casts = [ 
        'id'                       => 'integer',
        'tipe'                     => 'string',
        'dokumentasi_foto'         => 'string', // Changed from json to string
        'surat_tanda_terima'       => 'string',
        'tanggal'                  => 'date',
        'quantity'                 => 'integer',
        'harga'                    => 'integer',
        'id_proyek'                => 'integer',
        'id_asal_proyek'           => 'integer',
        'id_apb_mutasi'            => 'integer', // Add this
        'id_spb'                   => 'integer',
        'id_detail_spb'            => 'integer',
        'id_master_data_sparepart' => 'integer',
        'id_master_data_supplier'  => 'integer',
    ];

    public function proyek () : BelongsTo
    {
        return $this->belongsTo ( Proyek::class, 'id_proyek' );
    }

    public function asalProyek () : BelongsTo
    {
        return $this->belongsTo ( Proyek::class, 'id_asal_proyek' );
    }

    public function spb () : BelongsTo
    {
        return $this->belongsTo ( SPB::class, 'id_spb' );
    }

    public function detailSpb () : BelongsTo
    {
        return $this->belongsTo ( DetailSPB::class, 'id_detail_spb' );
    }

    public function masterDataSparepart () : BelongsTo
    {
        return $this->belongsTo ( MasterDataSparepart::class, 'id_master_data_sparepart' );
    }

    public function masterDataSupplier () : BelongsTo
    {
        return $this->belongsTo ( MasterDataSupplier::class, 'id_master_data_supplier' );
    }

    public function saldo ()
    {
        return $this->hasOne ( Saldo::class, 'id_atb' );
    }

    public function apbMutasi(): BelongsTo
    {
        return $this->belongsTo(APB::class, 'id_apb_mutasi');
    }
}
