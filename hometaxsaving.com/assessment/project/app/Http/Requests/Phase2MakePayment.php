<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Validator;


class Phase2MakePayment extends Request
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
        $date_month = date('m')-1;	$date_year = date('Y');
        $date = $date_month."-".$date_year;
        
        Validator::extend('alpha_spaces', function($attribute, $value, $parameters)
        {
            return preg_match('/^[\pL\s]+$/u', $value);
        });
        
        Validator::extend('special_chars', function($attribute, $value, $parameters)
        {
            return preg_match('/^[a-zA-Z0-9 ]+$/', $value);
        });

        Validator::extend('card', function($attribute, $number, $parameters)
        {
            // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
            $number=preg_replace('/\D/', '', $number);

            // Set the string length and parity
            $number_length=strlen($number);
            $parity=$number_length % 2;

            // Loop through each digit and do the maths
            $total=0;
            for ($i=0; $i<$number_length; $i++) {
                    $digit=$number[$i];
                    // Multiply alternate digits by two
                    if ($i % 2 == $parity) {
                            $digit*=2;
                            // If the sum is two digits, add them together (in effect)
                            if ($digit > 9) {
                                    $digit-=9;
                            }
                    }
                    // Total up the digits
                    $total+=$digit;
            }

            // If the total mod 10 equals 0, the number is valid
            return ($total % 10 == 0) ? TRUE : FALSE;

        });

		
		if ($this->request->get('same_billing_address') == NULL || empty($this->request->get('same_billing_address')) || $this->request->get('same_billing_address') == '0')
			$same_billing_address = '0';
		else
			$same_billing_address = '1';
		
        if($this->request->get('token_phase2_exist') == '0' && $same_billing_address == '1') {
            $rules = [
                //'email'           => 'required|email|confirmed|max:100',
                'card_type'       => 'required',
                'card_number'     => 'required|numeric|card',
                'ex_month'        => 'required',
                'ex_year'         => 'required',
                //'ex_date'       => 'date_format:"m-Y"|after:'.$date,
                'cvv'             => 'required|numeric',
                'name_on_card'    => 'required|alpha_spaces|max:150',
                'token_exist'     => ''
            ];
			if($this->request->get('ex_month') != '' && $this->request->get('ex_year') != '') {
                $rules['ex_date'] = 'date_format:"m-Y"|after:'.$date;
            }
		}
		elseif($this->request->get('token_phase2_exist') == '0'){
			$rules = [
                //'email'           => 'required|email|confirmed|max:100',
                'card_type'       => 'required',
                'card_number'     => 'required|numeric|card',
                'ex_month'        => 'required',
                'ex_year'         => 'required',
                //'ex_date'       => 'date_format:"m-Y"|after:'.$date,
                'cvv'             => 'required|numeric',
                'name_on_card'    => 'required|alpha_spaces|max:150',
                'street_number'   => 'required|max:100',
                'route'           => 'required|max:100',
                'locality'        => 'required|max:100',
                'administrative_area_level_1'   => 'required',
                'postal_code'     => 'required|max:100',
                'administrative_area_level_2'  => 'required',
                'token_exist'     => ''
            ];	
			 if($this->request->get('ex_month') != '' && $this->request->get('ex_year') != '') {
                $rules['ex_date'] = 'date_format:"m-Y"|after:'.$date;
            }
		}

           
        
        else {
            $rules = [
                'token_phase2_exist'   => ''
            ];
        }
        
        return $rules;
    }
	
	
	public function messages() {
        return [
            'email.max'             => 'The email cannot be greater than 100 digits.',                 
            'card_number.required'  => 'Credit card number is required.',
            'card_number.numeric'   => 'Enter only numeric value. ',
            'ex_month.required'     => 'The expiry month is required.',
            'ex_year.required'      => 'The expiry year is required.',
            'ex_date.after'	    => 'Your credit card has been expired.',
            'card_number.card' 	    => 'Your credit card number is not valid.',
            'cvv.max' 		    => 'The cvv number cannot be greater than 4 digits.',
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
            'name_on_card.alpha_spaces' => 'Name on card cannot contain numbers.',
        ];
    }
}
