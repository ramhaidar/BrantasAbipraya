<?php

namespace App\Models;

use App\Models\APB;
use App\Models\ATB;
use App\Models\Alat;
use App\Models\User;
use App\Models\UserProyek;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function atbs () : HasMany
    {
        return $this->hasMany ( ATB::class, 'id_proyek' );
    }

    public function asalAtbs () : HasMany
    {
        return $this->hasMany ( ATB::class, 'id_asal_proyek' );
    }

    public function tujuanApbs ()
    {
        return $this->hasMany ( APB::class, 'id_tujuan_proyek' );
    }

    public function apbs () : HasMany
    {
        return $this->hasMany ( APB::class, 'id_proyek' );
    }

    public function masterDataAlats ()
    {
        return $this->belongsToMany ( MasterDataAlat::class, 'alat_proyek', 'id_proyek', 'id_alat' )
            ->withPivot ( 'assigned_at', 'removed_at' )
            ->withTimestamps ();
    }

    public function currentAlats ()
    {
        return $this->hasMany ( MasterDataAlat::class, 'id_proyek_current' );
    }

    public function saldos () : HasMany
    {
        return $this->hasMany ( Saldo::class, 'id_proyek' );
    }

    public function asalSaldo () : HasMany
    {
        return $this->hasMany ( Saldo::class, 'id_asal_proyek' );
    }
}
