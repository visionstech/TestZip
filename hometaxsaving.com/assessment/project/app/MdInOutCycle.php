<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class MdInOutCycle extends Authenticatable
{
    protected $table = 'md_in_out_cycle';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cycle_id', 'county_id', 'incycle_notice_date', 'incycle_deadline_date', 'incycle_link', 'outcycle_notice_date', 'outcycle_deadline_date', 'outcycle_link', 'created_at', 'updated_by'
    ];

}
