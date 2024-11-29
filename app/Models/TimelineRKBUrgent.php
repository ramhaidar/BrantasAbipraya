<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimelineRKBUrgent extends Model
{
    use HasFactory;

    protected $table = 'timeline_rkb_urgent';

    protected $fillable = [ 
        'nama_rencana',
        'tanggal_awal_rencana',
        'tanggal_akhir_rencana',
        'tanggal_awal_actual',
        'tanggal_akhir_actual',
        'is_done',
        'id_kategori_sparepart_sparepart',
        'id_master_data_sparepart',
    ];

    protected $casts = [ 
        'id'                    => 'integer',
        'nama_rencana'          => 'string',
        'tanggal_awal_rencana'  => 'date',
        'tanggal_akhir_rencana' => 'date',
        'tanggal_awal_actual'   => 'date',
        'tanggal_akhir_actual'  => 'date',
        'is_done'               => 'boolean',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
    ];

    public function kategoriSparepart () : BelongsTo
    {
        return $this->belongsTo ( KategoriSparepart::class, 'id_kategori_sparepart_sparepart' );
    }

    public function masterDataSparepart () : BelongsTo
    {
        return $this->belongsTo ( MasterDataSparepart::class, 'id_master_data_sparepart' );
    }

    public function linkAlatDetailRkbs () : HasMany
    {
        return $this->hasMany ( LinkAlatDetailRKB::class, 'id_timeline_rkb_urgent' );
    }
}
