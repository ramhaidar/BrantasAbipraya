<?php

namespace App\Models;

use App\Models\MasterDataAlat;
use App\Models\KategoriSparepart;
use App\Models\MasterDataSparepart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailRKBUrgent extends Model
{
    use HasFactory;

    protected $table = 'detail_rkb_urgent';

    protected $fillable = [ 
        'quantity_requested',
        'quantity_approved',
        'quantity_remainder',
        'satuan',
        'kronologi',
        'nama_koordinator',
        'dokumentasi',
        'id_kategori_sparepart_sparepart',
        'id_master_data_sparepart',
    ];

    protected $casts = [ 
        'id'                 => 'integer',
        'quantity_requested' => 'integer',
        'quantity_approved'  => 'integer',
        'quantity_remainder' => 'integer',
        'satuan'             => 'string',
        'kronologi'          => 'string',
        'nama_koordinator'   => 'string',
        'dokumentasi'        => 'string',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

    public function kategoriSparepart () : BelongsTo
    {
        return $this->belongsTo ( KategoriSparepart::class, 'id_kategori_sparepart_sparepart' );
    }

    public function masterDataSparepart () : BelongsTo
    {
        return $this->belongsTo ( MasterDataSparepart::class, 'id_master_data_sparepart' );
    }

    public function linkRkbDetails () : HasMany
    {
        return $this->hasMany ( LinkRkbDetail::class, 'id_detail_rkb_urgent' );
    }

    public function incrementQuantityRemainder ( $quantity )
    {
        $this->quantity_remainder = min ( $this->quantity_approved, $this->quantity_remainder + $quantity );
        $this->save ();
    }

    public function decrementQuantityRemainder ( $quantity )
    {
        $this->quantity_remainder = max ( 0, $this->quantity_remainder - $quantity );
        $this->save ();
    }
}
