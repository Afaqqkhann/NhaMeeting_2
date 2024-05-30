<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UnPaidInterneeRequest extends Request
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
            'cnic_no' => 'required|unique:tbl_internee',
            'name' => 'required|max:100',           
            'discipline' => 'required',
            'father_name' => 'required|max:100',
            'gender' => 'required',            
            'mobile' => 'required|max:13',          
            'pre_district' => 'required|max:100',
            'pre_tehsil' => 'required|max:100',
            'pre_address' => 'required|max:500',
            'prm_district' => 'required|max:100',
            'prm_tehsil' => 'required|max:100',
            'prm_address' => 'required|max:500',
            'domicile_dist' => 'required|max:25',
            'degree' => 'required|max:200',
            'uni_name' => 'required|max:200',
            'session' => 'required|max:10',
            'proposed_month' => 'required',
            //'date_comp' => 'required',            
            //'grade' => 'required',            
            'place_id' => 'required',
            'cnic_front' => 'required|mimes:jpeg,jpg,png,gif|max:10000',
            'cnic_back' => 'required|mimes:jpeg,jpg,png,gif|max:10000',
            'eductional_recom_letter_edoc' => 'required|mimes:jpeg,jpg,png,gif|max:10000',
            //'final_transac' => 'required|mimes:jpeg,jpg,png,gif|max:10000',
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
			'cnic_no.required' => 'The CNIC field is required.',
            'name.required' => 'The Name field is required.',            
            'discipline.required' => 'The discipline field is required.',
            'father_name.required' => 'The father name field is required.',
            'gender.required' => 'The gender field is required.',
            'email.required' => 'The email field is required.',
            'mobile.required' => 'The mobile field is required.',
            'phone.required' => 'The phone field is required.',
            'pre_district.required' => 'The Present district field is required.',
            'pre_tehsil.required' => 'The Present tehsil field is required.',
            'pre_address.required' => 'The Present address field is required.',
            'prm_district.required' => 'The Permanent address field is required.',
            'prm_tehsil.required' => 'The Permanent tehsil field is required.',
            'prm_address.required' => 'The Permanent address field is required.',
            'domicile_dist.required' => 'The domicile dist field is required.',
            'degree.required' => 'The degree field is required.',
            'uni_name.required' => 'The university name field is required.',
            'session.required' => 'The session field is required.',
            //'date_comp.required' => 'The date of completion field is required.',           
            //'grade.required' => 'The grade field is required.',
            'place_id.required' => 'The Place field is required.',
            'cnic_front.required' => 'The CNIC Front must be uploaded.',
            'cnic_back.required' => 'The CNIC Back must be uploaded.',
            'eductional_recom_letter_edoc.required' => 'The Recommendation Letter of University/Institute must be uploaded.',
            //'final_transac.required' => 'The Final Transcript must be uploaded.',
            'proposed_month' => 'The Month for Internship field is required.',
        ];
    }
}
