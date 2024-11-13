<?php

namespace App\Models;

use App\Models\MasterDataSparepart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KategoriSparepart extends Model
{
    use HasFactory;

    protected $table = 'kategori_sparepart';

    protected $fillable = [ 
        'kode',
        'nama',
        'jenis',
        'sub_jenis',
    ];

    protected function casts () : array
    {
        return [ 
            'id'         => 'integer',
            'kode'       => 'string',
            'nama'       => 'string',
            'jenis'      => 'string',
            'sub_jenis'  => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function spareparts () : HasMany
    {
        return $this->hasMany ( MasterDataSparepart::class, 'id_kategori' );
    }
}
