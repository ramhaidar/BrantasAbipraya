<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    use HasFactory;

    protected $table = 'saldo';

    protected $fillable = [ 
        'tipe',
        'satuan',
        'quantity',
        'harga',
        'id_atb',
        'id_proyek',
        'id_asal_proyek',
        'id_spb',
        'id_master_data_sparepart',
        'id_master_data_supplier',
    ];

    protected $casts = [ 
        'id'                       => 'integer',
        'tipe'                     => 'string',
        'satuan'                   => 'string',
        'quantity'                 => 'integer',
        'harga'                    => 'decimal:5',
        'id_atb'                   => 'integer',
        'id_proyek'                => 'integer',
        'id_asal_proyek'           => 'integer',
        'id_spb'                   => 'integer',
        'id_master_data_sparepart' => 'integer',
        'id_master_data_supplier'  => 'integer',
        'created_at'               => 'datetime',
        'updated_at'               => 'datetime',
    ];

    public function proyek ()
    {
        return $this->belongsTo ( Proyek::class, 'id_proyek' );
    }

    public function asalProyek ()
    {
        return $this->belongsTo ( Proyek::class, 'id_asal_proyek' );
    }

    public function spb ()
    {
        return $this->belongsTo ( Spb::class, 'id_spb' );
    }

    public function masterDataSparepart ()
    {
        return $this->belongsTo ( MasterDataSparepart::class, 'id_master_data_sparepart' );
    }

    public function masterDataSupplier ()
    {
        return $this->belongsTo ( MasterDataSupplier::class, 'id_master_data_supplier' );
    }

    public function atb ()
    {
        return $this->belongsTo ( ATB::class, 'id_atb' );
    }

    public function apb ()
    {
        return $this->hasOne ( APB::class, 'id_saldo' );
    }

    public function incrementQuantity ( $amount )
    {
        $this->quantity += $amount;
        $this->save ();

        return $this;
    }

    public function decrementQuantity ( $amount )
    {
        if ( $this->quantity < $amount )
        {
            throw new \Exception( 'Stok tidak mencukupi' );
        }

        $this->quantity = max ( 0, $this->quantity - $amount );
        $this->save ();

        return $this;
    }

    public function reduceQuantity ( $amount )
    {
        $this->quantity = max ( 0, $this->quantity - $amount );
        $this->save ();

        return $this;
    }
}
