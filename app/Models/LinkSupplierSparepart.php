<?php

namespace App\Models;

use App\Models\MasterDataSupplier;
use App\Models\MasterDataSparepart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkSupplierSparepart extends Model
{
    protected $table = 'link_supplier_sparepart';

    protected $fillable = [ 
        'id_master_data_supplier',
        'id_master_data_sparepart',
    ];

    protected $casts = [ 
        'id'                       => 'integer',
        'id_master_data_supplier'  => 'integer',
        'id_master_data_sparepart' => 'integer',
    ];

    public function supplier () : BelongsTo
    {
        return $this->belongsTo ( MasterDataSupplier::class, 'id_master_data_supplier' );
    }

    public function sparepart () : BelongsTo
    {
        return $this->belongsTo ( MasterDataSparepart::class, 'id_master_data_sparepart' );
    }
}
