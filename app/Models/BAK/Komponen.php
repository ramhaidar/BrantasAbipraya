<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komponen extends Model
{
    use HasFactory;
    protected $table = 'komponen';
    protected $fillable = [ 
        'asal_proyek',
        'nama_proyek',
        'kode',
        'first_group_id',
        'second_group_id'
    ];
    public function first_group ()
    {
        return $this->belongsTo ( FirstGroup::class);
    }
    public function second_group ()
    {
        return $this->belongsTo ( SecondGroup::class);
    }
}

