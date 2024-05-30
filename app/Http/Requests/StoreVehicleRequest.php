<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class StoreVehicleRequest extends Request
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
            'vehno' => 'required',
            'make' => 'required',
            'body_type' => 'required',
            'model' => 'required',
            'enginenum' => 'required',
            'chasisnum' => 'required',
            'region_id' => 'required',
            'place_id' => 'required',
            'entitelment' => 'required',
            'deployedwith' => 'required',
           // 'district_registration' => 'required',
            'token_expiry' => 'required',
            'fuel_type' => 'required',
            'physical_id' => 'required',
            //'veh_pica' => 'required',
            //'edoc' => 'required',
            'fuellimit' => 'required|integer',
            'seating_capacity' => 'required|integer',           
            'manafactured' => 'max:12'
        ];
    }
    /**
     * Custom Messages
     */

    public function messages()
    {
        return [
            'manafactured.required' => 'Manufactured field should not more than 12 characters.',
            'fuellimit.required' => 'Fuel Limit field is required.',
            'physical_id.required' => 'Physical Title field is required.',
            //'veh_pica.required' => 'Vehicle Photo 1 must be uploaded.',
            //'edoc.required' => 'E-Document must be uploaded.',
            'region_id.required' => 'Reion field is required.',
            'place_id.required' => 'Place field is required.',
            
        ];
    }
}
