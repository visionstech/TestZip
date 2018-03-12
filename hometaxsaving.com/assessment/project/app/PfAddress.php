<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class PfAddress extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address_id', 'system_object_type_id', 'ref_object_id', 'address_type', 'mobile_number', 'receive_notification', 'address_line_1', 'address_line_2', 'address_line_3', 'city', 'postal_code',
        'state', 'country', 'province', 'county', 'created_by', 'created_at', 'updated_by', 'updated_at', 'end_date' 
    ];

}
