<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class PfLookup extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
	 
     */
    protected $table = 'pf_lookups';
	protected $primaryKey = 'lookup_id';
	
	
	protected $fillable = [
        'lookup_id', 'lookup_type_id', 'name', 'description', 'value', 'value1','value2','parent_lookup_id', 'display_order', 'created_by', 'created_at', 
        'updated_by', 'updated_at', 'end_date'
    ];

}
