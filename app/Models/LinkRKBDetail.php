<?php

namespace App\Models;

use App\Models\RKB;
use App\Models\LinkAlatDetailRKB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkRKBDetail extends Model
{
    protected $table = 'link_rkb_detail';

    protected $fillable = [ 
        'id_link_alat_detail_rkb',
        'id_detail_rkb_general',
        'id_detail_rkb_urgent',
    ];

    protected function casts () : array
    {
        return [ 
            'id'         => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function linkAlatDetailRkb () : BelongsTo
    {
        return $this->belongsTo ( LinkAlatDetailRkb::class, 'id_link_alat_detail_rkb' );
    }

    public function detailRkbGeneral () : BelongsTo
    {
        return $this->belongsTo ( DetailRkbGeneral::class, 'id_detail_rkb_general' );
    }

    public function detailRkbUrgent () : BelongsTo
    {
        return $this->belongsTo ( DetailRkbUrgent::class, 'id_detail_rkb_urgent' );
    }

}
