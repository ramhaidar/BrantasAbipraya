<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    ];

    protected $casts = [ 
        'id'          => 'integer',
        'nama'        => 'string',
        'part_number' => 'string',
        'merk'        => 'string',
    ];

    public function suppliers () : BelongsToMany
    {
        return $this->belongsToMany ( MasterDataSupplier::class, 'link_supplier_sparepart', 'id_sparepart', 'id_supplier' )
            ->withTimestamps ();
    }
}
