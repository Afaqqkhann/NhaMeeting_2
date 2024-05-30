<?php

namespace App\Http\Controllers;

use App\Models\Intrnee;
use App\Models\Intrnee_edu;
use App\Models\Order;
use App\Models\Tranings;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Validator;
use Input;

class Interneee_eduController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $semester = ['' => 'Select Semester', '1' => 'Ist', '2' => '2nd', '3' => '3rd','4' => '4th','5' => '5th','6' => '6th','7' => '7th','8' => '8th'];
        $year = ['' => 'Select Year', '1' => 'Ist', '2' => '2nd', '3' => '3rd','4' => '4th'];
       return view('internee_deu.index',compact('semester', 'year'));
    }
    public function index1($id)
    {
       $data = DB::table('tbl_internee')->join('tbl_internee_education', 'tbl_internee.internee_id', '=', 'tbl_internee_education.internee_id' )
              ->where('tbl_internee.internee_id', '=', $id)
           ->select('tbl_internee.internee_id as internee_id', 'tbl_internee.name as name','tbl_internee.news_paper_name as news_paper_name','tbl_internee.pubication_date as pubiction_date',
               'tbl_internee.fname as fname','tbl_internee.cnic_no as cnic_no', 'tbl_internee.gender as gender',
               'tbl_internee.email as email', 'tbl_internee.mobile_no as mobile', 'tbl_internee.telephone_no as phone_no','tbl_internee.present_address as present_address'
               , 'tbl_internee.present_district as present_district', 'tbl_internee.present_tehsil as present_tehsil','tbl_internee.domicle as domicle_dist',
               'tbl_internee.permanent_address as permanent_address','tbl_internee.permanent_district as permanent_district','tbl_internee.permanent_tehsil as permanent_tehsil',
               'tbl_internee_education.internee_edu_id as internee_edu_id','tbl_internee_education.discipline as discipline', 'tbl_internee_education.degrees as degrees', 'tbl_internee_education.institute as institute',
               'tbl_internee_education.session_paid as session_paid', 'tbl_internee_education.completion_date_paid as completion_date_paid'
               ,'tbl_internee_education.total_marks_paid as total_marks_paid', 'tbl_internee_education.obtain_marks_paid as obtain_marks_paid','tbl_internee_education.grade_paid as grade_paid',
               'tbl_internee_education.cgpa_paid as cgpa_paid', 'tbl_internee_education.enrollment_no as enrollment_no', 'tbl_internee_education.addmission_date as addmission_date', 'tbl_internee_education.current_semester as current_semester'
               , 'tbl_internee_education.yeared as yeared', 'tbl_internee_education.proposed_month as proposed_month',
               'tbl_internee_education.cnic_edoc as cnic_edoc', 'tbl_internee_education.domicle_edoc as domicle_edoc',
               'tbl_internee_education.transcript_edoc as ts_edoc'
              )
              ->first();
    // echo "<pre>"; print_r($data);
        return view('internee_deu.detail', compact('data'));
    }
    public function store(Request $request)
    {
        $messages = array(
            'cnic_no' => 'The CNIC field is required.',
            'name' => 'The Name field is required.',
        );
//
        $validator = Validator::make($request->all(), [

            'cnic_no' => 'required|unique:tbl_internee',
            'name' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());

		$ord = Intrnee::orderBy('internee_id', 'desc')->first();
        $intrenee = new Intrnee();
       $int_id= $intrenee->internee_id = ($ord) ? $ord->internee_id + 1 : 1;
        $intrenee->name = $request->input('name');
        $intrenee->news_paper_name = $request->input('name_newspaper');
        $intrenee->pubication_date = ($request->input('date_publication')) ? date('Y-m-d', strtotime($request->input('date_publication'))) : null;
        $intrenee->cnic_no = $request->input('cnic_no');
        $intrenee->domicle = $request->input('domicile_dist');
        $intrenee->fname = $request->input('father_name');
        $intrenee->gender = $request->input('gender');
        $intrenee->email = $request->input('email');
        $intrenee->mobile_no = $request->input('mobile');
        $intrenee->telephone_no = $request->input('phone');
        $intrenee->present_address = $request->input('pre_address');
        $intrenee->present_tehsil = $request->input('pre_tehsil');
        $intrenee->present_district = $request->input('pre_district');
        $intrenee->permanent_address = $request->input('prm_address');
        $intrenee->permanent_tehsil = $request->input('prm_tehsil');
        $intrenee->permanent_district = $request->input('prm_district');
      //  echo "<pre>"; print_r($intrenee); die;
        $intrenee->save();
        $int_edu = Intrnee_edu::orderBy('internee_edu_id', 'desc')->first();
        $intrenee_edu = new Intrnee_edu();
        $intrenee_edu->internee_edu_id = ($int_edu) ? $int_edu->internee_edu_id + 1 : 1;
        $intrenee_edu->internee_id = $int_id;
        $intrenee_edu->degrees = $request->input('degree');
        $intrenee_edu->discipline = $request->input('discipline');
        $intrenee_edu->institute = $request->input('uni_name');
        $intrenee_edu->session_paid = $request->input('session');
        $intrenee_edu->completion_date_paid = ($request->input('date_comp')) ? date('Y-m-d', strtotime($request->input('date_comp'))) : null;
        $intrenee_edu->total_marks_paid = $request->input('total_marks' );
        $intrenee_edu->obtain_marks_paid = $request->input('obtained_marks');
        $intrenee_edu->grade_paid = $request->input('grade');
        $intrenee_edu->cgpa_paid = $request->input('cgpa');
        if(is_string(request()->get('cnic'))){
            $intrenee_edu->cnic_edoc = 1;
        }
        else {
            $intrenee_edu->cnic_edoc = 0;
        }
        if(is_string(request()->get('domicile'))){
            $intrenee_edu->domicle_edoc = 1;
        }
        else {
            $intrenee_edu->domicle_edoc = 0;
        }
        if(is_string(request()->get('transcript'))){
            $intrenee_edu->transcript_edoc = 1;
        }
        else {
            $intrenee_edu->transcript_edoc = 0;
        }
       // echo "<pre>"; print_r($intrenee_edu); die;
        $intrenee_edu->save();
        Session::flash('success', 'Data Add successfully.');
        return redirect('internee_edu_detail/'.$intrenee_edu->internee_id);

    }

    /**
     * Display the specified resource.
     *
        * @param  int  $id
    * @return \Illuminate\Http\Response
        */
    public function cnic_check($id)
    {
         //echo $id;die;
       // $cnic = DB::table('tbl_internee')->where('cnic', '=', $id)->select('cnic')->first();
//        if (!empty($cnic)) {
            $data = DB::table('tbl_internee')->join('tbl_internee_education', 'tbl_internee.internee_id', '=', 'tbl_internee_education.internee_id')
                ->where('tbl_internee.cnic_no', '=', $id)
                ->select('tbl_internee.internee_id as internee_id', 'tbl_internee.name as name', 'tbl_internee.news_paper_name as news_paper_name', 'tbl_internee.pubication_date as pubiction_date',
                    'tbl_internee.fname as fname', 'tbl_internee.cnic_no as cnic_no', 'tbl_internee.gender as gender',
                    'tbl_internee.email as email', 'tbl_internee.mobile_no as mobile', 'tbl_internee.telephone_no as phone_no', 'tbl_internee.present_address as present_address'
                    , 'tbl_internee.present_district as present_district', 'tbl_internee.present_tehsil as present_tehsil', 'tbl_internee.domicle as domicle_dist',
                    'tbl_internee.permanent_address as permanent_address', 'tbl_internee.permanent_district as permanent_district', 'tbl_internee.permanent_tehsil as permanent_tehsil',
                    'tbl_internee_education.internee_edu_id as internee_edu_id', 'tbl_internee_education.discipline as discipline', 'tbl_internee_education.degrees as degrees', 'tbl_internee_education.institute as institute',
                    'tbl_internee_education.session_paid as session_paid', 'tbl_internee_education.completion_date_paid as completion_date_paid'
                    , 'tbl_internee_education.total_marks_paid as total_marks_paid', 'tbl_internee_education.obtain_marks_paid as obtain_marks_paid', 'tbl_internee_education.grade_paid as grade_paid',
                    'tbl_internee_education.cgpa_paid as cgpa_paid', 'tbl_internee_education.enrollment_no as enrollment_no', 'tbl_internee_education.addmission_date as addmission_date', 'tbl_internee_education.current_semester as current_semester'
                    , 'tbl_internee_education.yeared as yeared', 'tbl_internee_education.proposed_month as proposed_month',
                    'tbl_internee_education.cnic_edoc as cnic_edoc', 'tbl_internee_education.domicle_edoc as domicle_edoc',
                    'tbl_internee_education.transcript_edoc as ts_edoc'
                )
                ->first();
//        }
//        else
//        $data = " ";
        // echo "<pre>"; print_r($data); die;
        return json_encode($data);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // "test";die;
        $page_title = 'Edit Institute';
        $data = Tranings::find($id);
        $training_types = DB::table('tbl_training_head')->where('th_type', '=', 5)->get();
        $training_type= array(null => 'Select Training Type');
        foreach($training_types as $authorty1){
            $training_type[$authorty1->th_id] = $authorty1->th_title; //. ' ' . $package->contract_code;
        }

        $organizations = DB::table('tbl_training_head')->where('th_type', '=',1 )->get();
        $organization = array(null => 'Select organization');
        foreach($organizations as $authorty2){
            $organization[$authorty2->th_id] = $authorty2->th_title; //. ' ' . $package->contract_code;
        }
        $place_ids = DB::table('tbl_training_head')->where('th_type', '=',6 )->get();
        $place_id = array(null => 'Select Place');
        foreach($place_ids as $authorty3){
            $place_id[$authorty3->th_id] = $authorty3->th_title; //. ' ' . $package->contract_code;
        }



        $earned_through =  ['' => 'Select Order Type', 'CB' => 'Capacity Building', 'OO' => 'Official Obligation', 'OV' => 'Official Visit'];
        return view('institute.edit', compact('page_title','data','training_type','organization','place_id','earned_through'));

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
        $messages = array(
            'required' => 'The :attribute field is required.',
        );

        $validator = Validator::make($request->all(), [

            'organization' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());



        $order = Tranings::find($id);
        $expend = $request->input('expend');
        $training_type = $request->input('training_type');
        $earned = $request->input('earned');
        $organization = $request->input('organization');
        $place_id= $request->input('place_id');
        $comments= $request->input('comments');

		if($request->hasFile('e_doc')) {
            $file = $request->file('e_doc');            


            /// new file name
            $new_filename = 'Training_'.$id;

            $path = 'public/NHA-IS/TRAININGS';

            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);

            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $orderEdoc = $completeUrl;

        }
        else{
            if($order->e_doc)
                $orderEdoc = $order->e_doc;
            else
                $orderEdoc = '';

        }

        $updateFields = array(
            'expenditure' => $expend,
            'training_type_id' => $training_type,
            'earned_through' => $earned,
            'organization_id' => $organization,
            'place_id' => $place_id,
            'comments' => $comments,
            'training_edoc' => $orderEdoc
        );

        DB::table('TBL_TRAININGS')->where('training_id', '=', $id)->update($updateFields);


        //after order save
       /* if(Input::hasFile('e_doc')) {
            $path = 'public/NHA-IS/ORDER';
            $table = 'TBL_ORDER';
            $primary_field = 'ORDER_ID';
            $file = Input::file('e_doc');
            $edoc_field = 'E_DOC ';

            $uploader = new UploadController();
            $uploader->upload_edoc($file, 'ORDER_'.$order->order_id, $path, $table, $primary_field, $order->order_id, $edoc_field);
        }*/
        Session::flash('success', 'Training updated successfully.');
        return redirect('hrd');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //echo $id; die;
         DB::table('TBL_TRAINING_COURSE')->where('training_id', '=', $id)->delete();
		// Tranings::where('training_id', '=', $id)->delete();
        Session::flash('success', ' Training has been deleted successfully.');

        return redirect('t_course/'.$id );
    }
}
