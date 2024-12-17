<?php

namespace App\Models;

use App\Models\RKB;
use App\Models\SPB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LinkRKBSPB extends Model
{
    use HasFactory;

    protected $table = 'link_rkb_spb';

    protected $fillable = [ 
        'id_rkb',
        'id_spb'
    ];

    protected $casts = [ 
        'id'     => 'integer',
        'id_rkb' => 'integer',
        'id_spb' => 'integer'
    ];

    public function rkb () : BelongsTo
    {
        return $this->belongsTo ( RKB::class, 'id_rkb' );
    }

    public function spb () : BelongsTo
    {
        return $this->belongsTo ( SPB::class, 'id_spb' );
    }
}
