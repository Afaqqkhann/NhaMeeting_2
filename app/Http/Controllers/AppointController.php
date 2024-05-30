<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Appoint;
use App\Models\Order;
use App\Models\Post;
use App\Models\Sanction;
use Auth;
use DB;
use Illuminate\Http\Request;
use Input;
use Session;
use Validator;
use Naeem\Helpers\Helper;

class AppointController extends Controller
{

    public function __construct()
    {
        /*if (!Auth::user()->can('appointment')) {
            abort(403);
        }*/
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        ini_set('max_execution_time', 5000);
        ini_set('memory_limit', '5000M');
        $page_title = 'Appointment';
        $data = DB::table('TBL_APPOINTMENT ')->orderBy('APPOINTMENT_ID', 'DESC')
            ->join('TBL_EMP', 'TBL_APPOINTMENT.emp_id', '=', 'TBL_EMP.emp_id')
            ->join('TBL_SANCTION', 'TBL_APPOINTMENT.sanction_id', '=', 'TBL_SANCTION.sanction_id')
            ->get();
//        echo "<pre>";
        //        print_r($data); die;

        return view('appoint.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $page_title = 'Add Appointment';
        //// Get Sections
        $sanction = ['' => 'Select Sanction/Post'] + Sanction::lists('strength_name', 'sanction_id')->all();
        $against = ['' => 'Select Against Type', '1' => 'Regular', '2' => 'PC 1', '3' => 'DW/Regular', '4' => 'Contract/Regular', '5' => 'Contract', '6' => 'Daily Wages'];
        $appoint_type = ['' => 'Select Appointment Type', 'Contract ' => 'contract', 'Daily Wages' => 'Daily Wages', 'Deputation' => 'Deputation',
            'Direct' => 'Direct', 'Inducted' => 'Inducted', 'Promoted' => 'Promoted', 'Promotion' => 'Promotion', 'Re-Appointed' => 'Re-Appointed'
            ,'Regular' => 'Regular', 'Reinstated' => 'Reinstated', 'Work Charge' => 'Work Charge'];
        $post = ['' => 'Select Post'] + Post::lists('post_name', 'post_name')->all();
        $order = ['' => 'Select order'] + Order::lists('order_no', 'order_id')->all();
		
		
		$quotas = Appoint::select('appointment_quota')->distinct()->get();
		foreach($quotas as $key => $quot)
			$quotas_arr[$quot->appointment_quota] = $quot->appointment_quota;
			
		
		$service_types = Appoint::select('service_type')->distinct()->get();
		foreach($service_types as $key => $st)
			$serv_types_arr[$st->service_type] = $st->service_type;
		//echo '<pre>';print_r($quotas);die;

        return view('appoint.create', compact('page_title','serv_types_arr', 'quotas_arr', 'order', 'post', 'sanction', 'id', 'against', 'appoint_type'));
    }
	


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(),
            [
                'sanction_id' => 'required',
//                'post'  =>     'required',
            ]);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $record = Appoint::orderBy('appointment_id', 'desc')->first();
        $appoint = new Appoint();

        $appoint->appointment_id = ($record) ? $record->appointment_id + 1 : 1;
        $appoint->emp_id = $request->input('emp_id');
        $appoint->bs = $request->input('bs');
        $appoint->type_of_appointment = $request->input('type_of_appointment');
        $appoint->appointed_against = $request->input('appointed_against');
        $appoint->sanction_id = $request->input('sanction_id');
        $appoint->appointment_quota = $request->input('appointment_quota');
        $appoint->order_id = $request->input('order_id');
        $appoint->post_name = $request->input('post_name');
        $appoint->date_of_appointment = Helper::convert_date($request->input('date_of_appointment'));      
        $appoint->service_type = $request->input('service_type');
        $appoint->adver_no = $request->input('adver_no');
        $appoint->app_authority = $request->input('app_authority');
        $appoint->exam_held = $request->input('exam_held');
        
        $appoint->merit_no = $request->input('merit_no');
        $appoint->quota_adv_date = ($request->input('quota_adv_date')) ? date('Y-m-d', strtotime('quota_adv_date')) : '';
        $appoint->through_adver = $request->input('through_adver');
        $appoint->remarks = $request->input('remarks');

        $appoint->save();
        Session::flash('success', 'Appointment added successfully.');
        return Redirect('employee/emp_detail/' . $request->input('emp_id'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Appointment';
        $data = Appoint::find($id);
        // echo "<pre>"; print_r($data); die;

        return view('appoint.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Appoint';
        $data = Appoint::find($id);

        $against = ['' => 'Select Appointed Against', '1' => 'Regular', '2' => 'PC 1', '3' => 'DW/Regular', '4' => 'Contract/Regular', '5' => 'Contract', '6' => 'Daily Wages'];
        $appoint_type = ['' => 'Select Appointment Type', 'Contract ' => 'contract', 'Daily Wages' => 'Daily Wages', 'Deputation' => 'Deputation',
            'Direct' => 'Direct', 'Inducted' => 'Inducted', 'Promoted' => 'Promoted', 'Promotion' => 'Promotion', 'Re-Appointed' => 'Re-Appointed'
            ,'Regular' => 'Regular', 'Reinstated' => 'Reinstated', 'Work Charge' => 'Work Charge'];
        $sanction = ['' => 'Select Sanction/Post'] + Sanction::lists('strength_name', 'sanction_id')->all();
        $post = ['' => 'Select Post'] + Post::lists('post_name', 'post_name')->all();
        $order = ['' => 'Select order'] + Order::lists('order_no', 'order_id')->all();
		
		$quotas = Appoint::select('appointment_quota')->distinct()->get();
		foreach($quotas as $key => $quot)
			$quotas_arr[$quot->appointment_quota] = $quot->appointment_quota;
			
		
		$service_types = Appoint::select('service_type')->distinct()->get();
		foreach($service_types as $key => $st)
			$serv_types_arr[$st->service_type] = $st->service_type;
			
        return view('appoint.edit', compact('page_title','serv_types_arr', 'quotas_arr','data', 'sanction', 'post', 'order', 'against', 'appoint_type'));

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
        $messages = [
            'post_name.required' => 'The Post Name field is required.',

        ];

        $validation = Validator::make($request->all(),
            [
                'sanction_id' => 'required',

            ], $messages);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $data = Appoint::find($id);

        DB::transaction(function () use ($data, $request, $id) {

        $appointment_date = ($request->input('date_of_appointment')) ? date('Y-m-d', strtotime($request->input('date_of_appointment'))) : '';
        $quota_date = ($request->input('quota_adv_date')) ? date('Y-m-d', strtotime($request->input('quota_adv_date'))) : '';
        $appoint_status = ($request->input('appointment_status')) ? $request->input('appointment_status') : '0';

       // echo 'appoint'.$appoint_status;die;
		$departmentArray = array(

            'bs' => $request->input('bs'),
            'type_of_appointment' => $request->input('type_of_appointment'),
            'appointed_against' => $request->input('appointed_against'),
            'sanction_id' => $request->input('sanction_id'),
            'appointment_quota' => $request->input('appointment_quota'),
            'order_id' => $request->input('order_id'),
            'post_name' => $request->input('post_name'),
            'date_of_appointment' => $appointment_date,
            'service_type' => $request->input('service_type'),
            'adver_no' => $request->input('adver_no'),
            'app_authority' => $request->input('app_authority'),
            'exam_held' => $request->input('exam_held'),
            'merit_no' => $request->input('merit_no'),
            'quota_adv_date' => $quota_date,
            'through_adver' => $request->input('through_adver'),
            'remarks' => $request->input('remarks'),
			'appointment_status' => $appoint_status,

        );		
            
            if($appoint_status == '1'){			
                DB::table('TBL_APPOINTMENT')->where('emp_id', '=', $data->emp_id)->update(['appointment_status' => '0']);				
                DB::table('TBL_EMP')->where('emp_id', '=', $data->emp_id)->update(['date_of_appointment' => $appointment_date]);				
            }			

            DB::table('TBL_APPOINTMENT')->where('appointment_id', '=', $id)->update($departmentArray);
            

            Session::flash('success', 'Appointment updated successfully.');
        });

        return Redirect('employee/emp_detail/' . $data->emp_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Appoint::find($id);
        DB::table('TBL_APPOINTMENT')->where('appointment_id', '=', $id)->delete();
        Session::flash('success', 'Appointment has been deleted successfully.');
        return Redirect('employee/emp_detail/' . $data->emp_id);

    }
}
