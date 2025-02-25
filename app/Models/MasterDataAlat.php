<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterDataAlat extends Model
{
    use HasFactory;

    protected $table = 'master_data_alat';

    protected $fillable = [ 
        'jenis_alat',
        'kode_alat',
        'merek_alat',
        'tipe_alat',
        'serial_number',
        'id_proyek_current',
    ];

    protected $casts = [ 
        'id'                => 'integer',
        'jenis_alat'        => 'string',
        'kode_alat'         => 'string',
        'merek_alat'        => 'string',
        'tipe_alat'         => 'string',
        'serial_number'     => 'string',
        'id_proyek_current' => 'integer',
    ];

    public function detailRkbGenerals () : HasMany
    {
        return $this->hasMany ( DetailRkbGeneral::class, 'id_master_data_alat' );
    }

    public function detailRkbUrgents () : HasMany
    {
        return $this->hasMany ( DetailRkbUrgent::class, 'id_master_data_alat' );
    }

    public function linkAlatDetailRkbs () : HasMany
    {
        return $this->hasMany ( LinkAlatDetailRKB::class, 'id_master_data_alat' );
    }

    public function detailSpbs () : HasMany
    {
        return $this->hasMany ( DetailSPB::class, 'id_master_data_alat' );
    }

    public function proyekCurrent () : BelongsTo
    {
        return $this->belongsTo ( Proyek::class, 'id_proyek_current', 'id' );
    }

    public function alatProyek ()
    {
        return $this->hasMany ( AlatProyek::class, 'id_master_data_alat' );
    }

    public function proyeks ()
    {
        return $this->belongsToMany ( Proyek::class, 'alat_proyek', 'id_alat', 'id_proyek' )
            ->withPivot ( 'assigned_at', 'removed_at' )
            ->withTimestamps ();
    }

    public function getCurrentProjectAttribute ()
    {
        return $this->alatProyek ()
            ->whereNull ( 'removed_at' )
            ->latest ( 'assigned_at' )
            ->first ()?->proyek;
    }

}
