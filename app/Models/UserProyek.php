<?php

namespace App\Models;

use App\Models\User;
use App\Models\Proyek;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProyek extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika berbeda dengan nama model (dalam kasus ini, nama tabel 'user_project')
    protected $table = 'user_proyek';

    // Tentukan atribut yang dapat diisi (fillable)
    protected $fillable = [ 
        'id_user',
        'id_proyek',
    ];

    // Tentukan relasi ke model User dan Proyek
    public function user ()
    {
        return $this->belongsTo ( User::class, 'id_user' );
    }

    public function proyek ()
    {
        return $this->belongsTo ( Proyek::class, 'id_proyek' );
    }
}

