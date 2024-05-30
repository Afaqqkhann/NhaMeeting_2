<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Auth;
class PersonnelController extends Controller
{
	public function __construct() {
		
		$this->middleware('auth');
        if(!Auth::user()->can('personnel_dss_dashboard'))
            abort(403);
	}
	
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $data = array(
            'page_title'	=>	'Personnel Dashboard',

            'total_postings'            =>  DB::select('SELECT TOTAL_POSTINGS() total_postings FROM dual'),
            'total_postings_3' 	        => 	DB::select('SELECT TOTAL_POSTINGS_3() total_postings_3 FROM dual'),
            'total_postings_5' 	        => 	DB::select('SELECT TOTAL_POSTINGS_5() total_postings_5 FROM dual'),
            'posting_year_actualized'   => 	DB::select('SELECT POSTING_YEAR_ACTUALIZED() posting_year_actualized FROM dual'),
            'posting_year_not_act'      => 	DB::select('SELECT POSTING_YEAR_NOT_ACT() posting_year_not_act FROM dual'),
            'posting_year_cancelled'    => 	DB::select('SELECT POSTING_YEAR_CANCELLED() posting_year_cancelled FROM dual'),
            'posting_3'                 => 	DB::select('SELECT POSTING_3() posting_3 FROM dual'),
            'posting_5'                 => 	DB::select('SELECT POSTING_5() posting_5 FROM dual'),
            'degree_verification'	    => 	DB::select('SELECT DEGREE_VERIFICATION() degree_verification FROM dual'),
            'degree_awaited'            => 	DB::select('SELECT DEGREE_AWAITED() degree_awaited FROM dual'),
            'reward_officer'            =>  DB::select('SELECT REWARD_OFFICER() reward_officer FROM dual'),
            'reward_staff'              =>  DB::select('SELECT REWARD_STAFF() reward_staff FROM dual'),

        );
        //dd($data);
        return view('personnel.personnel')->with($data);
    }
}
