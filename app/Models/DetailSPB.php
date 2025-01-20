<?php

namespace App\Models;

use App\Models\LinkSPBDetailSPB;
use App\Models\MasterDataSparepart;
use App\Models\ATB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailSPB extends Model
{
    use HasFactory;

    protected $table = 'detail_spb';

    protected $fillable = [ 
        'quantity_po',
        'quantity_belum_diterima', // Updated from quantity_diterima
        'harga',
        'satuan',
        'id_master_data_sparepart',
        'id_master_data_alat',
        'id_link_rkb_detail',
    ];

    protected $casts = [ 
        'id'                       => 'integer',
        'quantity_po'              => 'integer',
        'quantity_belum_diterima'  => 'integer',
        'harga'                    => 'integer',
        'satuan'                   => 'string',
        'id_master_data_sparepart' => 'integer',
        'id_master_data_alat'      => 'integer',
        'id_link_rkb_detail'       => 'integer',
    ];

    protected static function boot ()
    {
        parent::boot ();

        static::creating ( function ($model)
        {
            if ( ! isset ( $model->quantity_belum_diterima ) )
            {
                $model->quantity_belum_diterima = $model->quantity_po;
            }
        } );
    }

    public function masterDataSparepart () : BelongsTo
    {
        return $this->belongsTo ( MasterDataSparepart::class, 'id_master_data_sparepart' );
    }

    public function linkSpbDetailSpb () : HasMany
    {
        return $this->hasMany ( LinkSPBDetailSPB::class, 'id_detail_spb' );
    }

    public function masterDataAlat () : BelongsTo
    {
        return $this->belongsTo ( MasterDataAlat::class, 'id_master_data_alat' );
    }

    public function linkRkbDetail () : BelongsTo
    {
        return $this->belongsTo ( LinkRKBDetail::class, 'id_link_rkb_detail' );
    }

    public function atbs () : HasMany
    {
        return $this->hasMany ( ATB::class, 'id_detail_spb' );
    }

    public function reduceQuantityBelumDiterima ( $quantity )
    {
        $this->quantity_belum_diterima = max ( 0, $this->quantity_belum_diterima - $quantity );
        $this->save ();
    }

    public function increaseQuantityBelumDiterima ( $quantity )
    {
        $this->quantity_belum_diterima = min ( $this->quantity_po, $this->quantity_belum_diterima + $quantity );
        $this->save ();
    }
}
