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
        'id_link_alat_detail_rkb',
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

    public function getDiffInDaysRencanaAttribute ()
    {
        if ( $this->tanggal_awal_rencana && $this->tanggal_akhir_rencana )
        {
            return $this->tanggal_awal_rencana->diffInDays ( $this->tanggal_akhir_rencana );
        }
        return null;
    }

    public function getDiffInDaysActualAttribute ()
    {
        if ( $this->tanggal_awal_actual && $this->tanggal_akhir_actual )
        {
            return $this->tanggal_awal_actual->diffInDays ( $this->tanggal_akhir_actual );
        }
        return null;
    }

    public function kategoriSparepart () : BelongsTo
    {
        return $this->belongsTo ( KategoriSparepart::class, 'id_kategori_sparepart_sparepart' );
    }

    public function masterDataSparepart () : BelongsTo
    {
        return $this->belongsTo ( MasterDataSparepart::class, 'id_master_data_sparepart' );
    }

    public function linkAlatDetailRkb () : BelongsTo
    {
        return $this->belongsTo ( LinkAlatDetailRKB::class, 'id_link_alat_detail_rkb' );
    }
}
