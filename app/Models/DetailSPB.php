<?php

namespace App\Models;

use App\Models\LinkSPBDetailSPB;
use App\Models\MasterDataSparepart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailSPB extends Model
{
    use HasFactory;

    protected $table = 'detail_spb';

    protected $fillable = [ 
        'quantity',
        'harga',
        'satuan',
        'id_master_data_sparepart',
    ];

    protected $casts = [ 
        'id'                       => 'integer',
        'quantity'                 => 'integer',
        'harga'                    => 'integer',
        'satuan'                   => 'string',
        'id_master_data_sparepart' => 'integer',
    ];

    public function sparepart () : BelongsTo
    {
        return $this->belongsTo ( MasterDataSparepart::class, 'id_master_data_sparepart' );
    }

    public function linkSpbDetailSpb () : HasMany
    {
        return $this->hasMany ( LinkSPBDetailSPB::class, 'id_detail_spb' );
    }
}
