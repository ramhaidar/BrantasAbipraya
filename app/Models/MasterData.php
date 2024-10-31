<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterData extends Model
{
    use HasFactory;

    protected $table = 'master_data'; // Nama tabel baru

    protected $fillable = [ 
        'supplier',
        'sparepart',
        'part_number',
        'buffer_stock',
        'id_user',
    ];

    // Relasi ke ATB
    public function atbs ()
    {
        return $this->hasMany ( ATB::class, 'id_master_data' );
    }

}
