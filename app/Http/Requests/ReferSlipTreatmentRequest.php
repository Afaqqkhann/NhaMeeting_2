<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ReferSlipTreatmentRequest extends Request
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
            'treatment_id' => 'required',               
            'remarks' => 'string|max:200',
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
            'treatment_id' => 'The Treatment field is required.',
            'remarks' => 'The Remarks field length is max 200 characters.',           
        ];
    }

}
