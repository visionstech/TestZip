<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Validator;

class SearchAddress extends Request
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
        Validator::extend('alpha_spaces', function($attribute, $value, $parameters)
        {
            return preg_match('/^[\pL\s]+$/u', $value);
        });
           //echo $this->request->get('in_out_case');exit;
        //if($this->request->get('token_exist') == '0') {
         
            
            $rules = [
/*                'first_name'      => 'required|alpha_spaces|max:50',
                'last_name'       => 'required|alpha_spaces|max:50',
                'email'           => 'required|email|max:100',
                'mobile_number'   => 'required|numeric|digits_between:9,17',*/
                'street_number'   => 'required|max:100',
                'route'           => 'required|max:100',
                'locality'        => 'required|max:100',
                'administrative_area_level_1'    => 'required',
                'postal_code'     => 'required|max:100',
                'administrative_area_level_2'   => 'required',
                /*'land_assessment_value' => 'required|numeric',
                'improvement_assessment_value' => 'required|numeric',
                'total_assessment_value' => 'required|numeric',*/
                'token_exist'     => ''

            ];
            if($this->request->get('in_out_case') == '1') {
                $year=date('Y');
                $rules['assessment_year'] = 'required|numeric|max:'.$year;
                $rules['confirm_assessment_year'] = 'required|numeric|max:'.$year;

            }
            //echo "<pre>eee";print_r($rules);exit;
       /* }
        else {
            $rules = [
                'token_exist'   => ''
            ];
        }*/
        
        return $rules;
    }
    
    public function messages() {
        return [
            'first_name.required'       => 'The first name is required.',
            'first_name.alpha_spaces'   => 'First name can contain only alphabets.',
            'first_name.max'            => 'First name cannot be greater than 50 characters.',
            'last_name.required'        => 'The last name is required.',
            'last_name.alpha_spaces'    => 'Last name can contain only alphabets.',
            'last_name.max'             => 'Last name cannot be greater than 50 characters.',
            'email.max'                 => 'The email cannot be greater than 100 digits.',                 
            'mobile_number.required'    => 'The mobile number is required.',
            'mobile_number.numeric'     => 'Mobile number should be a numeric value.',
            'mobile_number.digits_between' => 'Please enter a valid mobile number.',
            'street_number.required'    => 'The street number is required.',   
            'street_number.max'         => 'The street number cannot be greater than 100 digits.',   
            'route.required'            => 'The street is required.',   
            'route.max'                 => 'The street cannot be greater than 100 digits.',   
            'locality.required'         => 'The city is required.',   
            'locality.max'              => 'The city cannot be greater than 100 digits.',   
            'administrative_area_level_1.required'     => 'The state is required.',   
            'postal_code.required'      => 'The zip code is required.',   
            'postal_code.max'           => 'The zip code cannot be greater than 100 digits.',   
            'administrative_area_level_2.required'    => 'The county is required.',   
            /*'land_assessment_value.required'  => 'The land assessment value is required.',    
            'improvement_assessment_value.required'  => 'The improvement assessment value is required.',    
            'total_assessment_value.required'  => 'The total assessment value is required.',    
            'land_assessment_value.numeric'  => 'The land assessment value must be a number.',    
            'improvement_assessment_value.numeric'  => 'The improvement assessment value must be a number.',    
            'total_assessment_value.numeric'  => 'The total assessment value must be a number.',    */
            'assessment_year'=> 'The latest assessment year field is required.',
            'confirm_assessment_year'=> 'The confirm latest assessment year field is required.'
        ];
    }
}
