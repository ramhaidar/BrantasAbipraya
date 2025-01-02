<?php

namespace App\Models;

use App\Models\MasterDataSparepart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterDataSupplier extends Model
{
    use HasFactory;

    protected $table = 'master_data_supplier';

    protected $fillable = [ 
        'nama',
        'alamat',
        'contact_person',
    ];

    protected $casts = [ 
        'id'             => 'integer',
        'nama'           => 'string',
        'alamat'         => 'string',
        'contact_person' => 'string',
    ];

    public function masterDataSpareparts () : BelongsToMany
    {
        return $this->belongsToMany ( MasterDataSparepart::class, 'link_supplier_sparepart', 'id_master_data_supplier', 'id_master_data_sparepart' )
            ->withTimestamps ();
    }

    public function spbs () : HasMany
    {
        return $this->hasMany ( SPB::class, 'id_master_data_supplier' );
    }

    public function atbs () : HasMany
    {
        return $this->hasMany ( ATB::class, 'id_master_data_supplier' );
    }
}