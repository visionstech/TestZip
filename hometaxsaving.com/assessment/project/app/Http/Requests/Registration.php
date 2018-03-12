<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Validator;

class Registration extends Request
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

        $rules = [
            'first_name'      => 'required|alpha_spaces|max:50',
            'last_name'       => 'required|alpha_spaces|max:50',
            'email'           => 'required|email|max:100',
            'mobile_number'   => 'required|numeric|digits_between:9,17',
            'password'        => 'required|min:6',
            'password_confirmation'   => 'required|same:password',
            'street_number'   => 'required|max:100',
            'route'           => 'required|max:100',
            'locality'        => 'required|max:100',
            'administrative_area_level_1'    => 'required',
            'postal_code'     => 'required|max:100',
            'administrative_area_level_2'   => 'required',
        ];
        if($this->request->get('in_out_case') == '1') {
            $year=date('Y');
            $rules['assessment_year'] = 'required|numeric|max:'.$year;
            $rules['confirm_assessment_year'] = 'required|numeric|max:'.$year;

        }
        
        
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
            'password.required'         => 'The password is required.',
            'password.min'              => 'Password length at least 6 characters or greater.',
            'password_confirmation.required'         => 'The confirm password is required.',
            'password_confirmation.same'        => 'Confirm password doesn\'t match.',
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
        ];
    }
}
