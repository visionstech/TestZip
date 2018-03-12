<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubjectCompsDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject_comps_id', 'system_object_type_id', 'ref_object_id', 'comparable_number', 'type_of_house', 'square_footage', 'bedrooms', 'bathrooms', 'unfinished_space', 'finished_space', 
        'garage', 'carport', 'porch_deck', 'patio', 'swimming_pool', 'fireplace', 'year_built', 'air_conditioning', 'owner_name', 'corelogic_response', 'basement_area', 'parcel_size', 'exterior', 'half_bath_count', 'created_by', 'created_at', 'updated_by', 'updated_at', 'end_date'
    ];
}
