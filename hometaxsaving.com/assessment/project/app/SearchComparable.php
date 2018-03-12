<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchComparable extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'search_comparable_id', 'user_searches_id', 'subject_comps_detail_id', 'sale_price', 'year_built', 'year_renovated', 'distance_from_subject', 'date_of_sale', 'sale_price_divided_sf', 'data_source', 'subsidy', 'leasehold', 'square_footage','square_footage_price', 'exterior', 'gross_living_area', 'basement', 'basement_type', 'net_adjustment', 'parcel_size', 'total_bedrooms', 'total_bathrooms', 'finished_space', 'unfinished_space', 'garage', 'carport', 'porch_deck', 'patio', 'swimming_pool', 'fireplace', 'total_adjustment_price', 'price_after_adjustment', 'lookup_id', 'lookup_value', 'lookup_count','land_assessment_value','improvement_assesment_value','total_assessment_value', 'half_bath_count', 'created_by', 'created_at', 'updated_by', 'updated_at', 'end_date','fireplace_count'
    ];
}
