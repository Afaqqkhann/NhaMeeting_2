<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\User;
use App\Models\Employees\Employees;
use Auth;

class RecruitmentController extends Controller
{
	public function __construct() {
		
		$this->middleware('auth');
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if(!Auth::user()->can('recruitment_dss_dashboard'))
            abort(403);
        //
		$page_title = 'Recruitment Dashboard';
		$years = DB::table('V_APPOINTMENT_YEARS')->select('years')->distinct('years')->orderby('years', 'desc')->get();
		
        return view('recruitment_dashboard.index', compact('page_title', 'years'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param $year
     * @return string
     */
	   public function acr(){
        $page_title = 'Acr Remaining';
        $data = DB::table('V_EMP_DB')->where('job_status', '=', 'On Job')->where('ACR_REMAINING', '!=', '0')->select([
            'FP_ID',
            'EMP_ID',
            'EMP_NAME',
            'F_H_NAME',
            'DOB',
            'CNIC',
            'DOMICILE',
            'BS',
            'REF_FILE_NO',
            'DATE_OF_APPOINTMENT',
            'TYPE_OF_APPOINTMENT',
            'EMP_PENSION_STATUS',
            'POSTING_DATE',
            'POSTING_TENURE',
            'TRANSFER_DATE',
            'TRANSFER_TENURE',
            'TOTAL_POSTING',
            'TOTAL_TRANSFER',
            'ACR_REMAINING',
            'ASSET_REMAINING',
            'APPOINTMENT_YEAR',
            'APPOINTMENT_MONTH',
			'JOB_STATUS'
        ])->get();;
        //echo "<pre>"; print_r($data); die;


        return view('employees.employees_list1', compact('page_title','data'));
    }
	 public function confirmation(){
		 //echo "test";die;
		  $page_title = 'Confirmation ';
		   $data = DB::table('V_EMP_DB')->where('job_status', '=', 'On Job')->where('TRANSFER_TENURE', '>', '5')->where('BS', '>', '14')->select([
            'FP_ID',
            'EMP_ID',
            'EMP_NAME',
            'F_H_NAME',
            'DOB',
            'CNIC',
            'DOMICILE',
            'BS',
            'REF_FILE_NO',
            'DATE_OF_APPOINTMENT',
            'TYPE_OF_APPOINTMENT',
            'EMP_PENSION_STATUS',
            'POSTING_DATE',
            'POSTING_TENURE',
            'TRANSFER_DATE',
            'TRANSFER_TENURE',
            'TOTAL_POSTING',
            'TOTAL_TRANSFER',
            'ACR_REMAINING',
            'ASSET_REMAINING',
            'APPOINTMENT_YEAR',
            'APPOINTMENT_MONTH',
			'JOB_STATUS'
        ])->get();;
        //echo "<pre>"; print_r($data); die;


        return view('employees.employees_list1', compact('page_title','data'));
		 
	 }
	
	 public function posting_tenure(){
		  $page_title = 'Posting Tenure ';
		   $data = DB::table('V_EMP_DB')->where('job_status', '=', 'On Job')->where('BS', '>', '13')->where('POSTING_TENURE', '>', '3')->select([
            'FP_ID',
            'EMP_ID',
            'EMP_NAME',
            'F_H_NAME',
            'DOB',
            'CNIC',
            'DOMICILE',
            'BS',
            'REF_FILE_NO',
            'DATE_OF_APPOINTMENT',
            'TYPE_OF_APPOINTMENT',
            'EMP_PENSION_STATUS',
            'POSTING_DATE',
            'POSTING_TENURE',
            'TRANSFER_DATE',
            'TRANSFER_TENURE',
            'TOTAL_POSTING',
            'TOTAL_TRANSFER',
            'ACR_REMAINING',
            'ASSET_REMAINING',
            'APPOINTMENT_YEAR',
            'APPOINTMENT_MONTH',
			'JOB_STATUS'
        ])->get();;
        //echo "<pre>"; print_r($data); die;


        return view('employees.employees_list1', compact('page_title','data'));
		  
	 }
    public function asset(){
        $page_title = 'Assets Remaining ';
        $data = DB::table('V_EMP_DB')->where('job_status', '=', 'On Job')->where('ASSET_REMAINING', '!=', '0')->select([
            'FP_ID',
            'EMP_ID',
            'EMP_NAME',
            'F_H_NAME',
            'DOB',
            'CNIC',
            'DOMICILE',
            'BS',
            'REF_FILE_NO',
            'DATE_OF_APPOINTMENT',
            'TYPE_OF_APPOINTMENT',
            'EMP_PENSION_STATUS',
            'POSTING_DATE',
            'POSTING_TENURE',
            'TRANSFER_DATE',
            'TRANSFER_TENURE',
            'TOTAL_POSTING',
            'TOTAL_TRANSFER',
            'ACR_REMAINING',
            'ASSET_REMAINING',
            'APPOINTMENT_YEAR',
            'APPOINTMENT_MONTH',
			'JOB_STATUS'
        ])->get();;
        //echo "<pre>"; print_r($data); die;


        return view('employees.employees_list1', compact('page_title','data'));
    }
	 
	 
	 
	  public function emp_info($month, $year){
        //echo 'yyy';die;
        $page_title = 'Recruitment';

        $data = DB::table('V_EMP_DB')->where('APPOINTMENT_MONTH', '=', $month)->where('APPOINTMENT_YEAR', '=',$year )->select([
            'FP_ID',
            'EMP_ID',
            'EMP_NAME',
            'F_H_NAME',
            'DOB',
            'CNIC',
            'DOMICILE',
            'BS',
            'REF_FILE_NO',
            'DATE_OF_APPOINTMENT',
            'TYPE_OF_APPOINTMENT',
            'EMP_PENSION_STATUS',
            'POSTING_DATE',
            'POSTING_TENURE',
            'TRANSFER_DATE',
            'TRANSFER_TENURE',
            'TOTAL_POSTING',
            'TOTAL_TRANSFER',
            'ACR_REMAINING',
            'ASSET_REMAINING',
            'APPOINTMENT_YEAR',
            'APPOINTMENT_MONTH',
			'JOB_STATUS'
        ])->get();;
        //echo "<pre>"; print_r($data); die;


        return view('employees.employees_list1', compact('page_title','data'));
    }
	 
	 
    public function get_yearwise_data($year) {
       $years = DB::table('V_APPOINTMENT_YEARS')->where('years', '=', $year)->get();


        $YearArr = array();
        foreach($years as $yer) {
            $object1 = new \stdClass();
            $object1->name = $yer->months;
            $n = (int)$yer->no_of_appointments;
            $object1->y = $n;
            array_push($YearArr,$object1);
        }
        $yerPieArr = $YearArr;
       // echo "<pre>"; print_r($yerPieArr); die;
        return json_encode(array('barChartArr' => $yerPieArr));
    }

    /**
     * @return string
     */
    public function get_advertisement_data() {
        // Advertisement data
        $data = array();
        $adverts = DB::table('V_ADVERTISEMENT')->get();

        foreach($adverts as $advert) {
            $data['categories'][] = $advert->strength_name;
            $data['series']['total_applicants'][] = $advert->total_applicants;
            $data['series']['total_applicants_short'][] = $advert->total_applicants_short;
            $data['series']['total_applicants_interview'][] = $advert->total_applicants_interview;
        }
        // convert array string values into int
        $data['series']['total_applicants'] = array_map('intval', $data['series']['total_applicants']);
        $data['series']['total_applicants_short'] = array_map('intval', $data['series']['total_applicants_short']);
        $data['series']['total_applicants_interview'] = array_map('intval', $data['series']['total_applicants_interview']);

        return json_encode($data);
    }
}
