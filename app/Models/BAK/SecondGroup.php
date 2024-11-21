<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecondGroup extends Model
{
    use HasFactory;
    protected $table = 'second_group';
    protected $fillable = [ 
        'name'
    ];
}

