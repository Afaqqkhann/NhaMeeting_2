<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Datatables;
use URL;
use Auth;
class TrainingsController extends Controller
{
	
	public function __construct() {
		
		$this->middleware('auth');
        if(!Auth::user()->can('training_dss_dashboard'))
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
        return view('trainings.index');
    }

    /**
     * @return mixed
     */
    public function training_list_data() {

        $trainings = DB::table('V_TRAININGS')->select(['TN_ID', 'COURSE_NAME', 'EXPENDITURE', 'START_DATE', 'END_DATE', 'TOTAL_DAYS', 'YEARS', 'REFERENCE_NO']);

        return Datatables::of($trainings)
            ->addColumn('action', function ($trainings) {
                //return '<a href="'.URL::to('/trainings/training_chart').'/'.$trainings->tn_id.'" class="btn btn-xs btn-primary"><i class="fa fa-file-text-o"></i>  Training Chart</a>';
                return '<a href="'.URL::to('/trainings/charts').'" class="btn btn-xs btn-primary"><i class="fa fa-file-text-o"></i>  Training Chart</a>';
            })
            ->editColumn('start_date', '{{date("d-M-Y", strtotime($start_date))}}')
            ->editColumn('end_date', '{{date("d-M-Y", strtotime($end_date))}}')
            ->make();
    }

    public function charts() {
        $page_title = 'Trainings';

        $years = DB::table('V_TRAINING_COURSE')->select('years')->distinct('years')->orderby('years', 'desc')->get();
        $place_years = DB::table('V_TRAINING_PLACE')->select('years')->distinct('years')->orderby('years', 'desc')->get();

        // values from functions
        //$training_tt_local = DB::select("SELECT TRAINING_TT_LOCAL('2015', 'DEC') training_tt_local FROM dual");
        $training_tt_local = DB::select("SELECT TRAININGS_LOCAL() training_tt_local FROM dual");
        //$training_tt_foreign = DB::select("SELECT TRAINING_TT_FOREIGN('2015', 'DEC') training_tt_foreign FROM dual");
        $training_tt_foreign = DB::select("SELECT TRAININGS_FOREIGN() training_tt_foreign FROM dual");
        $training_local_current = DB::select('SELECT TRAINING_LOCAL_CURRENT() training_local_current FROM dual');
        $training_foreign_current = DB::select('SELECT TRAINING_FOREIGN_CURRENT() training_foreign_current FROM dual');
        /**** Capacity building ****/
        $training_capacity_build = DB::select('SELECT TRAININGS_CB() training_cb FROM dual');
        $training_cb_current = DB::select('SELECT TRAININGS_CB_CURRENT() training_cb_current FROM dual');

        $training_ob = DB::select('SELECT TRAININGS_OB() training_ob FROM dual');
        $training_ob_current = DB::select('SELECT TRAININGS_OB_CURRENT() training_ob_current FROM dual');

        return view('trainings.training_charts', compact('page_title', 'years', 'place_years',
            'training_capacity_build', 'training_tt_local', 'training_tt_foreign', 'training_local_current',
        'training_cb_current','training_ob','training_ob_current',
            'training_foreign_current'));
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
    public function get_yearwise_data($year) {
        $years = DB::table('V_TRAINING_COURSE')
            ->select(DB::raw('COUNT(no_of_persons) as tot_training'),DB::raw('months as month'))
            ->where('years','=',$year)
            ->groupBy(DB::raw('months') )
            ->get();
        /*$years =  DB::table('V_TRAINING_COURSE')->Where('years', '=', $year)
            ->select(DB::raw("SUM(months) as t_months"), DB::raw("SUM(no_of_persons) as t_person"))
            ->groupBy('months', 'no_of_persons')
            ->get();*/

        //$years = DB::select('SELECT YEARS, MONTHS, SUM (NO_OF_PERSONS) as SUM FROM V_TRAINING_COURSE)GROUP BY YEARS, MONTHS ORDER BY 1, 2')->get();
       //echo "<pre>"; print_r($years); die;
//        $data = $result = array();
//        $course_name = array('name' => 'Participants');
//        foreach($years as $row) {
//            $course_name['data'][] = $row->month;
//            $data['data'][] = $row->tot_training;
//        }
//        array_push($result, $course_name['data']);
//        array_push($result, array('data' => $data['data']));
//        return json_encode($result, JSON_NUMERIC_CHECK);


        $YearArr = array();
        foreach($years as $yer) {
            $object1 = new \stdClass();
            $object1->name = $yer->month;
            $n = (int)$yer->tot_training;
            $object1->y = $n;
            array_push($YearArr,$object1);
        }
        $yerPieArr = $YearArr;
        return json_encode(array('barChartArr' => $yerPieArr));

    }
	
	public function month_data($month , $year){
       // echo "test"; die;
       $page_title = "Month Wise Trainings Data";
       $data = DB::table('V_TRAINING_COURSE')->where('years', '=', $year)->where('months', '=', $month)->get();
       //echo "<pre>"; print_r($data);die;
       return view('trainings.month_wise', compact('page_title','data'));

   }

    public function get_training_place_data($year) {
        $data = DB::table('V_TRAINING_PLACE')->where('years', '=', $year)->get();

        foreach($data as $row) {
            $data['local'][] = intval($row->local_trainings);
            $data['foreign'][] = intval($row->foreign_trainings);
            $data['categories'][] = $row->months;
        }

        return json_encode($data);
    }
}
