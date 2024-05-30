<?php

namespace App\Http\Controllers;

use App\Models\Discipline;
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

class TraineeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       // echo "test"; die;
        $semester = ['' => 'Select Semester', '1' => 'Ist', '2' => '2nd', '3' => '3rd','4' => '4th','5' => '5th','6' => '6th','7' => '7th','8' => '8th'];
        $year = ['' => 'Select Year', '1' => 'Ist', '2' => '2nd', '3' => '3rd','4' => '4th'];
        $discipline = ['' => 'Select Discipline'] + Discipline::lists('discipline_title', 'discipline_title')->all();
        $proposed_month = ['' => 'Select Month','July - 2021'=>'July - 2021','August - 2021'=>'August - 2021'
			,'September - 2021'=>'September - 2021','October - 2021'=>'October - 2021','November - 2021'=>'November - 2021'
			,'December - 2021'=>'December - 2021','January - 2022'=>'January - 2022','February - 2022'=>'February - 2022'
			,'March - 2022'=>'March - 2022','April - 2022'=>'April - 2022','May - 2022'=>'May - 2022'
			,'June - 2022'=>'June - 2022'];
		$gender = ['' => 'Select Gender', 'Male' => 'Male', 'Female' => 'Female'];
		return view('trainee.index',compact('semester','gender', 'year', 'discipline','proposed_month'));
    }
	
	 public function index_test()
    {
        // echo "test"; die;

        return view('trainee.loader');
    }
	
	
	
	
	/*** Create Form **/
	public function create(){
		// echo "test"; die;
        $semester = ['' => 'Select Semester', '1' => 'Ist', '2' => '2nd', '3' => '3rd','4' => '4th','5' => '5th','6' => '6th','7' => '7th','8' => '8th'];
        $year = ['' => 'Select Year', '1' => 'Ist', '2' => '2nd', '3' => '3rd','4' => '4th'];
        $discipline = ['' => 'Select Discipline'] + Discipline::lists('discipline_title', 'discipline_title')->all();
        $proposed_month = ['' => 'Select Month','July - 2021'=>'July - 2021','August - 2021'=>'August - 2021'
			,'September - 2021'=>'September - 2021','October - 2021'=>'October - 2021','November - 2021'=>'November - 2021'
			,'December - 2021'=>'December - 2021','January - 2022'=>'January - 2022','February - 2022'=>'February - 2022'
			,'March - 2022'=>'March - 2022','April - 2022'=>'April - 2022','May - 2022'=>'May - 2022'
			,'June - 2022'=>'June - 2022'];
        return view('trainee.create',compact('semester', 'year', 'discipline','proposed_month'));
	}
	
	
    public function printForm($id)
    {

       $data = DB::table('tbl_internee')->join('tbl_internee_education', 'tbl_internee.internee_id', '=', 'tbl_internee_education.internee_id' )
              ->where('tbl_internee.internee_id', '=', $id)
           ->select('tbl_internee.internee_id as internee_id', 'tbl_internee.name as name','tbl_internee.news_paper_name as news_paper_name','tbl_internee.pubication_date as pubiction_date',
               'tbl_internee.fname as fname','tbl_internee.cnic_no as cnic_no', 'tbl_internee.gender as gender',
               'tbl_internee.email as email', 'tbl_internee.mobile_no as mobile', 'tbl_internee.telephone_no as phone_no','tbl_internee.present_address as present_address'
               , 'tbl_internee.present_district as present_district', 'tbl_internee.present_tehsil as present_tehsil','tbl_internee.domicle as domicle_dist',
               'tbl_internee.permanent_address as permanent_address','tbl_internee.permanent_district as permanent_district','tbl_internee.permanent_tehsil as permanent_tehsil',
               'tbl_internee_education.internee_edu_id as internee_edu_id','tbl_internee_education.discipline as discipline', 'tbl_internee_education.degrees as degrees', 'tbl_internee_education.institute as institute',
               'tbl_internee_education.session_paid as session_paid',
             
              'tbl_internee_education.enrollment_no as enrollment_no', 'tbl_internee_education.addmission_date as addmission_date', 'tbl_internee_education.current_semester as current_semester'
               , 'tbl_internee_education.yeared as yeared', 'tbl_internee_education.proposed_month as proposed_month'
              
              )
              ->first();
     // echo "<pre>"; print_r($data);
        return view('trainee.detail', compact('data'));
    }
    public function store(Request $request)
    {
        $messages = array(
            'cnic_no' => 'The CNIC field is required.',
            'name' => 'The Name field is required.',
            
            'discipline' => 'The discipline field is required.',
            'father_name' => 'The father name field is required.',
            'gender' => 'The gender field is required.',            
            'mobile' => 'The mobile field is required.',            
            'pre_district' => 'The Present district field is required.',
            'pre_tehsil' => 'The Present tehsil field is required.',
            'pre_address' => 'The Present address field is required.',
            'prm_district' => 'The Permanent address field is required.',
            'prm_tehsil' => 'The Permanent tehsil field is required.',
            'prm_address' => 'The Permanent address field is required.',
            'domicile_dist' => 'The domicile dist field is required.',
            'degree' => 'The degree field is required.',
            'uni_name' => 'The university name field is required.',
            'enr_no' => 'The university enrollment no. field is required.',
            'adm_date' => 'The date of admission field is required.',
            'semester' => 'The semester field is required.',
            'year' => 'The year field is required.',           
            'p_month' => 'The proposed month for internship field is required.',
            
        );
        $validator = Validator::make($request->all(), [
            'cnic_no' => 'required|unique:tbl_internee',
            'name' => 'required',  
            'discipline' => 'required',
            'father_name' => 'required',
            'gender' => 'required',            
            'mobile' => 'required',          
            'pre_district' => 'required',
            'pre_tehsil' => 'required',
            'pre_address' => 'required',
            'prm_district' => 'required',
            'prm_tehsil' => 'required',
            'prm_address' => 'required',
            'domicile_dist' => 'required',
            'degree' => 'required',
            'uni_name' => 'required',
            'enr_no' => 'required',
            'adm_date' => 'required',
            'semester' => 'required',
            'year' => 'required',
            'p_month' => 'required',           
            

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
		
		$ord = Intrnee::orderBy('internee_id', 'desc')->first();
        $intrenee = new Intrnee();
       $int_id= $intrenee->internee_id = ($ord) ? $ord->internee_id + 1 : 1;
        $intrenee->program_type = 'Unpaid';
        $intrenee->name = $request->input('name');
        //$intrenee->news_paper_name = $request->input('name_newspaper');
        //$intrenee->pubication_date = ($request->input('date_publication')) ? date('Y-m-d', strtotime($request->input('date_publication'))) : null;
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
        if($intrenee->save()){
			$int_edu = Intrnee_edu::orderBy('internee_edu_id', 'desc')->first();
			$intrenee_edu = new Intrnee_edu();
			$intrenee_edu->internee_edu_id = ($int_edu) ? $int_edu->internee_edu_id + 1 : 1;
			$intrenee_edu->internee_id = $int_id;
			$intrenee_edu->degrees = $request->input('degree');
			$intrenee_edu->discipline = $request->input('discipline');
			$intrenee_edu->institute = $request->input('uni_name');
			$intrenee_edu->enrollment_no = $request->input('enr_no');
			$intrenee_edu->addmission_date = ($request->input('adm_date')) ? date('Y-m-d', strtotime($request->input('adm_date'))) : null;
			$intrenee_edu->current_semester = $request->input('semester' );
			$intrenee_edu->proposed_month = $request->input('p_month');
			$intrenee_edu->yeared = $request->input('year');
			
		   // echo "<pre>"; print_r($intrenee_edu); die;
			$intrenee_edu->save();
		}
        Session::flash('success', 'Your application form has been submitted successfully.');
        return redirect('trainee/print/'.$int_id);

    }
	/// CNIC Check
	public function cnic_check($id,$prog_type)
    {
         //echo $id;die;
       // $cnic = DB::table('tbl_internee')->where('cnic', '=', $id)->select('cnic')->first();
//        if (!empty($cnic)) {
            $data = DB::table('tbl_internee_education')
			->rightJoin('tbl_internee', 'tbl_internee.internee_id', '=', 'tbl_internee_education.internee_id')
                   ->leftJoin('tbl_internee_batch', 'tbl_internee.batch_id', '=', 'tbl_internee_batch.batch_id')
				->where('tbl_internee.cnic_no', '=', $id)
				->where('tbl_internee.program_type', '=', $prog_type)
                ->select('tbl_internee.internee_id as internee_id', 'tbl_internee.name as name','tbl_internee_education.displine_id', 'tbl_internee.news_paper_name as news_paper_name', 'tbl_internee.pubication_date as pubiction_date',
                    'tbl_internee.fname as fname', 'tbl_internee.cnic_no as cnic_no', 'tbl_internee.gender as gender',
                    'tbl_internee.email as email', 'tbl_internee.mobile_no as mobile', 'tbl_internee.telephone_no as phone_no', 'tbl_internee.present_address as present_address'
                    , 'tbl_internee.present_district as present_district', 'tbl_internee.present_tehsil as present_tehsil', 'tbl_internee.domicle as domicle_dist',
                    'tbl_internee.permanent_address as permanent_address', 'tbl_internee.permanent_district as permanent_district', 'tbl_internee.permanent_tehsil as permanent_tehsil',
                    'tbl_internee_education.internee_edu_id as internee_edu_id', 'tbl_internee_education.discipline as discipline', 'tbl_internee_education.degrees as degrees', 'tbl_internee_education.institute as institute',
                    'tbl_internee_education.session_paid as session_paid', 'tbl_internee_education.completion_date_paid as completion_date_paid'
                    , 'tbl_internee_education.total_marks_paid as total_marks_paid', 'tbl_internee_education.obtain_marks_paid as obtain_marks_paid', 'tbl_internee_education.grade_paid',
                    'tbl_internee_education.cgpa_paid as cgpa_paid', 'tbl_internee_education.enrollment_no as enrollment_no', 'tbl_internee_education.addmission_date as addmission_date', 'tbl_internee_education.current_semester as current_semester'
                    , 'tbl_internee_education.yeared as yeared', 'tbl_internee_education.proposed_month as proposed_month',
                    'tbl_internee_education.cnic_edoc as cnic_edoc', 'tbl_internee_education.domicle_edoc as domicle_edoc',
                    'tbl_internee_education.transcript_edoc as ts_edoc','tbl_internee.joining_status','tbl_internee.internee_status',
					
					 'tbl_internee_batch.batch_id as batch_id', 'tbl_internee_batch.batch_title', 'tbl_internee_batch.date_from as date_from', 'tbl_internee_batch.date_to as date_to',
               'tbl_internee_batch.comments as batch_comments'
                )
                ->first();

        // echo "<pre>"; print_r($data); die;
        return json_encode($data);
    }

    /**
     * Display the specified resource.
     *
        * @param  int  $id
    * @return \Illuminate\Http\Response
        */
    public function show($id)
    {
        //echo $id; die;
        $page_title = 'Organization Show';
        $training_course = DB::table('V_TRAINING_COURSE')->where('training_id', '=',$id)->first();
       // echo "<pre>"; print_r($training_course); die;
        $emp_nomination = DB::table('V_TRAINING_NOMINATION')->where('training_id' ,'=', $id)->orderBy('TN_ID', 'DESC')
          ->get();

        return view('organization.show', compact('page_title','emp_nomination', 'training_course'));
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
