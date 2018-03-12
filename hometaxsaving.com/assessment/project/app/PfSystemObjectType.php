<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class PfSystemObjectType extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'system_object_type_id', 'name', 'description', 'table_name', 'column_name', 'created_by', 'created_at', 'updated_by', 'updated_at', 'end_date'
    ];

}
