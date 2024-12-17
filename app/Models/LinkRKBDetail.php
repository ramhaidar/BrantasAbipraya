<?php

namespace App\Models;

use App\Models\RKB;
use App\Models\LinkAlatDetailRKB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LinkRKBDetail extends Model
{
    protected $table = 'link_rkb_detail';

    protected $fillable = [ 
        'id_link_alat_detail_rkb',
        'id_detail_rkb_general',
        'id_detail_rkb_urgent',
    ];

    protected $casts = [ 
        'id'                      => 'integer',
        'id_link_alat_detail_rkb' => 'integer',
        'id_detail_rkb_general'   => 'integer',
        'id_detail_rkb_urgent'    => 'integer',
        'created_at'              => 'datetime',
        'updated_at'              => 'datetime',
    ];

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

    public function linkRkbDetailSpbs () : BelongsToMany
    {
        return $this->belongsToMany (
            DetailSPB::class,
            'link_spb_detail_spb',
            'id_link_rkb_detail',
            'id_detail_spb'
        );
    }

}
