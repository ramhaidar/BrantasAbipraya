<?php

namespace App\Models;

use App\Models\SPB;
use App\Models\DetailSPB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LinkSPBDetailSPB extends Model
{
    use HasFactory;

    protected $table = 'link_spb_detail_spb';

    protected $fillable = [ 
        'id_spb',
        'id_detail_spb'
    ];

    protected $casts = [ 
        'id'            => 'integer',
        'id_spb'        => 'integer',
        'id_detail_spb' => 'integer'
    ];

    public function spb () : BelongsTo
    {
        return $this->belongsTo ( SPB::class, 'id_spb' );
    }

    public function detailSpb () : BelongsTo
    {
        return $this->belongsTo ( DetailSPB::class, 'id_detail_spb' );
    }
}
