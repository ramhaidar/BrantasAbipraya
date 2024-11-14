<?php

namespace App\Models;

use App\Models\User;
use Database\Factories\ProyekFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proyek extends Model
{
    use HasFactory;

    protected $table = 'proyek';

    protected $fillable = [ 
        'nama'
    ];

    protected $casts = [ 
        'id'         => 'integer',
        'nama'       => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function userProyeks ()
    {
        return $this->hasMany ( UserProyek::class, 'id_proyek' );
    }

    public function users ()
    {
        return $this->belongsToMany ( User::class, 'user_proyek', 'id_proyek', 'id_user' );
    }

    public function alat ()
    {
        return $this->hasMany ( Alat::class, 'id_proyek' );
    }

    public function atbs ()
    {
        return $this->hasMany ( ATB::class, 'id_proyek' );
    }

    public function asalAtbs ()
    {
        return $this->hasMany ( ATB::class, 'id_asal_proyek' );
    }

    public function tujuanApbs ()
    {
        return $this->hasMany ( APB::class, 'id_tujuan_proyek' );
    }

    public function apbs ()
    {
        return APB::whereIn ( 'id_saldo', function ($query)
        {
            $query->select ( 'id_saldo' )->from ( 'atb' )->where ( 'id_proyek', $this->id );
        } )->get ();
    }
}
