<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PhpParser\Node\Expr\Cast;

class LinkRKBDetail extends Model
{
    protected $table = 'link_rkb_detail';

    protected $fillable = [ 
        'id_rkb',
        'id_detail_rkb_general',
        'id_detail_rkb_urgent',
    ];

    protected $casts = [ 
        'id'                    => 'integer',
        'id_rkb'                => 'integer',
        'id_detail_rkb_general' => 'integer',
        'id_detail_rkb_urgent'  => 'integer',
    ];

    public function rkb () : BelongsTo
    {
        return $this->belongsTo ( RKB::class, 'id_rkb' );
    }

    public function detailRkbGeneral () : BelongsTo
    {
        return $this->belongsTo ( DetailRKBGeneral::class, 'id_detail_rkb_general' );
    }

    public function detailRkbUrgent () : BelongsTo
    {
        return $this->belongsTo ( DetailRKBUrgent::class, 'id_detail_rkb_urgent' );
    }
}
