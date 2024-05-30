<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use App\Http\Controllers\Controller;
use App\Models\Trainee_eva;
use App\Models\Trainee_leacture;
use Auth;
use PhpParser\Node\Expr\Cast\Object_;
use Symfony\Component\VarDumper\Caster\ExceptionCaster;

class HrdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index()
    {
        $this->middleware('auth');
        if (!Auth::user()->can('Trainings_HRD'))
            abort(403);

        $page_title = 'HRD';
        $training_done = DB::table("V_TRAINING_DONE")->select("start_date")->distinct("start_date")->get();
        $courses = DB::table("V_TRAINING_DONE")->select("course_id,course,gender,cadre_name,start_date, end_date")
            ->where('training_id', '=', 16)
            ->distinct()->orderBy('course', 'asc')->get();
        $training_years = DB::table("V_TRAINING_DONE")->select("start_date")->distinct("start_date")->get();
        /* echo  "<pre>";
        print_r($courses);
        die; */
        $array_training = array();
        foreach ($courses as $key => $course) {
            $test =  $course->course_id;
            $training_dones = DB::table("V_TRAINING_DONE")->where("course_id", '=', $test)->count();
            $course_name =  $course->course;
            $cadre_name =  $course->cadre_name;
            $start_date =  $course->start_date;
            $end_date =  $course->end_date;

            $male = DB::select("SELECT GENDER($test,'Male') total_male FROM dual");
            //print_r($male);die;
            $female = DB::select("SELECT GENDER($test,'Female') total_female FROM dual");
            //$total_gender =  $female + $female;
            $course_obj = new \stdClass();
            $course_obj->males = $male[0]->total_male;
            $course_obj->females = $female[0]->total_female;
            $total_gender =  $course_obj->males +  $course_obj->females;
            $course_obj->course_title = $course_name;
            $course_obj->training_count = $training_dones;
            // $course_obj->training_years = $training_years[0]->start_date;
            $course_obj->cadre_name = $cadre_name;
            $course_obj->start_date = $start_date;
            $course_obj->end_date = $end_date;
            $course_obj->total = $total_gender;
            $course_obj->id = $test;
            $array_training[$key] = $course_obj;
            /* echo  "<pre>";
            print_r($array_training);
            die; */
        }


        $training_head = DB::table('TBL_TRAINING_HEAD')->orderBy('TH_ID', 'DESC  ')->get();
        if (Auth::user()->hasrole('hrtc_role'))
            $training = DB::table('MV_TRAININGS')->orderBy('training_id', 'DESC')->where('organization_id', '=', 285)->get();
        else
            $training = DB::table('MV_TRAININGS')->orderBy('training_id', 'DESC')->get();
        //echo "<pre>"; print_r($training);die;
        if (Auth::user()->hasrole('hrtc_role'))
            $training_course = DB::table('V_TRAINING_COURSE')->orderBy('TC_ID', 'DESC')->where('training_id', '=', 16)->get();
        else
            $training_course = DB::table('V_TRAINING_COURSE')->orderBy('TC_ID', 'DESC')->get();
        $training_coursee = DB::table('V_TRAINING_COURSE')->orderBy('TC_ID', 'DESC')->first();
        //echo "<pre>"; print_r($training_course); die;
        $emp_nomination = DB::table('TBL_TRAINING_NOMINATION')
            ->orderBy('TN_ID', 'DESC')
            ->join('TBL_EMP', 'TBL_TRAINING_NOMINATION.EMP_ID', '=', 'TBL_EMP.EMP_ID')->get();
        return view('hrd.hr_traning1', compact('page_title', 'training_done', 'training_years', 'array_training', 'training_head', 'training', 'training_course', 'emp_nomination', 'training_coursee'));
    }

    public function hrtc_form($tn_id, $tc_id,  $emp_id, $tran_id)
    {
        $this->middleware('auth');
        if (!Auth::user()->can('Trainings_HRD'))
            abort(403);

        return view('hrd.hrtc_form', compact('tn_id', 'tc_id', 'emp_id', 'tran_id'));
    }
    public function hrtc_form_b($tn_id, $tc_id,  $emp_id, $tran_id)
    {
        $this->middleware('auth');
        if (!Auth::user()->can('Trainings_HRD'))
            abort(403);

        // echo $tran_id;die;
        return view('hrd.hrtc_form_5b', compact('tn_id', 'tc_id', 'emp_id', 'tran_id'));
    }

    public function hrtc_b_store(Request $request)
    {
        $ord = Trainee_leacture::orderBy('tl_id', 'desc')->first();
        $order = new Trainee_leacture();
        $order->tl_id = ($ord) ? $ord->tl_id + 1 : 1;
        if ($request->input('group1') == 1)
            $lect_exp = 1;
        elseif ($request->input('group1') == 2)
            $lect_exp = 2;
        elseif ($request->input('group1') == 3)
            $lect_exp = 3;
        else
            $lect_exp = 4;

        if ($request->input('group2') == 1)
            $met_pres = 1;
        elseif ($request->input('group2') == 2)
            $met_pres = 2;
        elseif ($request->input('group2') == 3)
            $met_pres = 3;
        else
            $met_pres = 4;

        if ($request->input('group3') == 1)
            $intrest_sub = 1;
        elseif ($request->input('group3') == 2)
            $intrest_sub = 2;
        elseif ($request->input('group3') == 3)
            $intrest_sub = 3;
        else
            $intrest_sub = 4;

        if ($request->input('group4') == 1)
            $answer = 1;
        elseif ($request->input('group4') == 2)
            $answer = 2;
        elseif ($request->input('group4') == 3)
            $answer = 3;
        else
            $answer = 4;

        if ($request->input('group5') == 1)
            $trainee_met = 1;
        elseif ($request->input('group5') == 2)
            $trainee_met = 2;
        elseif ($request->input('group5') == 3)
            $trainee_met = 3;
        else
            $trainee_met = 4;

        if ($request->input('group6') == 1)
            $over_all = 1;
        elseif ($request->input('group6') == 2)
            $over_all = 2;
        elseif ($request->input('group6') == 3)
            $over_all = 3;
        elseif ($request->input('group6') == 4)
            $over_all = 4;

        else
            $over_all = 5;
        $order->lecture_exp_support = $lect_exp;
        $order->material_presented = $met_pres;
        $order->intrest_subjest = $intrest_sub;
        $order->answer_questioned = $answer;
        $order->trainer_met_training = $trainee_met;
        $order->overall_effectiveness = $over_all;
        $order->emp_id = $request->input('emp_id');
        $order->training_id = $request->input('trn_id');
        $order->course_id = $request->input('tc_id');
        /// echo "<pre>"; print_r($order); die;
        $order->save();


        return redirect('training_course/' . $request->input('tc_id'));
    }



    public function hrtc_store(Request $request)
    {
        $ord = Trainee_eva::orderBy('tt_eva_id', 'desc')->first();
        $order = new Trainee_eva();
        $order->tt_eva_id = ($ord) ? $ord->tt_eva_id + 1 : 1;
        if ($request->input('group1') == 1)
            $met = 1;
        elseif ($request->input('group1') == 2)
            $met = 2;
        elseif ($request->input('group1') == 3)
            $met = 3;
        else
            $met = 4;

        if ($request->input('group2') == 1)
            $know_learn = 1;
        elseif ($request->input('group2') == 2)
            $know_learn = 2;
        elseif ($request->input('group2') == 3)
            $know_learn = 3;
        else
            $know_learn = 4;

        if ($request->input('group3') == 1)
            $train_objective = 1;
        elseif ($request->input('group3') == 2)
            $train_objective = 2;
        elseif ($request->input('group3') == 3)
            $train_objective = 3;
        else
            $train_objective = 4;

        if ($request->input('group4') == 1)
            $conteant_org = 1;
        elseif ($request->input('group4') == 2)
            $conteant_org = 2;
        elseif ($request->input('group4') == 3)
            $conteant_org = 3;
        else
            $conteant_org = 4;

        if ($request->input('group5') == 1)
            $metarial_dist = 1;
        elseif ($request->input('group5') == 2)
            $metarial_dist = 2;
        elseif ($request->input('group5') == 3)
            $metarial_dist = 3;
        else
            $metarial_dist = 4;

        if ($request->input('group6') == 1)
            $training_know = 1;
        elseif ($request->input('group6') == 2)
            $training_know = 2;
        elseif ($request->input('group6') == 3)
            $training_know = 3;
        else
            $training_know = 4;

        if ($request->input('group7') == 1)
            $quality_inst = 1;
        elseif ($request->input('group7') == 2)
            $quality_inst = 2;
        elseif ($request->input('group7') == 3)
            $quality_inst = 3;
        else
            $quality_inst = 4;

        if ($request->input('group8') == 1)
            $met_objective = 1;
        elseif ($request->input('group8') == 2)
            $met_objective = 2;
        elseif ($request->input('group8') == 3)
            $met_objective = 3;
        else
            $met_objective = 4;

        if ($request->input('group9') == 1)
            $part_interaction = 1;
        elseif ($request->input('group9') == 2)
            $part_interaction = 2;
        elseif ($request->input('group9') == 3)
            $part_interaction = 3;
        else
            $part_interaction = 4;

        if ($request->input('group10') == 1)
            $adequate = 1;
        elseif ($request->input('group10') == 2)
            $adequate = 2;
        elseif ($request->input('group10') == 3)
            $adequate = 3;
        else
            $adequate = 4;

        if ($request->input('group11') == 1)
            $training_over = 1;
        elseif ($request->input('group11') == 2)
            $training_over = 2;
        elseif ($request->input('group11') == 3)
            $training_over = 3;
        else
            $training_over = 4;

        if ($request->input('group14') == 1)
            $fac_rate = "Excellent";
        elseif ($request->input('group14') == 2)
            $fac_rate = "Good";
        elseif ($request->input('group14') == 3)
            $fac_rate = "Average";
        elseif ($request->input('group14') == 4)
            $fac_rate = "poor";
        else
            $fac_rate = "Very Poor";

        $order->training_met_expectation = $met;
        $order->knowledge_learned = $know_learn;
        $order->training_objective = $train_objective;
        $order->content_organized = $conteant_org;
        $order->material_distributed = $metarial_dist;
        $order->training_knowledge = $training_know;
        $order->quality_instruction = $quality_inst;
        $order->met_objective = $met_objective;
        $order->participation_intercation = $part_interaction;
        $order->adequate_fine = $adequate;
        $order->training_overall = $training_over;
        $order->triining_coold_improved = $request->input('improved');
        $order->comments_training = $request->input('comments');
        $order->facility_rate = $fac_rate;
        $order->comments_ifany = $request->input('ifany');
        $order->emp_id = $request->input('emp_id');
        $order->training_id = $request->input('tn_id');
        $order->course_id = $request->input('tc_id');
        //echo $test = $request->input('tc_id'); die;
        // echo "<pre>"; print_r($order); die;
        $order->save();
        return redirect('training_course/' . $request->input('tc_id'));
    }
    public function participants($id, $year)
    {
        $this->middleware('auth');
        if (!Auth::user()->can('Trainings_HRD'))
            abort(403);

        $data = DB::table("V_TRAINING_DONE")->where('training_id', '=', 16)->where('course_id', '=',   $id)
            ->where('start_date', '=', $year)->get();
        return view('hrd.participants', compact('data'));
        //echo $training_years; die;
    }

    public function t_course($training_id)
    {
        //echo $training_id; die;
        $this->middleware('auth');
        if (!Auth::user()->can(['hrtc_training_courses', 'hrd_trainings']))
            abort(403);

        $page_title = 'Training Courses';
        $test = $training_id;
        //if((Auth::user()->hasRole('superadmin'))||(Auth::user()->hasRole('HRD_TRAINING'))) {

        $training_course = DB::table('V_TRAINING_COURSE')->where('training_id', '=', $training_id)->get();      //hrtc_role              
        /* else{
            $training_course = DB::table('V_TRAINING_COURSE')->where('training_id', '=', $training_id)
                ->where('end_date', '>=', date('Y-m-d 00:00:00'))
				->get();
				 
        } */

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
    }
}
