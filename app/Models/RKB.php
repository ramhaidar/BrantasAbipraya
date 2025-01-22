<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RKB extends Model
{
    use HasFactory;

    protected $table = 'rkb';

    protected $fillable = [ 
        'nomor',
        'periode',
        'tipe',
        'id_proyek',
        'is_finalized',
        'is_evaluated',
        'is_approved_vp',
        'is_approved_svp',
        'vp_approved_at',
        'svp_approved_at',
    ];

    protected $casts = [ 
        'id'              => 'integer',
        'nomor'           => 'string',
        'periode'         => 'date',
        'tipe'            => 'string',
        'is_finalized'    => 'boolean',
        'is_evaluated'    => 'boolean',
        'is_approved_vp'  => 'boolean',
        'is_approved_svp' => 'boolean',
        'vp_approved_at'  => 'datetime',
        'svp_approved_at' => 'datetime',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    public function proyek () : BelongsTo
    {
        return $this->belongsTo ( Proyek::class, 'id_proyek' );
    }

    public function linkAlatDetailRkbs () : HasMany
    {
        return $this->hasMany ( LinkAlatDetailRkb::class, 'id_rkb' );
    }

    public function spbs () : BelongsToMany
    {
        return $this->belongsToMany (
            SPB::class,
            'link_rkb_spb',
            'id_rkb',
            'id_spb'
        )->withTimestamps ();
    }

    public function linkRkbSpbs () : HasMany
    {
        return $this->hasMany ( LinkRKBSPB::class, 'id_rkb' );
    }
}
