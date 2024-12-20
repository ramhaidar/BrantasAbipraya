<?php

namespace App\Models;

use App\Models\LinkRKBSPB;
use App\Models\LinkSPBDetailSPB;
use App\Models\MasterDataSupplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SPB extends Model
{
    use HasFactory;

    protected $table = 'spb';

    protected $fillable = [ 
        'nomor',
        'tanggal',
        'id_master_data_supplier'
    ];

    protected $casts = [ 
        'id'                      => 'integer',
        'nomor'                   => 'string',
        'tanggal'                 => 'date:d F Y',
        'id_master_data_supplier' => 'integer',
    ];

    public function masterDataSupplier () : BelongsTo
    {
        return $this->belongsTo ( MasterDataSupplier::class, 'id_master_data_supplier' );
    }

    public function linkRkbSpbs () : HasMany
    {
        return $this->hasMany ( LinkRKBSPB::class, 'id_spb' );
    }

    public function linkSpbDetailSpb () : HasMany
    {
        return $this->hasMany ( LinkSPBDetailSPB::class, 'id_spb' );
    }
}
