<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class Address extends Request
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
            'home_street'  => 'required|max:100',
            'home_city'    => 'required|max:100',
            'home_state'   => 'required',
            'home_zipcode' => 'required|max:100',
            'home_county'  => 'required',
        ];
    }
    
    public function messages() {
        return [
            'home_street.max'       => 'The street cannot be greater than 100 digits.',   
            'home_city.max'         => 'The city cannot be greater than 100 digits.',   
            'home_zipcode.max'      => 'The zip code cannot be greater than 100 digits.',   
            'home_street.required'  => 'The street is required.',   
            'home_city.required'    => 'The city is required.',  
            'home_state.required'   => 'The state is required.',  
            'home_zipcode.required' => 'The zip code is required.',   
            'home_county.required'  => 'The county is required.',   
        ];
    }
}
