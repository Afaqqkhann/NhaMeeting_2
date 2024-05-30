<?php

namespace App\Http\Controllers;


use App\Services\CarrierService;
use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Employees\Employees;
use App\Services\EmployeeService;
use DB;
use Illuminate\Http\Request;
use Input;
use Session;
use Validator;

class CarrierController extends Controller
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
    public function create($emp_id, Request $request)
    {
        $reporting_officer = ['' => 'Select Reporting Officer'];
        //$order = ['' => 'Select Order'];
        $post = ['' => 'Select Post'];
        $place = ['' => 'Select Place'];
        $charge = ['' => 'Select Charge'];
        $sections = ['' => 'Select Section'];

        if ($request->is('transfer/*')) {
            $page_title = 'Add Transfer';
            $type = 'transfer';
            $emp_charge = DB::table('TBL_CHARGE')->where('charge_id', '>=', 200)->where('charge_id', '<=', 299)->orderBy('charge_title', 'ASC')->get();
        } else if ($request->is('misc/*')) {
            $page_title = 'Add Misc. Posting';
            $type = 'misc';
            $emp_charge = DB::table('TBL_CHARGE')->where('charge_id', '>=', 300)->where('charge_id', '<=', 399)->orderBy('charge_title', 'ASC')->get();
        } else if ($request->is('carrier/*')) {
            $page_title = 'Add Carrier Off';
            $type = 'carrier';
            $emp_charge = DB::table('TBL_CHARGE')->where('charge_id', '>=', 400)->where('charge_id', '<=', 499)->orderBy('charge_title', 'ASC')->get();
        } else {
            $page_title = '';
            $type = '';
        }
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

        $rep_off = DB::table('TBL_SANCTION')->orderBy('strength_name', 'ASC')->get();
        foreach ($rep_off as $row) {
            $reporting_officer[$row->sanction_id] = $row->strength_name . '- (' . $row->sanction_id . ')';
        }

        $emp_sections = DB::table('TBL_SECTION')->orderBy('section_name', 'ASC')->get();
        foreach ($emp_sections as $row) {
            $sections[$row->section_id] = $row->section_name . ' - (' . $row->section_id . ')';
        }

        /// Package Info from PMIS
        $pmis_packages = array('' => 'Seclect Package');

        $pmis_pkgs = DB::connection('oracle_pmis')->table('V_DIRECT_CONSTRUCTION')
            ->select(
                'CONTRACT_PACKAGE_TITLE',
                'CONTRACT_PACKAGE_ID',
                'PROJECT_NAME',
                'PROJECT_TITLE'
            )->whereNotNull('PACKAGE_NAME')
            ->where('PACKAGE_STATUS', '=', 1)->orderBy('CONTRACT_PACKAGE_TITLE')
            ->get();
        foreach ($pmis_pkgs as $key => $row) {
            $pmis_packages[$row->contract_package_id] = $row->contract_package_title . ' - ' . $row->project_title . ' - ' . $row->project_name; //. ' - (' . $row->contract_package_id . ')';
        }

        $current_status = ['' => 'Select Station Status', 'Releived' => 'Releived', 'Releiving Awaited' => 'Releiving Awaited'];
        $posting_status = ['' => 'Select Posting Status', 'Joined' => 'Joined', 'Joining Awaited' => 'Joining Awaited', 'Abeyance' => 'Abeyance', 'Cancelled' => 'Cancelled'];

        return view('carrier.create', compact(
            'page_title',
            'type',
            'pmis_packages',
            'emp_bs',
            'sections',
            'emp_id',
            'post',
            'charge',
            'place',
            'current_status',
            'posting_status',
            'reporting_officer'
        ));
    }

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
        $carrier = new Carrier();
        $carrier->carrier_id = ($record) ? $record->carrier_id + 1 : 1;
        $carrier->emp_id = $request->input('emp_id');
        $carrier->order_id = $request->input('order_id');
        $carrier->section_id = $request->input('section_id');
        // Post Name
        $carrier->sanction_id = $request->input('post_id');
        $sanction = DB::table('TBL_SANCTION')->select('strength_name')
            ->where('sanction_id', $request->input('post_id'))->first();
        $carrier->post_name = $sanction->strength_name;
        //$carrier->post_name = $request->input('post_name');
        /// Package Info from PMIS
        $carrier->package_id = $request->input('package_id');
        $carrier->package_name = $request->input('package_name');

        $carrier->charge_id = $request->input('charge_id');
        $carrier->bs = $request->input('bs');
        $carrier->reporting_off_id = $request->input('reporting_off_id');
        $carrier->place_id = $request->input('place_id');
        $carrier->remarks = $request->input('remarks');
        $carrier->station_status = $request->input('station_status');
        $carrier->posting_status = $request->input('posting_status');


        if (
            !empty($request->input('joining_date')) && $request->input('joining_date') !== null
            && $request->input('joining_date') !== 'dd-mm-yyyy'
        ) {

            $carrier->joining_date = date('Y-m-d', strtotime($request->input('joining_date')));
        }

        if (
            !empty($request->input('relieving_date')) && $request->input('relieving_date') !== null
            && $request->input('relieving_date') !== 'dd-mm-yyyy'
        ) {

            $carrier->relieving_date = date('Y-m-d', strtotime($request->input('relieving_date')));
        }

        $carrier->save();



        if ($request->input('carrier_type') !== 'carrier') {
            $empServ = new EmployeeService();
            $empServ->updateEmployeeInfo($carrier->emp_id);
        } else {

            DB::table('TBL_EMP')->where('emp_id', '=', $carrier->emp_id)->update(['job_status' => 'Off Job']);
        }

        DB::commit();

        return Redirect('employee/emp_detail/' . $request->input('emp_id'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if ($request->is('transfer/*'))
            $page_title = 'Transfer';
        else if ($request->is('misc/*'))
            $page_title = 'Misc Posting';
        else
            $page_title = 'Carrier';

        $data = Carrier::find($id);
        return view('carrier.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //echo "test";die;
        $reporting_officer = ['' => 'Select Reporting Officer'];
        //$order = ['' => 'Select Order'];
        $post = ['' => 'Select Post'];
        $place = ['' => 'Select Place'];
        $charge = ['' => 'Select Charge'];
        $sections = ['' => 'Select Section'];

        if ($request->is('transfer/*')) {
            $page_title = 'Edit Transfer';
            $type = 'transfer';
            $emp_charge = DB::table('TBL_CHARGE')->where('charge_id', '>=', 200)->where('charge_id', '<=', 299)->orderBy('charge_title', 'ASC')->get();
        } else if ($request->is('misc/*')) {
            $page_title = 'Edit Misc. Posting';
            $type = 'misc';
            $emp_charge = DB::table('TBL_CHARGE')->where('charge_id', '>=', 300)->where('charge_id', '<=', 399)->orderBy('charge_title', 'ASC')->get();
        } else if ($request->is('carrier/*')) {
            $page_title = 'Edit Carrier Off';
            $type = 'carrier';
            $emp_charge = DB::table('TBL_CHARGE')->where('charge_id', '>=', 400)->where('charge_id', '<=', 499)->orderBy('charge_title', 'ASC')->get();
        } else {
            $page_title = '';
            $type = '';
        }
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

        $rep_off = DB::table('TBL_SANCTION')->orderBy('strength_name', 'ASC')->get();
        foreach ($rep_off as $row) {
            $reporting_officer[$row->sanction_id] = $row->strength_name . '- (' . $row->sanction_id . ')';
        }

        $emp_sections = DB::table('TBL_SECTION')->orderBy('section_name', 'ASC')->get();
        foreach ($emp_sections as $row) {
            $sections[$row->section_id] = $row->section_name . ' - (' . $row->section_id . ')';
        }

        /// Package Info from PMIS
        $pmis_packages = array('' => 'Seclect Package');

        $pmis_pkgs = DB::connection('oracle_pmis')->table('V_DIRECT_CONSTRUCTION')
            ->select(
                'CONTRACT_PACKAGE_TITLE',
                'CONTRACT_PACKAGE_ID',
                'PROJECT_NAME',
                'PROJECT_TITLE'
            )->whereNotNull('PACKAGE_NAME')
            ->where('PACKAGE_STATUS', '=', 1)->orderBy('CONTRACT_PACKAGE_TITLE')
            ->get();
        foreach ($pmis_pkgs as $key => $row) {
            $pmis_packages[$row->contract_package_id] = $row->contract_package_title . ' - ' . $row->project_title . ' - ' . $row->project_name; // . ' - (' . $row->contract_package_id . ')';
        }



        $current_status = ['' => 'Select Station Status', 'Releived' => 'Releived', 'Releiving Awaited' => 'Releiving Awaited'];
        $posting_status = ['' => 'Select Posting Status', 'Joined' => 'Joined', 'Joining Awaited' => 'Joining Awaited', 'Abeyance' => 'Abeyance', 'Cancelled' => 'Cancelled'];

        $data = Carrier::find($id);



        return view('carrier.edit', compact(
            'page_title',
            'type',
            'emp_bs',
            'data',
            'pmis_packages',
            'sections',
            'post',
            'charge',
            'place',
            'current_status',
            'posting_status',
            'reporting_officer'
        ));
    }

    public function getEmployeeCarrier($empId, CarrierService $carrier)
    {
        return $carrier->getEmpCarrier($empId);
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

        $carrier = Carrier::findOrFail($id);

        $carrier->order_id = $request->input('order_id');
        // Post Name
        $carrier->sanction_id = $request->input('post_id');
        $sanction = DB::table('TBL_SANCTION')->select('strength_name')
            ->where('sanction_id', $request->input('post_id'))->first();
        $carrier->post_name = $sanction->strength_name;
        //$carrier->post_name = $request->input('post_name');
        /// Package Info from PMIS
        $carrier->package_id = $request->input('package_id');

        $carrier->bs = $request->input('bs');
        $carrier->charge_id = $request->input('charge_id');
        $carrier->reporting_off_id = $request->input('reporting_off_id');
        $carrier->place_id = $request->input('place_id');
        $carrier->remarks = $request->input('remarks');
        $carrier->station_status = $request->input('station_status');
        $carrier->section_id = $request->input('section_id');
        $carrier->carrier_status = ($request->input('carrier_status')) ? $request->input('carrier_status') : '0';

        $carrier->posting_status = $request->input('posting_status');
        if (
            !empty($request->input('joining_date')) && $request->input('joining_date') !== null
            && $request->input('joining_date') !== 'dd-mm-yyyy'
        ) {

            $carrier->joining_date = date('Y-m-d', strtotime($request->input('joining_date')));
        } else {
            $carrier->joining_date = null;
        }

        if (
            !empty($request->input('relieving_date')) && $request->input('relieving_date') !== null
            && $request->input('relieving_date') !== 'dd-mm-yyyy'
        ) {
            $carrier->relieving_date = date('Y-m-d', strtotime($request->input('relieving_date')));
        } else {
            $carrier->relieving_date = null;
        }

        if ($request->input('carrier_type') == 'carrier') {
            $job_status = ($request->input('carrier_status') == '1') ? 'Off Job' : 'On Job';
            DB::table('TBL_EMP')->where('emp_id', '=', $carrier->emp_id)->update(['job_status' => $job_status]);
        }

        $carrier->save();


        if ($request->input('carrier_type') !== 'carrier') {
            $empServ = new EmployeeService();
            $empServ->updateEmployeeInfo($carrier->emp_id);
        } else {
            $job_status = ($request->input('carrier_status') == '1') ? 'Off Job' : 'On Job';
            DB::table('TBL_EMP')->where('emp_id', '=', $carrier->emp_id)->update(['job_status' => $job_status]);
        }

        DB::commit();

        Session::flash('success', 'Record updated successfully.');
        return Redirect('employee/emp_detail/' . $carrier->emp_id);
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
        $empID = $data->emp_id;
        DB::beginTransaction();

        DB::table('TBL_CARRIER')->where('CARRIER_ID', '=', $id)->delete();
        $empServ = new EmployeeService();
        $empServ->updateEmployeeInfo($empID);

        DB::commit();

        Session::flash('success', 'Record has been deleted successfully.');
        return Redirect('employee/emp_detail/' . $data->emp_id);
    }
}
