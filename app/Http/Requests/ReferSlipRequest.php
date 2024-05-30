<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ReferSlipRequest extends Request
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
            'emp_id' => 'required',
            //'dependent_id' => 'required',
			'panel_type_id' => 'required',
            'panel_id' => 'required',
        ];
    }

    /**
     * Set custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [            
			'emp_id' => 'The Employee field is required.',            
            //'dependent_id' => 'The Dependent field is required.',
            'panel_type_id' => 'The Panel Type field is required.',
            'panel_id' => 'The Panel field is required.',
           
        ];
    }

}
