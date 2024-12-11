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
        'file_path',
    ];

    protected $casts = [ 
        'id'         => 'integer',
        'file_path'  => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function linkAlatDetailRkb () : BelongsTo
    {
        return $this->belongsTo ( LinkAlatDetailRKB::class, 'id', 'id_lampiran_rkb_urgent' );
    }

}
