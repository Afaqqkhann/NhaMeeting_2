<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use App\Http\Controllers\Controller;
use Auth;
class HRDController extends Controller
{
	public function __construct() {

		$this->middleware('auth');
        if(!Auth::user()->can('HRTC_TRAINING'))
            abort(403);
		
		/*if(!Auth::user()->can('hrd_training'))
            abort(403);*/
	}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index()
    {
		//echo"test"; die;
		$page_title = 'Hrmis HRD';
        $training_head = DB::table('TBL_TRAINING_HEAD')->orderBy('TH_ID', 'DESC  ')->get();
            if(Auth::user()->hasrole('hrtc_role'))
        $training = DB::table('MV_TRAININGS')->orderBy('training_id', 'DESC')->where('organization_id','=',285)->get();
        else
        $training = DB::table('MV_TRAININGS')->orderBy('training_id', 'DESC')->get();
        //echo "<pre>"; print_r($training);die;
        $training_course = DB::table('V_TRAINING_COURSE')->orderBy('TC_ID', 'DESC')->get();
        //echo "<pre>"; print_r($training_course); die;
        $emp_nomination = DB::table('TBL_TRAINING_NOMINATION')->orderBy('TN_ID', 'DESC')
                         ->join('TBL_EMP','TBL_TRAINING_NOMINATION.EMP_ID', '=', 'TBL_EMP.EMP_ID')->get();
        return view('hrd.hr_traning1', compact('page_title', 'training_head','training','training_course','emp_nomination'));
    }
    public function t_course($training_id){

       // echo $training_id; die;
        $page_title = 'Training Course';
        $test = $training_id;

        $training_course = DB::table('V_TRAINING_COURSE')->where('training_id', '=',$training_id)->get();
        //echo "<pre>"; print_r($training_course); die;
        return view('hrd.t_course', compact('page_title', 'training_course', 'test'));
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
}
