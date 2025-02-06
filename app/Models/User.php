<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Alat;
use App\Models\Proyek;
use App\Models\UserProyek;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [ 
        'name',
        'username',
        'sex',
        'path_profile',
        'role',
        'phone',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [ 
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [ 
        'id'             => 'integer',
        'name'           => 'string',
        'username'       => 'string',
        'sex'            => 'string',
        'path_profile'   => 'string',
        'role'           => 'enum:superadmin,svp,vp,admin_divisi,koordinator_proyek',
        'phone'          => 'string',
        'email'          => 'string',
        'password'       => 'hashed',
        'remember_token' => 'string',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];


    public function userProyek ()
    {
        return $this->hasMany ( UserProyek::class, 'id_user' );
    }


    public function proyek ()
    {
        return $this->belongsToMany ( Proyek::class, 'user_proyek', 'id_user', 'id_proyek' );
    }

    public function proyeks ()
    {
        return $this->belongsToMany ( Proyek::class, 'user_proyek', 'id_user', 'id_proyek' );
    }
}
