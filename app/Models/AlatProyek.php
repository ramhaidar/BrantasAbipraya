<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AlatProyek extends Model
{
    use HasFactory;

    protected $table = 'alat_proyek';

    protected $fillable = [ 
        'assigned_at',
        'removed_at',
        'id_master_data_alat',
        'id_proyek',
    ];

    protected $casts = [ 
        'id'                  => 'integer',
        'assigned_at'         => 'datetime',
        'removed_at'          => 'datetime',
        'id_master_data_alat' => 'integer',
        'id_proyek'           => 'integer',
    ];

    public function alat ()
    {
        return $this->belongsTo ( MasterDataAlat::class, 'id_alat' );
    }

    public function proyek ()
    {
        return $this->belongsTo ( Proyek::class, 'id_proyek' );
    }
}
