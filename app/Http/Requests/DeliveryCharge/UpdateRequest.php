<?php

namespace App\Http\Requests\DeliveryCharge;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
                 
                'shipping_type'   => ['required','numeric'],
                'delivery_type_id'=> ['required','numeric'],
                
                //from   
                'from_country_id'        => ['required','numeric'],
                'from_city_id'           => ['required','numeric'],
                'from_district_id'       => ['required','numeric'],
                'from_town_id'           => ['required','numeric'],
                'from_portal_code'       => ['required','numeric'],
                //end from 
                
                //to 
                'to_country_id'         => ['required','numeric'],
                'to_city_id'            => ['required','numeric'],
                'to_district_id'        => ['required','numeric'],
                'to_town_id'            => ['required','numeric'],
                'to_portal_code'        => ['required','numeric'],
                //end to 
    
     
                'dtd_start_weight'      => ['required', 'numeric'],
                'dtd_end_weight'        => ['required', 'numeric'],
                'dtd_s_e_rate'          => ['required', 'numeric'],
                'dtd_additional_weight' => ['required', 'numeric'],
                'dtd_additional_rate'   => ['required', 'numeric'],
    
                'dth_start_weight'      => ['required', 'numeric'],
                'dth_end_weight'        => ['required', 'numeric'],
                'dth_s_e_rate'          => ['required', 'numeric'],
                'dth_additional_weight' => ['required', 'numeric'],
                'dth_additional_rate'   => ['required', 'numeric'],
    
                'hth_start_weight'      => ['required', 'numeric'],
                'hth_end_weight'        => ['required', 'numeric'],
                'hth_s_e_rate'          => ['required', 'numeric'],
                'hth_additional_weight' => ['required', 'numeric'],
                'hth_additional_rate'   => ['required', 'numeric'],
    
                'htd_start_weight'      => ['required', 'numeric'],
                'htd_end_weight'        => ['required', 'numeric'],
                'htd_s_e_rate'          => ['required', 'numeric'],
                'htd_additional_weight' => ['required', 'numeric'],
                'htd_additional_rate'   => ['required', 'numeric'],
    
                'position'              => ['numeric'],
                'status'                => ['required', 'numeric',],
            ];
        
        }
        public function attributes()
        {
            return [
                'zone_id'              => 'zone',
                'dtd_start_weight'     => 'start weight',
                'dtd_end_weight'       => 'end weight',
                'dtd_s_e_rate'         => 'rate',
                'dtd_additional_weight'=> 'additional weight',
                'dtd_additional_rate'  => 'additional rate',
    
                'dth_start_weight'      => 'start weight',
                'dth_end_weight'        => 'end weight',
                'dth_s_e_rate'          => 'rate',
                'dth_additional_weight' => 'additional weight',
                'dth_additional_rate'   => 'additional rate',
    
                'hth_start_weight'      => 'start weight',
                'hth_end_weight'        => 'end weight',
                'hth_s_e_rate'          => 'rate',
                'hth_additional_weight' => 'additional weight',
                'hth_additional_rate'   => 'additional rate',
    
                'htd_start_weight'      => 'start weight',
                'htd_end_weight'        => 'end weight',
                'htd_s_e_rate'          => 'rate',
                'htd_additional_weight' => 'additional weight',
                'htd_additional_rate'   => 'additional rate',
            ];
        }
}
