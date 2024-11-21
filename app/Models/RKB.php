<?php

namespace App\Models;

use App\Models\Proyek;
use App\Models\DetailRKBUrgent;
use App\Models\DetailRKBGeneral;
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
        'is_finalized'
    ];

    protected $casts = [ 
        'id'           => 'integer',
        'nomor'        => 'string',
        'periode'      => 'date',
        'id_proyek'    => 'integer',
        'is_finalized' => 'boolean'
    ];

    public function proyek () : BelongsTo
    {
        return $this->belongsTo ( Proyek::class, 'id_proyek' );
    }

    public function linkRkbDetails () : HasMany
    {
        return $this->hasMany ( Link_RKBDetail::class, 'id_rkb' );
    }
}
