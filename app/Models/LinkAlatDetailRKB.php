<?php

namespace App\Models;

use App\Models\MasterDataAlat;
use App\Models\DetailRKBUrgent;
use App\Models\DetailRKBGeneral;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LinkAlatDetailRKB extends Model
{
    use HasFactory;

    protected $table = 'link_alat_detail_rkb';

    protected $fillable = [ 
        'nama_mekanik',
        'id_rkb',
        'id_master_data_alat',
        'id_timeline_rkb_urgent',
    ];

    protected $casts = [ 
        'id'                     => 'integer',
        'nama_mekanik'           => 'string',
        'id_rkb'                 => 'integer',
        'id_master_data_alat'    => 'integer',
        'id_timeline_rkb_urgent' => 'integer',
        'created_at'             => 'datetime',
        'updated_at'             => 'datetime',
    ];

    public function rkb () : BelongsTo
    {
        return $this->belongsTo ( RKB::class, 'id_rkb' );
    }

    public function masterDataAlat () : BelongsTo
    {
        return $this->belongsTo ( MasterDataAlat::class, 'id_master_data_alat' );
    }

    public function timelineRkbUrgent () : BelongsTo
    {
        return $this->belongsTo ( TimelineRKBUrgent::class, 'id_timeline_rkb_urgent' );
    }

    public function linkRkbDetails () : HasMany
    {
        return $this->hasMany ( LinkRkbDetail::class, 'id_link_alat_detail_rkb' );
    }
}
