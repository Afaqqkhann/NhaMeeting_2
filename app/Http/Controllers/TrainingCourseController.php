<?php

namespace App\Http\Controllers;

use App\Models\Cadre;
use App\Models\Desig;
use App\Models\Education;
use App\Models\Employees\Employees;
use App\Models\Experience;
use App\Models\Extension;
use App\Models\Family;
use App\Models\Noc;
use App\Models\Order;
use App\Models\Post;
use App\Models\Relation;
use App\Models\Section;
use App\Models\TrainingHead;
use App\Models\TrainingCourse;
use App\Models\Tranings;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Validator;
use Input;
use DateTime;


class TrainingCourseController extends Controller
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
        $page_title = 'Courses';

        $sponcer_id = TrainingHead::where('th_type', '=', 3)->orderBy('th_title', 'Asc')->get();
        $course_id = TrainingHead::where('th_type', '=', 2)->orderBy('th_title', 'Asc')->get();
        $place_id = TrainingHead::where('th_type', '=', 7)->orderBy('th_title', 'Asc')->get();
        $cadre_id = ['' => 'Select Cadre'] + Cadre::lists('cadre_name', 'cadre_id')->all();

        /* $trainings = (is_null($id)) ? 
                ['' => 'Select Training'] + Tranings::with('organization','place')
                    ->get()->lists('trainingsList', 'training_id')->all():
                ''; */

        //dd($trainings);

        return view('t_courses.create', compact('page_title', 'sponcer_id', 'course_id', 'place_id', 'cadre_id', 'id'));
    }





    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = array(
            'required' => 'The :attribute field is required.',
            'required' => 'The :attribute field is required.',
            'required' => 'The :attribute field is required.',
        );
        $validator = Validator::make($request->all(), [
            'sponser' => 'required',
            'course' => 'required',
            'place' => 'required',
        ], $messages);
        if ($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());

        $record = TrainingCourse::orderBy('tc_id', 'desc')->first();
        $training_course = new TrainingCourse();
        $training_course->tc_id = ($record) ? $record->tc_id + 1 : 1;
        $training_course->start_date = ($request->input('s_date')) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('s_date')))) : '';
        $training_course->end_date = ($request->input('e_date')) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('e_date')))) : '';
        ///       
        $no_of_days = \Naeem\Helpers\Helper::getNoOfDays($request->input('s_date'), $request->input('e_date'));
        $training_course->total_days = $no_of_days;
        //$training_course->age = $request->input('age');
        //$training_course->bs = $request->input('bs');
        $training_course->reference_no = $request->input('r_no');
        $training_course->cadre_id = $request->input('cadre');
        $training_course->training_id =  $request->input('t_id');
        $training_course->sponser_id = $request->input('sponser');
        $training_course->course_id = $request->input('course');
        $training_course->place_id = $request->input('place');
        $training_course->tc_status = 1;
        //echo "<pre>"; print_r($training_course); die;
        $training_course->save();
        Session::flash('success', 'Course added successfully.');
        return Redirect('t_course/' . $request->input('t_id'));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Noc';
        $data = Noc::find($id);
        //echo "<pre>"; print_r($data); die;
        return view('noc.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // echo "test"; die;
        $data = TrainingCourse::find($id);
        // echo $data->course_id; die;
        $page_title = 'Courses';
        $sponcer_id = TrainingHead::where('th_type', '=', 3)->orderBy('th_title', 'Asc')->get();
        $course_id = TrainingHead::where('th_type', '=', 2)->orderBy('th_title', 'Asc')->get();
        $place_id = TrainingHead::where('th_type', '=', 7)->orderBy('th_title', 'Asc')->get();
        //dd($place_id->pluck('th_title'));
        $cadre_id = ['' => 'Select Cadre'] + Cadre::lists('cadre_name', 'cadre_id')->all();
        return view('t_courses.edit', compact('page_title', 'sponcer_id', 'course_id', 'place_id', 'cadre_id', 'id', 'data'));
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
        $messages = [];
        $validation = Validator::make(
            $request->all(),
            [],
            $messages
        );

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $st_date = ($request->input('start_date')) ? date('Y-m-d', strtotime($request->input('start_date'))) : '';
        $en_date = ($request->input('end_date')) ? date('Y-m-d', strtotime($request->input('end_date'))) : '';
        ///       
        $no_of_days = \Naeem\Helpers\Helper::getNoOfDays($request->input('start_date'), $request->input('end_date'));

        $training_course = TrainingCourse::findOrFail($id);
        $training_course->start_date = $st_date;
        $training_course->end_date = $en_date;
        $training_course->total_days = $no_of_days;
        $training_course->reference_no = $request->input('r_no');
        $training_course->cadre_id = $request->input('cadre');
        $training_course->course_id = $request->input('courses');
        $training_course->sponser_id = $request->input('sponser');
        $training_course->place_id = $request->input('places');

        $training_course->save();
        Session::flash('success', 'Training Course updated successfully.');

        return Redirect('t_course/' . $training_course->training_id);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $training_course = TrainingCourse::find($id);
        $training_id = $training_course->training_id;

        $training_course->delete();

        Session::flash('success', 'Training Course has been deleted successfully.');
        return Redirect('t_course/' . $training_id);
    }
}
