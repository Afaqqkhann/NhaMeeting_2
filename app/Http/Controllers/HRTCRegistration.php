<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class HRTCRegistration extends Controller
{
    /**
     *  HRTC Registration Form
     */
    public function registrationForm($emp_id,$tc_id)
    {
        $page_title = 'REGISTRATION & PRE-TRAINING SURVEY';

        $data = DB::table('TBL_EMP')->where('emp_id','=',$emp_id)->first();
        $educations = DB::table('TBL_EDUCATION')->select('institute_name','sessions','document_name')
            ->where('document_type_id','<>',6)->where('document_type_id','>',4)->where('emp_id','=',$emp_id)->get();

        $trainings = DB::table('TBL_EDUCATION')->select('institute_name','sessions','document_name')
            ->where('document_type_id','=',6)->where('emp_id','=',$emp_id)->get();

        $carriers = DB::table('V_CARRIER')->select('post_name','place_title','place_type','relieving_date','joining_date',
            'remarks')->where('emp_id','=',$emp_id)->get();

        return view('hrtc.registration',compact('page_title','data','carriers','trainings','educations','tc_id'));
    }

    /**
     * Print HRTC Registration Form
     */
    public function printForm(Request $request)
    {
        $page_title = 'REGISTRATION & PRE-TRAINING SURVEY';

        //print_r($request->input('word_skill'));
         //print_r($request->input('sex'));//
        $pic_url = '';

        //// Function to upload Picture
        function picUpload($file, $new_filename,$path){
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            return $completeUrl;
        }
        /////////

        // upload Picture
        if($request->hasFile('picture')) {
            $file = $request->file('picture');
            echo $new_filename =  $request->input('fp_id');
            $path = 'storage/emp_pic/';
            $pic_url = picUpload($file,$new_filename,$path);
            $pic_url;

        }else{
            $pic_url = 'storage/emp_pic/'.$request->input('fp_id').'.jpg';
        }


        $biodata = array(
            'course_title' => $request->input('course_title'),
            'emp_name' => $request->input('emp_name'),
            'emp_id' => $request->input('emp_id'),
            'cnic' => $request->input('cnic'),
            'registration_no' => $request->input('registration_no'),
            'picture' => $pic_url,
            'domicile' => $request->input('domicile'),
            'sex' => $request->input('sex'),
            'religion' => $request->input('religion'),
            'dob_date' => $request->input('dob_date'),
            'dob_month' => $request->input('dob_month'),
            'dob_year' => $request->input('dob_year'),
            'dob_age' => $request->input('dob_age'),
            'designation' => $request->input('designation'),
            'position' => $request->input('position'),
            'station' => $request->input('station'),
            'cadre' => $request->input('cadre'),
            'doa_date' => $request->input('doa_date'),
            'doa_month' => $request->input('doa_month'),
            'doa_year' => $request->input('doa_year'),
            'dop_date' => $request->input('dop_date'),
            'dop_month' => $request->input('dop_month'),
            'dop_year' => $request->input('dop_year'),
            'outline' => $request->input('outline'),
            'tc_id' => $request->input('tc_id'),

            'office_address' => $request->input('office_address'),
            'off_phone_no' => $request->input('off_phone_no'),
            'off_mobile_no' => $request->input('off_mobile_no'),
            'office_fax' => $request->input('office_fax'),
            'off_email' => $request->input('off_email'),
            'res_address' => $request->input('res_address'),
            'res_phone_no' => $request->input('res_phone_no'),
            'res_mobile_no' => $request->input('res_mobile_no'),

            'res_fax' => $request->input('res_fax'),
            'res_email' => $request->input('res_email'),
            'emrgcy_contact_name' => $request->input('emrgcy_contact_name'),
            'relationship_emp' => $request->input('relationship_emp'),
            'emrgcy_contact_address' => $request->input('emrgcy_contact_address'),
            'emrgcy_contact_phoneno' => $request->input('emrgcy_contact_phoneno'),
            'emergency_contact_mobile' => $request->input('emergency_contact_mobile'),
            'emrgcy_contact_fax' => $request->input('emrgcy_contact_fax'),
            'emrgcy_contact_email' => $request->input('emrgcy_contact_email'),
            'others_contact' => $request->input('others_contact'),
        );
        $job_record = array();
        if($request->input('post_name')) {
            foreach ($request->input('post_name') as $key => $job) {
                $job_record[$key] = array(
                    'post_name' => $request->input('post_name')[$key],
                    'job_station' => $request->input('job_station')[$key],
                    'relieving_date' => $request->input('relieving_date')[$key],
                    'joining_date' => $request->input('joining_date')[$key],
                    'job_remarks' => $request->input('job_remarks')[$key],
                );
            }
        }

        $edu_record = array();
        if($request->input('edu_institute_name')) {
            foreach ($request->input('edu_institute_name') as $key => $job) {
                $edu_record[$key] = array(
                    'edu_institute_name' => $request->input('edu_institute_name')[$key],
                    'edu_city' => $request->input('edu_city')[$key],
                    'edu_period_from' => $request->input('edu_period_from')[$key],
                    'edu_period_to' => $request->input('edu_period_to')[$key],
                    'edu_document_name' => $request->input('edu_document_name')[$key],
                    'edu_major' => $request->input('edu_major')[$key],
                );
            }
        }

        $tran_record = array();
        if($request->input('train_institute_name')) {
            foreach ($request->input('train_institute_name') as $key => $job) {
                $tran_record[$key] = array(
                    'train_institute_name' => $request->input('train_institute_name')[$key],
                    'train_period_from' => $request->input('train_period_from')[$key],
                    'train_period_to' => $request->input('train_period_to')[$key],
                    'train_document_name' => $request->input('train_document_name')[$key],

                );
            }
        }

        $other_detail = array(
          'english_listening' => $request->input('english_listening'),
          'english_speaking' => $request->input('english_speaking'),
          'english_reading' => $request->input('english_reading'),
          'english_writing' => $request->input('english_writing'),
          'condition' => $request->input('condition'),
          'disease_name' => $request->input('disease_name'),
          'word_skill' => $request->input('word_skill'),
          'excel_skill' => $request->input('excel_skill'),
          'ppt_writing' => $request->input('ppt_writing'),
          'pres_skill' => $request->input('pres_skill'),
          'writing_skill' => $request->input('writing_skill'),
          'hobbies' => $request->input('hobbies'),
          'sports' => $request->input('sports'),
          'strength' => $request->input('strength'),
          'contribute' => $request->input('contribute'),
          'knowledge' => $request->input('knowledge'),
          'goal' => $request->input('goal'),
        );

       // echo '<pre>';print_r($other_detail);
        //echo $biodata['sex'];
        //die;

        return view('hrtc.print',compact('page_title','biodata','job_record','edu_record','tran_record','other_detail'));
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
