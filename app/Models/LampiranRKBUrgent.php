<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LampiranRKBUrgent extends Model
{
    use HasFactory;

    protected $table = 'lampiran_rkb_urgent';

    protected $fillable = [ 
        'dokumentasi',
        'id_link_alat_detail_rkb',
    ];

    protected $casts = [ 
        'id'                      => 'integer',
        'dokumentasi'             => 'string',
        'id_link_alat_detail_rkb' => 'integer',
        'created_at'              => 'datetime',
        'updated_at'              => 'datetime',
    ];

    public function linkAlatDetailRKB () : BelongsTo
    {
        return $this->belongsTo ( LinkAlatDetailRKB::class, 'id_link_alat_detail_rkb' );
    }
}
