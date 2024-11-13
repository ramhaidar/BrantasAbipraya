<?php

namespace App\Models;

use App\Models\KategoriSparepart;
use App\Models\MasterDataSupplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MasterDataSparepart extends Model
{
    use HasFactory;

    protected $table = 'master_data_spareparts';

    protected $fillable = [ 
        'nama',
        'part_number',
        'merk',
        'id_kategori',
    ];

    protected $casts = [ 
        'id'          => 'integer',
        'nama'        => 'string',
        'part_number' => 'string',
        'merk'        => 'string',
        'id_kategori' => 'integer',
    ];

    public function kategori () : BelongsTo
    {
        return $this->belongsTo ( KategoriSparepart::class, 'id_kategori' );
    }

    public function suppliers () : BelongsToMany
    {
        return $this->belongsToMany ( MasterDataSupplier::class, 'link_supplier_sparepart', 'id_sparepart', 'id_supplier' )
            ->withTimestamps ();
    }
}
