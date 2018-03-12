<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserSearch extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_search_id', 'system_user_id', 'search_date', 'first_name', 'last_name', 'land_assessment_value', 'improvement_assessment_value', 'total_assessment_value', 'appeal_year', 'comparables', 'token', 'phase1_paid_amount', 'phase2_token', 'phase2_paid_amount', 'active_page', 'status', 'sale_date', 'sale_price', 'created_by', 'created_at', 'updated_by', 'updated_at', 'end_date','latest_assesement_year'
    ];

}
