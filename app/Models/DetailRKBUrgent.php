<?php

namespace App\Models;

use App\Models\MasterDataAlat;
use App\Models\KategoriSparepart;
use App\Models\MasterDataSparepart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailRKBUrgent extends Model
{
    use HasFactory;

    protected $table = 'detail_rkb_urgent';

    protected $fillable = [ 
        'quantity',
        'satuan',
        'id_alat',
        'id_kategori_sparepart',
        'id_sparepart',
    ];

    protected $casts = [ 
        'id'                    => 'integer',
        'quantity'              => 'string',
        'satuan'                => 'string',
        'id_alat'               => 'integer',
        'id_kategori_sparepart' => 'integer',
        'id_sparepart'          => 'integer',
    ];

    public function masterDataAlat () : BelongsTo
    {
        return $this->belongsTo ( MasterDataAlat::class, 'id_alat' );
    }

    public function kategoriSparepart () : BelongsTo
    {
        return $this->belongsTo ( KategoriSparepart::class, 'id_kategori_sparepart' );
    }

    public function masterDataSparepart () : BelongsTo
    {
        return $this->belongsTo ( MasterDataSparepart::class, 'id_sparepart' );
    }
}
