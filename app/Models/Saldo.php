<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    use HasFactory;
    protected $table = 'saldo';
    protected $fillable = [ 
        'current_quantity',
        'net',
    ];

    public function atb ()
    {
        return $this->hasOne ( ATB::class, 'id_saldo' );
    }

    public function apb ()
    {
        return $this->hasMany ( APB::class, 'id_saldo' );
    }
}

