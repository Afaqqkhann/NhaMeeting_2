<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Order;
use DB;
use Illuminate\Http\Request;
use Input;
use Session;
use Validator;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        ini_set('max_execution_time', 5000);
        ini_set('memory_limit', '5000M');
//        $page_title = 'family';
        //        $data = DB::table('TBL_FAMILY ')->orderBy('FAMILY_ID', 'DESC')
        //            ->join('TBL_EMP', 'TBL_FAMILY.emp_id', '=', 'TBL_EMP.emp_id')
        //            ->join('TBL_RELATION', 'TBL_FAMILY.relationship', '=', 'TBL_RELATION.r_id')
        //            ->get();
        //
        //        return view('family.index', compact('page_title', 'data'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $page_title = 'Add Leave Info';
        $lType = DB::table('TBL_LEAVE_TYPE')->orderBy('LT_TITLE','ASC')->get();
        $leaveTypes = array(null => 'Select Leave Type');
        foreach($lType as $leave){
            $leaveTypes[$leave->lt_id] = $leave->lt_title;
        }

        $order = ['' => 'Select Order'] + Order::lists('order_subject', 'order_id')->all();
        return view('leave.create', compact('page_title', 'leaveTypes', 'id', 'order'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [

            'leave_type_id.required' => 'The Remarks must be selected.',            
                    ];
        $validation = Validator::make($request->all(),
            [
                'leave_type_id'  =>     'required',
               
            ],$messages);
        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $record = Leave::orderBy('leave_id', 'desc')->first();
        $leaveType = new Leave();
        $leaveType->leave_id = ($record) ? $record->leave_id + 1 : 1;
        $leaveType->emp_id = $request->input('id');
        $leaveType->leave_type_id = $request->input('leave_type_id');
        $leaveType->period_duty_from_2 = \Naeem\Helpers\Helper::convert_date($request->input('p_d_from')); // ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('p_d_from')))) : '';
        $leaveType->period_duty_to_3 = \Naeem\Helpers\Helper::convert_date($request->input('p_d_to')); // ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('p_d_to')))) : '';
        $leaveType->period_ymd_4 = $request->input('ymd');
        $leaveType->full_calender_month_5 = $request->input('full_month');
        $leaveType->leave_earned_6 = $request->input('earned');
        $leaveType->leave_credit_7 = $request->input('credit');
        $leaveType->leave_from_8 = \Naeem\Helpers\Helper::convert_date($request->input('l_from'));// ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('l_from')))) : '';
        $leaveType->leave_to_9 = \Naeem\Helpers\Helper::convert_date($request->input('l_to')); // ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('l_to')))) : '';
        $leaveType->leave_10 = $request->input('c_10');
        $leaveType->leave_11 = $request->input('c_11');
        $leaveType->leave_12 = $request->input('c_12');
        $leaveType->leave_13 = $request->input('c_13');
        $leaveType->leave_14 = $request->input('c_14');
        $leaveType->leave_15 = $request->input('c_15');
        $leaveType->leave_16 = $request->input('c_16');
        $leaveType->leave_17 = $request->input('c_17');
        $leaveType->leave_18 = $request->input('c_18');
        $leaveType->leave_19 = $request->input('c_19');
        $leaveType->leave_20 = $request->input('c_20');
        $leaveType->leave_21 = $request->input('c_21');
        $lType = LeaveType::where('lt_id',$request->input('leave_type_id'))->first();
        $leaveType->remarks = $lType->lt_title;
		$leaveType->comments = $request->input('comments');
        $leaveType->order_id = $request->input('order');
        $leaveType->special_leave = $request->input('sl');
        $leaveType->leave_status = 1;
        $leaveType->save();
        Session::flash('success', 'leave Info added successfully.');
        return Redirect('employee/emp_detail/' . $request->input('id'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Leave Info';
        $data = Leave::find($id);
        //echo "<pre>"; print_r($data); die;
        return view('leave.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Leave Info';
        $data = Leave::find($id);

        $lType = DB::table('TBL_LEAVE_TYPE')->orderBy('LT_TITLE','ASC')->get();
        $leaveTypes = array(null => 'Select Leave Type');
        foreach($lType as $leave){
            $leaveTypes[$leave->lt_id] = $leave->lt_title;
        }
       
        $order = ['' => 'Select Order'] + Order::lists('order_subject', 'order_id')->all();
        
        return view('leave.edit', compact('page_title', 'leaveTypes', 'data', 'id', 'order'));

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

            'leave_type_id.required' => 'The Remarks must be selected.',            
        ];
        $validation = Validator::make($request->all(),
            [
                'leave_type_id'  =>     'required',
                
            ], $messages);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $duty_from_2 = \Naeem\Helpers\Helper::convert_date($request->input('p_d_from'));
        $duty_to_3 = \Naeem\Helpers\Helper::convert_date($request->input('p_d_to'));

        $leave_from_8 = \Naeem\Helpers\Helper::convert_date($request->input('l_from'));
        $leave_to_9 = \Naeem\Helpers\Helper::convert_date($request->input('l_to'));
        $leaveType = LeaveType::where('lt_id',$request->input('leave_type_id'))->first();
        $departmentArray = array(
            'emp_id' => $request->input('id'),
            'leave_type_id' => $request->input('leave_type_id'),
            'period_duty_from_2' => $duty_from_2,
            'period_duty_to_3' => $duty_to_3,
            'period_ymd_4' => $request->input('ymd'),
            'full_calender_month_5' => $request->input('full_month'),
            'leave_earned_6' => $request->input('earned'),
            'leave_credit_7' => $request->input('credit'),
            'leave_from_8' => $leave_from_8,
            'leave_to_9' => $leave_to_9,
            'leave_10' => $request->input('c_10'),
            'leave_11' => $request->input('c_11'),
            'leave_12' => $request->input('c_12'),
            'leave_13' => $request->input('c_13'),
            'leave_14' => $request->input('c_14'),
            'leave_15' => $request->input('c_15'),
            'leave_16' => $request->input('c_16'),
            'leave_17' => $request->input('c_17'),
            'leave_18' => $request->input('c_18'),
            'leave_19' => $request->input('c_19'),
            'leave_20' => $request->input('c_20'),
            'leave_21' => $request->input('c_21'),
            'order_id' => $request->input('order'),
            'special_leave' => $request->input('sl'),
            'remarks' => $leaveType->lt_title,
            'comments' => $request->input('comments'),
            'leave_status' => 1,
        );
        DB::table('TBL_LEAVE')->where('LEAVE_ID', '=', $id)->update($departmentArray);
        Session::flash('success', 'Leave Info updated successfully.');
        return Redirect('employee/emp_detail/' . $request->input('id'));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Leave::find($id);
        DB::table('TBL_LEAVE')->where('LEAVE_ID', '=', $id)->delete();
        Session::flash('success', 'Leave Info has been deleted successfully.');
        return Redirect('employee/emp_detail/' . $data->emp_id);
    }
}
