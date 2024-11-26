<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RKB extends Model
{
    use HasFactory;

    protected $table = 'rkb';

    protected $fillable = [ 
        'nomor',
        'periode',
        'id_proyek',
        'is_finalized',
        'is_evaluated',
        'is_approved',
    ];

    protected function casts () : array
    {
        return [ 
            'id'           => 'integer',
            'nomor'        => 'string',
            'periode'      => 'date',
            'is_finalized' => 'boolean',
            'is_evaluated' => 'boolean',
            'is_approved'  => 'boolean',
            'created_at'   => 'datetime',
            'updated_at'   => 'datetime',
        ];
    }

    public function proyek () : BelongsTo
    {
        return $this->belongsTo ( Proyek::class, 'id_proyek' );
    }

    public function linkAlatDetailRkbs () : HasMany
    {
        return $this->hasMany ( LinkAlatDetailRkb::class, 'id_rkb' );
    }
}
