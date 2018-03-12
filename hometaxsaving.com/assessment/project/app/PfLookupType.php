<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class PfLookupType extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
	 
     */
    protected $primaryKey = 'lookup_type_id';

    protected $fillable = [
        'lookup_type_id', 'name', 'description', 'created_by', 'created_at', 'updated_by', 'updated_at', 'end_date'
    ];

}
