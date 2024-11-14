<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MasterDataSupplier extends Model
{
    use HasFactory;

    protected $table = 'master_data_supplier';

    protected $fillable = [ 
        'nama',
    ];

    protected $casts = [ 
        'id'   => 'integer',
        'nama' => 'string',
    ];

    public function spareparts () : BelongsToMany
    {
        return $this->belongsToMany ( MasterDataSparepart::class, 'link_supplier_sparepart', 'id_supplier', 'id_sparepart' )
            ->withTimestamps ();
    }
}