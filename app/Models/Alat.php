<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    use HasFactory;
    protected $table = 'alat';
    protected $fillable = [ 
        'nama_proyek',
        'jenis_alat',
        'kode_alat',
        'merek_alat',
        'tipe_alat',
        'id_proyek',
        'id_user',
    ];

    public function proyek ()
    {
        return $this->belongsTo ( Proyek::class, 'id_proyek' );
    }

    public function user ()
    {
        return $this->belongsTo ( User::class, 'id_user' );
    }
}

