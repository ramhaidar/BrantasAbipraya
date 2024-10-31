<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirstGroup extends Model
{
    use HasFactory;
    protected $table = 'first_group';
    protected $fillable = [ 
        'name'
    ];
}

