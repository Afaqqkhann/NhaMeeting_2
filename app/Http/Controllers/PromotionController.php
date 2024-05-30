<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Employees\Employees;
use App\Services\EmployeeService;
use DB;
use Illuminate\Http\Request;
use Input;
use Session;
use Validator;

class PromotionController extends Controller
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
    public function create($emp_id)
    {
        $page_title = 'Add Promotion';

        $post = ['' => 'Select Post'];
        $place = ['' => 'Select Place'];
        $charge = ['' => 'Select Charge'];
        $sections = ['' => 'Select Section'];

        $emp_charge = DB::table('TBL_CHARGE')->where('charge_id', '>=', 100)->where('charge_id', '<=', 199)->orderBy('charge_title', 'ASC')->get();

        if (!empty($emp_charge)) {
            foreach ($emp_charge as $row) {
                $charge[$row->charge_id] = $row->charge_title . '- (' . $row->charge_id . ')';
            }
        }

        $emp_post = DB::table('TBL_SANCTION')->orderBy('strength_name', 'ASC')->get();
        foreach ($emp_post as $row) {
            $post[$row->sanction_id] = $row->strength_name . '- (' . $row->sanction_id . ')';
        }

        $emp_bs = [
            '' => 'Select BS', 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6,
            7 => 7, 8 => 8, 9 => 9, 10 => 10, 11 => 11, 12 => 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16,
            17 => 17, 18 => 18, 19 => 19, 20 => 20, 21 => 21, 22 => 22
        ];

        /*$emp_order = DB::table('TBL_ORDER')->orderBy('order_subject', 'ASC')->get();
        foreach ($emp_order as $row) {
        $order[$row->order_id] = $row->order_subject;
        }*/
        $emp_place = DB::table('TBL_PLACE')->orderBy('place_title', 'ASC')->get();
        foreach ($emp_place as $row) {
            $place[$row->place_id] = $row->place_title . '- (' . $row->place_id . ')';
        }

        $emp_sections = DB::table('TBL_SECTION')->orderBy('section_name', 'ASC')->get();
        foreach ($emp_sections as $row) {
            $sections[$row->section_id] = $row->section_name . ' - (' . $row->section_id . ')';
        }

        return view('promotion.create', compact('page_title', 'emp_bs', 'sections', 'emp_id', 'post', 'charge', 'place'));
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
            'post_id.required' => "The Post Name field is required.",
        ];
        $validation = Validator::make(
            $request->all(),
            [
                'post_id' => 'required',

            ],
            $messages
        );

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        DB::beginTransaction();
        $record = Carrier::orderBy('carrier_id', 'desc')->first();
        $promotion = new Carrier();
        $promotion->carrier_id = ($record) ? $record->carrier_id + 1 : 1;
        $promotion->emp_id = $request->input('emp_id');
        $promotion->order_id = $request->input('order_id');
        // Post Name
        $promotion->sanction_id = $request->input('post_id');
        $sanction = DB::table('TBL_SANCTION')->select('strength_name')
            ->where('sanction_id', $request->input('post_id'))->first();
        $promotion->post_name = $sanction->strength_name;
        //$promotion->post_name = $request->input('post_name');
        $promotion->charge_id = $request->input('charge_id');
        $promotion->section_id = $request->input('section_id');
        $promotion->bs = $request->input('bs');
        $promotion->place_id = $request->input('place_id');
        $promotion->remarks = $request->input('remarks');

        if (!empty($request->input('joining_date')) && $request->input('joining_date') !== null) {
            $promotion->joining_date = date('Y-m-d', strtotime($request->input('joining_date')));
        }

        $promotion->save();

        $empServ = new EmployeeService();
        $empServ->updateEmployeeInfo($promotion->emp_id);

        DB::commit();


        //Session::flash('success', 'Promotion added successfully.');
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
        $page_title = 'Promotion';
        $data = Carrier::find($id);
        // echo "<pre>"; print_r($data); die;
        return view('promotion.show', compact('page_title', 'data'));
    }
    public function edit($id)
    {
        $page_title = 'Promotion Edit';
        $data = Carrier::find($id);

        $post = ['' => 'Select Post'];
        $place = ['' => 'Select Place'];
        $charge = ['' => 'Select Charge'];
        $sections = ['' => 'Select Section'];

        $emp_charge = DB::table('TBL_CHARGE')->where('charge_id', '>=', 100)->where('charge_id', '<=', 199)->orderBy('charge_title', 'ASC')->get();

        if (!empty($emp_charge)) {
            foreach ($emp_charge as $row) {
                $charge[$row->charge_id] = $row->charge_title . '- (' . $row->charge_id . ')';
            }
        }

        $emp_post = DB::table('TBL_SANCTION')->orderBy('strength_name', 'ASC')->get();
        foreach ($emp_post as $row) {
            $post[$row->sanction_id] = $row->strength_name . '- (' . $row->sanction_id . ')';
        }

        $emp_bs = [
            '' => 'Select BS', 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6,
            7 => 7, 8 => 8, 9 => 9, 10 => 10, 11 => 11, 12 => 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16,
            17 => 17, 18 => 18, 19 => 19, 20 => 20, 21 => 21, 22 => 22
        ];

        /*$emp_order = DB::table('TBL_ORDER')->orderBy('order_subject', 'ASC')->get();
        foreach ($emp_order as $row) {
        $order[$row->order_id] = $row->order_subject;
        }*/
        $emp_place = DB::table('TBL_PLACE')->orderBy('place_title', 'ASC')->get();
        foreach ($emp_place as $row) {
            $place[$row->place_id] = $row->place_title . '- (' . $row->place_id . ')';
        }

        $emp_sections = DB::table('TBL_SECTION')->orderBy('section_name', 'ASC')->get();
        foreach ($emp_sections as $row) {
            $sections[$row->section_id] = $row->section_name . ' - (' . $row->section_id . ')';
        }

        return view('promotion.edit', compact('page_title', 'place', 'sections', 'charge', 'data', 'id', 'post', 'emp_bs'));
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
            'post_id.required' => "The Post Name field is required.",
        ];
        $validation = Validator::make(
            $request->all(),
            [
                'post_id' => 'required',
            ],
            $messages
        );

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        DB::beginTransaction();

        $promotion = Carrier::findOrFail($id);

        $promotion->order_id = $request->input('order_id');
        // Post Name
        $promotion->sanction_id = $request->input('post_id');
        $sanction = DB::table('TBL_SANCTION')->select('strength_name')
            ->where('sanction_id', $request->input('post_id'))->first();
        $promotion->post_name = $sanction->strength_name;
        //$promotion->post_name = $request->input('post_name');
        $promotion->charge_id = $request->input('charge_id');
        $promotion->bs = $request->input('bs');
        $promotion->section_id = $request->input('section_id');
        $promotion->place_id = $request->input('place_id');
        $promotion->remarks = $request->input('remarks');
        $promotion->carrier_status = ($request->input('carrier_status')) ? $request->input('carrier_status') : '0';

        if (!empty($request->input('joining_date')) && $request->input('joining_date') !== null) {
            $promotion->joining_date = date('Y-m-d', strtotime($request->input('joining_date')));
        }

        $promotion->save();

        $empServ = new EmployeeService();
        $empServ->updateEmployeeInfo($promotion->emp_id);

        DB::commit();

        // Session::flash('success', 'Promotion updated successfully.');

        return Redirect('employee/emp_detail/' . $promotion->emp_id);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Carrier::find($id);
        $empId = $data->emp_id;
        DB::beginTransaction();
        DB::table('TBL_CARRIER')->where('CARRIER_ID', '=', $id)->delete();

        $empServ = new EmployeeService();
        $empServ->updateEmployeeInfo($empId);

        DB::commit();
        Session::flash('success', 'Promotion Off has been deleted successfully.');
        return Redirect('employee/emp_detail/' . $data->emp_id);
    }
}
