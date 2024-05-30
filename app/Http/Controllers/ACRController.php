<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\ACR;
use DB;
use Validator;
use Session;
use App\Services\CarrierService;

class ACRController extends Controller
{
    public function __construct()
    {

        /*$this->middleware('auth');
        if(!Auth::user()->can('acr_assets'))
            abort(403);*/
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $page_title = 'ACR & Assets';

        return view('acr_assets.index', compact('page_title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($emp_id, CarrierService $carrServ)
    {
        $page_title = 'Add ACR';
        $reporting_officer = ['' => 'Select Officer'];
        //$order = ['' => 'Select Order'];
        $post = ['' => 'Select Post'];
        $years = ['' => 'Select Year'];
        $grades = ['' => 'Select Grade'];
        $sections = ['' => 'Select Section'];

        $posts = DB::table('TBL_SANCTION')->orderBy('strength_name', 'ASC')->get();
        foreach ($posts as $row) {
            $post[$row->sanction_id] = $row->strength_name . '- (' . $row->sanction_id . ')';
        }

        $year = DB::table('TBL_YEAR')->where('year_status', '=', 1)->orderBy('year_title', 'DESC')->get();
        foreach ($year as $row) {
            $years[$row->year_id] = $row->year_title;     //. '- (' . $row->year_id . ')';
        }

        $rep_off = DB::table('TBL_EMP')->orderBy('emp_name', 'ASC')->get();
        foreach ($rep_off as $row) {
            $reporting_officer[$row->emp_id] = $row->emp_name . '- (' . $row->emp_id . ')';
        }
        $reporting_officer['others'] = 'others';

        $grade = DB::table('TBL_GRADE')->where('grade_status', '=', 1)->orderBy('grade_name', 'ASC')->get();
        foreach ($grade as $row) {
            $grades[$row->grade_id] = $row->grade_name . ' - (' . $row->grade_id . ')';
        }
        $promotionOpt = ['' => 'Select Promotion Fitness', 'Yes' => 'Yes', 'No' => 'No', 'Premature' => 'Premature'];
        $roAssessment = ['' => 'Select RO Assessment', 'Fair' => 'Fair', 'Biased' => 'Biased', 'Exaggerated' => 'Exaggerated'];

        $empPosts = $carrServ->getEmpCarrier($emp_id);
        /*  $roPosts = $carrServ->getEmpCarrier($data->ro_id);
        $coPosts = $carrServ->getEmpCarrier($data->co_id); */

        return view('acr.create', compact(
            'page_title',
            'grades',
            'emp_id',
            'years',
            'post',
            'reporting_officer',
            'empPosts',
            'promotionOpt',
            'roAssessment'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $messages = [
            'year_id.required' => "The Year field is required.",
        ];
        $validation = Validator::make(
            $request->all(),
            [
                'year_id' => 'required',

            ],
            $messages
        );

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $last_acr = ACR::orderBy('acr_id', 'desc')->first();
        $acr = new ACR();
        $acr->acr_id = ($last_acr) ? $last_acr->acr_id + 1 : 1;
        $acr->emp_id = $request->input('emp_id');
        $acr->year_id = $request->input('year_id');

        if ($request->input('ro_id')) {
            if ($request->input('ro_id') != 'others') {
                $rep_off = DB::table('TBL_EMP')->where('emp_id', $request->input('ro_id'))->first();
                $acr->ro_id = $request->input('ro_id');
                $acr->ro_name = $rep_off->emp_name;
            } else {
                $acr->ro_name = $request->input('others_ro_name');
                $acr->ro_id = '';
            }
            $acr->ro_grading_id = $request->input('ro_grading_id');
        }
        if ($request->input('co_id')) {
            if ($request->input('co_id') != 'others') {
                $counter_off = DB::table('TBL_EMP')->where('emp_id', $request->input('co_id'))->first();
                $acr->co_id = $request->input('co_id');
                $acr->co_name = $counter_off->emp_name;
            } else {
                $acr->co_name = $request->input('others_co_name');
                $acr->co_id = '';
            }
            $acr->co_grading_id = $request->input('co_grading_id');
            $acr->ro_assmt_eval_by_co = $request->input('ro_assmt_eval_by_co');
        }

        $acr->ro_post_id = $request->input('ro_post_id');
        $acr->co_post_id = $request->input('co_post_id');
        $acr->ro_remarks = $request->input('ro_remarks');
        $acr->co_remarks = $request->input('co_remarks');
        $acr->promotion_fitness = $request->input('promotion_fitness');


        /*  if (
            !empty($request->input('date_of_receipt')) && $request->input('date_of_receipt') !== null
            && $request->input('date_of_receipt') !== 'dd-mm-yyyy'
        ) {

            $acr->date_of_receipt = date('Y-m-d', strtotime($request->input('date_of_receipt')));
        }


        if (
            !empty($request->input('initiation_date')) && $request->input('initiation_date') !== null
            && $request->input('initiation_date') !== 'dd-mm-yyyy'
        ) {

            $acr->initiation_date = date('Y-m-d', strtotime($request->input('initiation_date')));
        } */

        if (
            !empty($request->input('date_from')) && $request->input('date_from') !== null
            && $request->input('date_from') !== 'dd-mm-yyyy'
        ) {

            $acr->date_from = date('Y-m-d', strtotime($request->input('date_from')));
        }

        if (
            !empty($request->input('date_to')) && $request->input('date_to') !== null
            && $request->input('date_to') !== 'dd-mm-yyyy'
        ) {

            $acr->date_to = date('Y-m-d', strtotime($request->input('date_to')));
        }

        /* if (
            !empty($request->input('ro_date')) && $request->input('ro_date') !== null
            && $request->input('ro_date') !== 'dd-mm-yyyy'
        ) {

            $acr->ro_date = date('Y-m-d', strtotime($request->input('ro_date')));
        }

        if (
            !empty($request->input('co_date')) && $request->input('co_date') !== null
            && $request->input('co_date') !== 'dd-mm-yyyy'
        ) {

            $acr->co_date = date('Y-m-d', strtotime($request->input('co_date')));
        } */

        /// EDOC
        if ($request->hasFile('acr_edoc')) {
            $file = $request->file('acr_edoc');
            /// new file name 
            $new_filename = 'acr_' . $acr->acr_id;

            $path = 'public\NHA-IS\ACRs';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);

            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $acr->acr_edoc = $completeUrl;
        }

        $acr->save();

        Session::flash('success', 'Record added successfully.');


        return Redirect('employee/emp_detail/' . $request->input('emp_id'));
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
    public function edit($id, CarrierService $carrServ)
    {
        $page_title = 'Edit ACR';

        $data = ACR::find($id);
        //dd($data);

        $reporting_officer = ['' => 'Select Officer'];

        $post = ['' => 'Select Post'];
        $years = ['' => 'Select Year'];
        $grades = ['' => 'Select Grade'];
        $sections = ['' => 'Select Section'];

        $posts = DB::table('TBL_SANCTION')->orderBy('strength_name', 'ASC')->get();
        foreach ($posts as $row) {
            $post[$row->sanction_id] = $row->strength_name . '- (' . $row->sanction_id . ')';
        }


        $year = DB::table('TBL_YEAR')->where('year_status', '=', 1)->orderBy('year_title', 'DESC')->get();
        foreach ($year as $row) {
            $years[$row->year_id] = $row->year_title;     //. '- (' . $row->year_id . ')';
        }

        $rep_off = DB::table('TBL_EMP')->orderBy('emp_name', 'ASC')->get();
        foreach ($rep_off as $row) {
            $reporting_officer[$row->emp_id] = $row->emp_name . '- (' . $row->emp_id . ')';
        }
        $reporting_officer['others'] = 'others';

        $grade = DB::table('TBL_GRADE')->where('grade_status', '=', 1)->orderBy('grade_name', 'ASC')->get();
        foreach ($grade as $row) {
            $grades[$row->grade_id] = $row->grade_name . ' - (' . $row->grade_id . ')';
        }

        $promotionOpt = ['' => 'Select Promotion Fitness', 'Yes' => 'Yes', 'No' => 'No', 'Premature' => 'Premature'];

        $roAssessment = ['' => 'Select RO Assessment', 'Fair' => 'Fair', 'Biased' => 'Biased', 'Exaggerated' => 'Exaggerated'];

        $empPosts = $carrServ->getEmpCarrier($data->emp_id);
        $roPosts = $carrServ->getEmpCarrier($data->ro_id);
        $coPosts = $carrServ->getEmpCarrier($data->co_id);

        return view('acr.edit', compact(
            'page_title',
            'data',
            'promotionOpt',
            'roAssessment',
            'grades',
            'emp_id',
            'years',
            'post',
            'empPosts',
            'roPosts',
            'coPosts',
            'reporting_officer'
        ));
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
        $messages = [
            'year_id.required' => "The Year field is required.",
        ];
        $validation = Validator::make(
            $request->all(),
            [
                'year_id' => 'required',

            ],
            $messages
        );

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $acr = ACR::find($id);

        $acr->year_id = $request->input('year_id');
        $acr->carrer_id = $request->input('carrer_id');
        if ($request->input('ro_id')) {
            if ($request->input('ro_id') != 'others') {
                $rep_off = DB::table('TBL_EMP')->where('emp_id', $request->input('ro_id'))->first();
                $acr->ro_id = $request->input('ro_id');
                $acr->ro_name = $rep_off->emp_name;
            } else {
                $acr->ro_name = $request->input('others_ro_name');
                $acr->ro_id = '';
            }
            $acr->ro_grading_id = $request->input('ro_grading_id');
        }
        if ($request->input('co_id')) {
            if ($request->input('co_id') != 'others') {
                $counter_off = DB::table('TBL_EMP')->where('emp_id', $request->input('co_id'))->first();
                $acr->co_id = $request->input('co_id');
                $acr->co_name = $counter_off->emp_name;
            } else {
                $acr->co_name = $request->input('others_co_name');
                $acr->co_id = '';
            }
            $acr->co_grading_id = $request->input('co_grading_id');
            $acr->ro_assmt_eval_by_co = $request->input('ro_assmt_eval_by_co');
        }
        $acr->ro_post_id = $request->input('ro_post_id');
        $acr->co_post_id = $request->input('co_post_id');
        $acr->ro_remarks = $request->input('ro_remarks');
        $acr->co_remarks = $request->input('co_remarks');
        $acr->promotion_fitness = $request->input('promotion_fitness');
        $acr->acr_status = ($request->input('acr_status')) ? $request->input('acr_status') : '0';

        /* if (
            !empty($request->input('date_of_receipt')) && $request->input('date_of_receipt') !== null
            && $request->input('date_of_receipt') !== 'dd-mm-yyyy'
        ) {

            $acr->date_of_receipt = date('Y-m-d', strtotime($request->input('date_of_receipt')));
        }


        if (
            !empty($request->input('initiation_date')) && $request->input('initiation_date') !== null
            && $request->input('initiation_date') !== 'dd-mm-yyyy'
        ) {

            $acr->initiation_date = date('Y-m-d', strtotime($request->input('initiation_date')));
        } */

        if (
            !empty($request->input('date_from')) && $request->input('date_from') !== null
            && $request->input('date_from') !== 'dd-mm-yyyy'
        ) {

            $acr->date_from = date('Y-m-d', strtotime($request->input('date_from')));
        }

        if (
            !empty($request->input('date_to')) && $request->input('date_to') !== null
            && $request->input('date_to') !== 'dd-mm-yyyy'
        ) {

            $acr->date_to = date('Y-m-d', strtotime($request->input('date_to')));
        }

        /*  if (
            !empty($request->input('ro_date')) && $request->input('ro_date') !== null
            && $request->input('ro_date') !== 'dd-mm-yyyy'
        ) {

            $acr->ro_date = date('Y-m-d', strtotime($request->input('ro_date')));
        }

        if (
            !empty($request->input('co_date')) && $request->input('co_date') !== null
            && $request->input('co_date') !== 'dd-mm-yyyy'
        ) {

            $acr->co_date = date('Y-m-d', strtotime($request->input('co_date')));
        } */

        /// EDOC
        if ($request->hasFile('acr_edoc')) {
            $file = $request->file('acr_edoc');
            /// new file name 
            $new_filename = 'acr_' . $acr->acr_id;

            $path = 'public\NHA-IS\ACRs';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);

            $completeUrl = $path . '/' . $new_filename . '.' . $extension;

            $acr->acr_edoc = $completeUrl;
        } else {
            if ($acr->acr_edoc)
                $acr->acr_edoc = $acr->acr_edoc;
            else
                $acr->acr_edoc = '';
        }


        $acr->save();

        Session::flash('success', 'Record updated successfully.');


        return Redirect('employee/emp_detail/' . $acr->emp_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $data = ACR::find($id);
        DB::table('TBL_ACR')->where('ACR_ID', '=', $id)->delete();
        Session::flash('success', 'Record has been deleted successfully.');

        return Redirect('employee/emp_detail/' . $data->emp_id);
    }
}
