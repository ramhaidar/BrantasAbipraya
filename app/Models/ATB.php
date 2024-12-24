<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ATB extends Model
{
    use HasFactory;

    protected $table = 'atb';

    protected $fillable = [ 
        'tipe',
        'tanggal',
        'dokumentasi',
        'quantity',
        'satuan',
        'harga',
        'id_spb',
        'id_saldo',
        'id_proyek',
        'id_master_data_alat',
        'id_asal_proyek',
    ];

    protected $casts = [ 
        'id'                  => 'integer',
        'tipe'                => 'string',
        'tanggal'             => 'date',
        'dokumentasi'         => 'string',
        'quantity'            => 'integer',
        'satuan'              => 'string',
        'harga'               => 'integer',
        'id_spb'              => 'integer',
        'id_saldo'            => 'integer',
        'id_proyek'           => 'integer',
        'id_master_data_alat' => 'integer',
        'id_asal_proyek'      => 'integer',
    ];

    public function spb ()
    {
        return $this->belongsTo ( SPB::class, 'id_spb' );
    }

    public function saldo ()
    {
        return $this->belongsTo ( Saldo::class, 'id_saldo' );
    }

    public function proyek ()
    {
        return $this->belongsTo ( Proyek::class, 'id_proyek' );
    }

    public function masterDataAlat ()
    {
        return $this->belongsTo ( MasterDataAlat::class, 'id_master_data_alat' );
    }

    public function asalProyek ()
    {
        return $this->belongsTo ( Proyek::class, 'id_asal_proyek' );
    }
}
