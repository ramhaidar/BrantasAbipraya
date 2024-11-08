<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Link_SupplierSparepart extends Model
{
    protected $table = 'link_supplier_sparepart';

    protected $fillable = [ 
        'id_supplier',
        'id_sparepart',
    ];

    protected $casts = [ 
        'id'           => 'integer',
        'id_supplier'  => 'integer',
        'id_sparepart' => 'integer',
    ];

    public function supplier () : BelongsTo
    {
        return $this->belongsTo ( MasterDataSupplier::class, 'id_supplier' );
    }

    public function sparepart () : BelongsTo
    {
        return $this->belongsTo ( MasterDataSparepart::class, 'id_sparepart' );
    }
}
