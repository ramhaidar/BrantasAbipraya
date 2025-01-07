<?php

namespace App\Models;

use App\Models\LinkRKBSPB;
use App\Models\LinkSPBDetailSPB;
use App\Models\MasterDataSupplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SPB extends Model
{
    use HasFactory;

    protected $table = 'spb';

    protected $fillable = [ 
        'nomor',
        'is_addendum',
        'tanggal',
        'id_master_data_supplier',
        'id_spb_original'
    ];

    protected $casts = [ 
        'id'                      => 'integer',
        'nomor'                   => 'string',
        'is_addendum'             => 'boolean',
        'tanggal'                 => 'date:d F Y',
        'id_master_data_supplier' => 'integer',
        'id_spb_original'         => 'integer',
    ];

    public function masterDataSupplier () : BelongsTo
    {
        return $this->belongsTo ( MasterDataSupplier::class, 'id_master_data_supplier' );
    }

    public function linkRkbSpbs () : HasMany
    {
        return $this->hasMany ( LinkRKBSPB::class, 'id_spb' );
    }

    public function linkSpbDetailSpb () : HasMany
    {
        return $this->hasMany ( LinkSPBDetailSPB::class, 'id_spb' );
    }

    public function originalSpb () : BelongsTo
    {
        return $this->belongsTo ( SPB::class, 'id_spb_original' );
    }

    public function addendum () : HasOne
    {
        return $this->hasOne ( SPB::class, 'id_spb_original' );
    }

    public function atbs () : HasMany
    {
        return $this->hasMany ( ATB::class, 'id_spb' );
    }

    public function saldo () : HasMany
    {
        return $this->hasMany ( Saldo::class, 'id_spb' );
    }

    protected static function boot ()
    {
        parent::boot ();

        static::deleting ( function ($spb)
        {
            $spb->linkSpbDetailSpb ()->delete ();
            $spb->linkRkbSpbs ()->delete ();
            $spb->saldo ()->delete (); // Add deletion of related saldo records
        } );
    }
}
