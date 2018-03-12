<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class VerifyAddress extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'square_footage' => 'required|numeric',
            'bedrooms' => 'required|numeric',
            'bathrooms' => 'required|numeric',
            'unfinished_space' => 'required|numeric',
            'finished_space' => 'required|numeric',
            //'garage_exist' => 'sometimes',
            'carport_exist' => 'sometimes',
            'porch_deck_exist' => 'sometimes',
            'patio_exist' => 'sometimes',
            'pool_exist' => 'sometimes',
            //'fireplace_exist' => 'sometimes',

            //'garage_count' => 'required_if:garage_exist,1|numeric',
            //'fireplace_count' => 'required_if:fireplace_exist,1|numeric',
            'garage_count' => 'numeric',
            'fireplace_count' => 'numeric',
            'land_assessment_value' => 'required|numeric',
            'improvement_assessment_value' => 'required|numeric',
            'total_assessment_value' => 'required|numeric',
        ];
    }
    
    public function messages() {
        return [
            'square_footage.required'  => 'The square footage value is required.',   
            'square_footage.numeric'  => 'The square footage value must be a number.',  
            'bedrooms.required'  => 'The bedrooms value is required.',   
            'bedrooms.numeric'  => 'The bedrooms value must be a number.',  
            'bathrooms.required'  => 'The bathrooms value is required.',   
            'bathrooms.numeric'  => 'The bathrooms value must be a number.',  
            'unfinished_space.required'  => 'The unfinished space value is required.',   
            'unfinished_space.numeric'  => 'The unfinished space value must be a number.',
            'finished_space.required'  => 'The finished space value is required.',   
            'finished_space.numeric'  => 'The finished space value must be a number.',  
             
            //'fireplace_count.required_if'  => 'The fireplace count value is required.',  
            'fireplace_count.numeric'  => 'The fireplace count value must be a number.', 
            'garage_count.numeric'  => 'The garage count value must be a number.', 
            //'garage_count.required_if'  => 'The garage count value is required.',  
            'land_assessment_value.required'  => 'The land assessment value is required.',    
            'improvement_assessment_value.required'  => 'The improvement assessment value is required.',    
            'total_assessment_value.required'  => 'The total assessment value is required.',    
            'land_assessment_value.numeric'  => 'The land assessment value must be a number.',    
            'improvement_assessment_value.numeric'  => 'The improvement assessment value must be a number.',    
            'total_assessment_value.numeric'  => 'The total assessment value must be a number.',
        ];
    }
}
