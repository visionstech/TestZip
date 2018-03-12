<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class PfHighKey extends Authenticatable
{
    protected $table = 'pf_high_key';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key_description', 'schema_name', 'table_name', 'column_name', 'high_key', 'created_by', 'created_at', 'updated_by', 'updated_at', 'end_date'
    ];

}
