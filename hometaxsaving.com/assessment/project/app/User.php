<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'name', 'email', 'password', 'remember_token', 'ip_address','user_type'
    ];
}
