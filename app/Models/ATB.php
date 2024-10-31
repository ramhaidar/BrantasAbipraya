<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ATB extends Model
{
    use HasFactory;

    protected $table = 'atb';

    protected $fillable = [ 
        'tipe',
        'tanggal',
        'quantity',
        'dokumentasi',
        'satuan',
        'harga',
        'net',
        'ppn',
        'bruto',
        'id_komponen',
        'id_saldo',
        'id_proyek',
        'id_asal_proyek',
        'id_master_data',
    ];

    protected $casts = [ 
        'tanggal' => 'date:Y-m-d',
    ];

    public function komponen ()
    {
        return $this->belongsTo ( Komponen::class, 'id_komponen' );
    }

    public function saldo ()
    {
        return $this->belongsTo ( Saldo::class, 'id_saldo' );
    }

    public function proyek ()
    {
        return $this->belongsTo ( Proyek::class, 'id_proyek' );
    }

    public function asalProyek ()
    {
        return $this->belongsTo ( Proyek::class, 'id_asal_proyek' );
    }

    public function apb ()
    {
        return $this->hasManyThrough ( APB::class, Saldo::class, 'id', 'id_saldo', 'id_saldo', 'id' );
    }

    public function masterData ()
    {
        return $this->belongsTo ( MasterData::class, 'id_master_data' );
    }
}
