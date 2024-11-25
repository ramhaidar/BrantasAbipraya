<?php

namespace App\Models;

use App\Models\MasterDataAlat;
use App\Models\KategoriSparepart;
use App\Models\LinkAlatDetailRKB;
use App\Models\MasterDataSparepart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailRKBGeneral extends Model
{
    use HasFactory;

    protected $table = 'detail_rkb_general';

    protected $fillable = [ 
        'quantity_requested',
        'quantity_approved',
        'satuan',
        'id_kategori_sparepart_sparepart',
        'id_master_data_sparepart',
    ];

    protected function casts () : array
    {
        return [ 
            'id'                 => 'integer',
            'quantity_requested' => 'integer',
            'quantity_approved'  => 'integer',
            'satuan'             => 'string',
            'created_at'         => 'datetime',
            'updated_at'         => 'datetime',
        ];
    }

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
        return $this->hasMany ( LinkRkbDetail::class, 'id_detail_rkb_general' );
    }
}
