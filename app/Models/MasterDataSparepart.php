<?php

namespace App\Models;

use App\Models\KategoriSparepart;
use App\Models\MasterDataSupplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterDataSparepart extends Model
{
    use HasFactory;

    protected $table = 'master_data_sparepart';

    protected $fillable = [ 
        'nama',
        'part_number',
        'merk',
        'id_kategori_sparepart',
    ];

    protected $casts = [ 
        'id'                    => 'integer',
        'nama'                  => 'string',
        'part_number'           => 'string',
        'merk'                  => 'string',
        'id_kategori_sparepart' => 'integer',
    ];

    public function kategoriSparepart () : BelongsTo
    {
        return $this->belongsTo ( KategoriSparepart::class, 'id_kategori_sparepart' );
    }

    public function masterDataSuppliers () : BelongsToMany
    {
        return $this->belongsToMany (
            MasterDataSupplier::class,
            'link_supplier_sparepart',
            'id_master_data_sparepart',
            'id_master_data_supplier'
        )->withTimestamps ();
    }

    public function detailSpbs () : HasMany
    {
        return $this->hasMany ( DetailSPB::class, 'id_master_data_sparepart' );
    }

}
