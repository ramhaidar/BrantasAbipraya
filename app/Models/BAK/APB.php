<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APB extends Model
{
    use HasFactory;
    protected $table = 'apb';
    protected $fillable = [ 
        'tanggal',
        'quantity',
        'dokumentasi',
        'id_alat',
        'id_saldo',
        'id_tujuan_proyek',
    ];

    protected $casts = [ 
        'tanggal' => 'date:Y-m-d',
    ];

    public function alat ()
    {
        return $this->belongsTo ( Alat::class, 'id_alat' );
    }

    public function saldo ()
    {
        return $this->belongsTo ( Saldo::class, 'id_saldo' );
    }

    public function tujuanProyek ()
    {
        return $this->belongsTo ( Proyek::class, 'id_tujuan_proyek' );
    }
}

