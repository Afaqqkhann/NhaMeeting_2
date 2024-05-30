<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Session;

class StartEndController extends Controller
{
	
	public function __construct() {
		
		$this->middleware('auth');
        if(!Auth::user()->can('tenure_info'))
            abort(403);
	}

	/**
	Tenure Form
     **/

    public function tenure_form()
    {
        $data = DB::table('V_PERIOD')->select('emp_id',
'emp_name',
'f_h_name',
            'dob',
            'dor',
            'gender',
            'designation',
            'place_id',
            'reporting_officer',
            'section_id',
            'package_id',
            'remarks',
            'actual_bs',
            'domicile',
            'service_type',
            'date_of_appointment',
            'type_of_appointment',
            'through_adver',
            'carrier_id',
            'sanction_id' ,
            'charge_id',
            'bs', 'post_name',
            'joining_date',


            'joining',

            'job_status')
            ->get();
       // print_r($data);die;
        return view('tenure_policy.tenure',compact('data'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        return view('tenure_policy.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit()
    {
        $data = DB::table('START_END_DATE')->first();
        return view('tenure_policy.start_end',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request)
    {
        //echo 'test';die;
        $startDate = ($request->input('start_dated')) ? date('Y-m-d', strtotime($request->input('start_dated'))) : null;
        $endDate = ($request->input('end_dated')) ? date('Y-m-d', strtotime($request->input('end_dated'))) : null;
        DB::table('START_END_DATE')->update(['start_dated' => $startDate, 'end_dated' => $endDate]);
        Session::flash('success', 'Tenure updated successfully.');

        return redirect('/start_end_date');
    }


}
