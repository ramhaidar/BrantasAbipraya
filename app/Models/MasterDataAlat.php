<?php

namespace App\Models;

use Database\Factories\MasterDataAlatFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterDataAlat extends Model
{
    use HasFactory;

    protected $table = 'master_data_alats';

    protected $fillable = [ 
        'jenis_alat',
        'kode_alat',
        'merek_alat',
        'tipe_alat',
        'serial_number',
    ];

    protected $casts = [ 
        'id'            => 'integer',
        'jenis_alat'    => 'string',
        'kode_alat'     => 'string',
        'merek_alat'    => 'string',
        'tipe_alat'     => 'string',
        'serial_number' => 'string',
    ];

    protected static function newFactory ()
    {
        return MasterDataAlatFactory::new ();
    }
}
