<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReferSlipRequest;
use App\Models\Emp;
use App\Models\Family;
use App\Models\MedEntitlement;
use App\Models\Panel;
use App\Models\PanelType;
use App\Models\Place;
use App\Models\ReferSlip;
use App\Models\Region;
use App\Models\ReferTreatment;
use Auth;
use Illuminate\Http\Request;
use Session;
use Carbon\Carbon;
use DB;
use Validator;

class ReferSlipController extends Controller
{
    //protected $slip_service;

    /*public function __construct(ReferSlipService $slip_service)
    {
    $this->slip_service = $slip_service;
    }*/
    public function __construct()
    {
        $this->middleware('auth');
        if (!Auth::user()->can('refer_slip'))
            abort(403);
        //$this->auth()->user()->can('referslip');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Refer Slips';
        $query = ReferSlip::query();
        // && !auth()->user()->hasRole('memishq')
        
        if (!auth()->user()->hasRole('superadmin') && auth()->user()->hasRole('memis_list'))
            $query = $query->where('region_id', '=', auth()->user()->region_id);
		else if (!auth()->user()->hasRole('superadmin'))
            $query = $query->where('user_id', '=', auth()->user()->id);
        $referslips = $query->with([
            //$referslips = ReferSlip::with([
            'employee' => function ($q) {
                $q->select('emp_id', 'emp_name');
            },
            'family.relation',
            'panel' => function ($q) {
                $q->select('panel_id', 'panel_title');
            },
            'panelType' => function ($q) {
                $q->select('pt_id', 'pt_title');
            },
            'region' => function ($q) {
                $q->select('region_id', 'region_name');
            },
            'user' => function ($q) {
                $q->select('id', 'name');
            },
        ])
            ->orderBy('refer_slip_id', 'desc')->take(800)->get();

        $date = Carbon::now()->format('Y-m-d');
        $userId = Auth::user()->id;
        /**** Top Blocks Functions */
        // echo "<pre>";
        //print_r($referslips);die; 

        DB::setDateFormat('YYYY-MM-DD');
        $data = array(
            'refer_slips' => DB::select("SELECT MEMIS_REF_TT('$date'," . $userId . ") refer_slips FROM dual"),
            'user_ref' => DB::select("SELECT MEMIS_U_REF('$date'," . $userId . ") user_ref FROM dual"),
            'employee_ref' => DB::select("SELECT MEMIS_E_REF('$date'," . $userId . ") employee_ref FROM dual"),
            'dep_ref' => DB::select("SELECT MEMIS_D('$date'," . $userId . ") dep_ref FROM dual"),
            'curr_mon_ref' => DB::select("SELECT MEMIS_C_M_REF('$date'," . $userId . ") curr_mon_ref FROM dual"),
            'curr_mon_tt' => DB::select("SELECT MEMIS_C_M_TT('$date'," . $userId . ") curr_mon_tt FROM dual"),
            'curr_mon_emp' => DB::select("SELECT MEMIS_C_M_EMP('$date'," . $userId . ") curr_mon_emp FROM dual"),
            'curr_mon_dep' => DB::select("SELECT MEMIS_C_M_D('$date'," . $userId . ") curr_mon_dep FROM dual"),
            'budget_u_tt' => DB::select("SELECT MEMIS_BUDGET_U_TT('$date'," . $userId . ") budget_u_tt FROM dual"),
            'budget_u_u' => DB::select("SELECT MEMIS_BUDGET_U_U('$date'," . $userId . ") budget_u_u FROM dual"),
            'budget_cm_tt' => DB::select("SELECT MEMIS_BUDGET_CM_TT('$date'," . $userId . ") budget_cm_tt FROM dual"),
            'budget_cm_u' => DB::select("SELECT MEMIS_BUDGET_CM_U('$date'," . $userId . ") budget_cm_u FROM dual"),
            'ue_d_y_tt' => DB::select("SELECT MEMIS_UE_D_Y_TT('$date'," . $userId . ") ue_d_y_tt FROM dual"),
            'ue_d_y_u' => DB::select("SELECT MEMIS_UE_D_Y_U('$date'," . $userId . ") ue_d_y_u FROM dual"),
            'ue_d_m_tt' => DB::select("SELECT MEMIS_UE_D_M_TT('$date'," . $userId . ") ue_d_m_tt FROM dual"),
            'ue_d_m_u' => DB::select("SELECT MEMIS_UE_D_M_U('$date'," . $userId . ") ue_d_m_u FROM dual")
        );


        return view('referslip.index', compact('page_title', 'referslips', 'employees', 'dependents', 'panels', 'regions', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add Refer Slip';

        $employees = ['' => 'Select Employee'];
        $emps = Emp::select('emp_id', 'emp_name', 'cnic', 'designation')->orderBy('emp_name', 'asc')->get();
        //print_r($emps[1]->employee);die;
        foreach ($emps as $key => $emp) {
            $employees[$emp->emp_id] = $emp->emp_name . ' - ' . $emp->designation . ' (' . $emp->cnic . ')';
        }
        // print_r($employees);die;

        $service_types = ['' => 'Select Service Type'];
        $ser_types = PanelType::select('pt_id', 'pt_title')->orderBy('pt_id', 'asc')->get();

        foreach ($ser_types as $key => $ser) {
            $service_types[$ser->pt_id] = $ser->pt_title;
        }
        $dependents = array('' => 'Select Patient');
        return view('referslip.create', compact('page_title', 'service_types', 'employees', 'dependents'));
    }

    /**
     * Get NHA Panels
     */
    public function getPanels($panel_type_id)
    {
        $query = Panel::select('panel_id', 'panel_title')->where('panel_type_id', $panel_type_id);
        if (!auth()->user()->hasRole('superadmin') && !auth()->user()->hasRole('memishq') && !auth()->user()->hasRole('cmo'))
            $query = $query->where('region_id', auth()->user()->region_id);

        $panels = $query->orderBy('panel_title', 'ASC')->get();
        //echo '<pre>';
        //print_r($panels);die;

        return response()->json(['panels' => $panels]);
    }
    /**
     * Check Patient Record for Refer slip Geenration
     */
    public function checkPatientRecord(Request $request)
    {
        if ($request->ajax()) {
            $patient_id = $request->patient_id;
            $emp_id = $request->emp_id;

            if ($patient_id == 0) {
                $employee = Emp::where('emp_id', $emp_id)->first();
                if (!empty($employee->fp_id) && $employee->dob && $employee->cnic) {
                    return '';
                } else {
                    $total_slip_count = $employee->wp_slip_count;
                }
            } else {

                $family = Family::where('family_id', $patient_id)->first();
                if ($family->picture && $family->date_of_birth && $family->cnic) {
                    return '';
                } else {
                    $total_slip_count = $family->wp_slip_count;
                }
            }
            return response()->json(['slipCount' => $total_slip_count]);
        }
    }

    /**
     *  Family Detail of Employees
     * @return \Illuminate\Http\Response
     */
    public function getEmpFamily(Request $request) //, FamilyService $family_service)
    {
        $emp_id = $request->emp_id;
        //echo $emp_id;die;
        $families = Family::with('relation')
            ->where('emp_id', $emp_id)
            ->where('family_status', 1)
            //->where('hq_status', 1)
            //  ->where('regional_status', 1)
            ->orderBy('name', 'asc')
            ->get();
        return response()->json(['families' => $families]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReferSlipRequest $request)
    {

        $data = $request->all();

        $record = ReferSlip::orderBy('refer_slip_id', 'desc')->first();

        $today = date('Y-m-d');

        $refer_slip = new ReferSlip();
        $refer_slip->refer_slip_id = ($record) ? $record->refer_slip_id + 1 : 1;
        $refer_slip->emp_id = $data['emp_id'];
        $refer_slip->dated = date('Y-m-d');
        $refer_slip->dependent_id = $data['patient_id'];
        $refer_slip->referred_to = $data['referred_to'];
        $refer_slip->panel_id = $data['panel_id'];
        $refer_slip->panel_type_id = $data['panel_type_id'];
        $refer_slip->consultation = ($request->consultation) ? $request->consultation : '0';
        $refer_slip->emergency_treatment = ($request->emergency_treatment) ? $request->emergency_treatment : '0';
        $refer_slip->lab_investigation = ($request->lab_investigation) ? $request->lab_investigation : '0';
        $refer_slip->revisit = ($request->revisit) ? $request->revisit : '0';
        $refer_slip->admission = ($request->admission) ? $request->admission : '0';
        $refer_slip->remarks = $data['remarks'];
        $refer_slip->user_id = auth()->user()->id;
        $refer_slip->region_id = auth()->user()->region_id;
        // For Employee
        if (!empty($request->refer_slip_count) && $data['patient_id'] == 0) {
            $employee = Emp::where('emp_id', $data['emp_id'])->first();
            $employee->wp_slip_count = $request->refer_slip_count - 1;
            $employee->save();
        }

        // For Family
        else if (!empty($request->refer_slip_count) && $data['patient_id'] != 0) {
            $family = Family::where('family_id', $data['patient_id'])->first();
            $family->wp_slip_count = $request->refer_slip_count - 1;
            $family->save();
        }


        $refer_slip->save();

        return redirect('referslip/print/' . $refer_slip->refer_slip_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printReferSlip($id)
    {
        $page_title = "Refer Slip for Medical Service of NHA Employee";
        //$refer_slip = $this->slip_service->getReferSlipData($id);
        //$employee = $this->slip_service->getEmployeeInfo($refer_slip->emp_id);

        //$qrcode = QrCode::generate("string");
        //print_r($qr);die;
        $place = Place::where('region_id', Auth()->user()->region_id)->where('place_type_id', 3)->orWhere('place_type_id', 2)->where('place_status', 1)->first(['address']);

        $refer_slip = ReferSlip::with([
            'employee', 'family',
            'family.relation',
            'panel' => function ($q) {
                $q->select('panel_id', 'panel_title');
            },
            'region' => function ($q) {
                $q->select('region_id', 'region_name');
            },
            'user' => function ($q) {
                $q->select('id', 'name');
            },

        ])->where('refer_slip_id', $id)->first();

        $med_entitlement = MedEntitlement::select('entitlement')->where('id', $refer_slip->employee->bs)->first();

        $refer_slip->medicalOfficer = (Auth()->user()->region_id == 13) ? 'Chief Medical Officer' : 'Medical Officer';
        $refer_slip->welfOfficer = (Auth()->user()->region_id == 13) ? 'Dy. Director (Welfare)' : 'AD/DD (Welfare/Admin)';
        $refer_slip->officeAddress = $place->address;

        // $employee = $this->slip_service->getEmployeeInfo(4000);

        return view('referslip.print_refer_slip', compact('page_title', 'refer_slip', 'med_entitlement'));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $page_title = "Refer Slip";

        $referSlip = ReferSlip::findOrFail($id);

        $referTreatments = ReferTreatment::where('refer_id', $id)->orderBy('rt_id', 'desc')->get();

        return view('referslip.show', compact('page_title', 'referSlip', 'referTreatments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Add Refer Slip';

        $referslip = ReferSlip::find($id);
        // $referslip = $this->slip_service->getReferSlip($id);

        //$employees = Emp::orderBy('emp_name', 'asc')->lists('emp_name', 'emp_id');
        $employees = Emp::orderBy('emp_name', 'asc')->get(['emp_id', 'emp_name', 'cnic'])
            ->lists('employee', 'emp_id'); //->lists('emp_name', 'emp_id');
        //$employees->prepend('Select Employee');
        /*echo '<pre>';
        print_r($employees);die;*/

        $panels = Panel::lists('panel_title', 'panel_id');
        //$panels->prepend('Select Panel');

        $regions = Region::orderBy('region_name', 'asc')->lists('region_name', 'region_id');
        //$regions->prepend('Select Region');

        $dependents = Family::where('emp_id', $referslip->emp_id)->orderBy('name', 'asc')->lists('name', 'family_id');

        return view('referslip.edit', compact('page_title', 'referslip', 'employees', 'dependents', 'panels', 'regions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ReferSlipRequest $request, $id)
    {
        $data = $request->all();
        $messages = [
            'emp_name.required' => 'The Employee Name field is required.',
            //'dependent_id.required' => 'The Patient field is required.',

        ];
        $validation = Validator::make(
            $data,
            [
                'emp_id' => 'required',
                //  'dependent_id' => 'required',

            ],
            $messages
        );

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $refer_slip = ReferSlip::find($id);
        $refer_slip->emp_id = $data['emp_id'];
        $refer_slip->dated = date('Y-m-d');
        $refer_slip->dependent_id = $data['patient_id'];
        $refer_slip->referred_to = $data['referred_to'];
        $refer_slip->panel_id = $data['panel_id'];
        $refer_slip->consultation = $data['consultation'];
        $refer_slip->emergency_treatment = $data['emergency_treatment'];
        $refer_slip->lab_investigation = $data['lab_investigation'];
        $refer_slip->revisit = $data['revisit'];
        $refer_slip->admission = $data['admission'];
        $refer_slip->user_id = auth()->user()->id;
        $refer_slip->region_id = auth()->user()->region_id;
        $refer_slip->save();
        //$this->slip_service->updateReferSlip($data, $id);

        Session::flash('success', 'Refer Slip has been updated successfully.');

        return redirect('referslip');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // use force delete to avoid soft delete
        ReferSlip::where('refer_slip_id', $id)->forceDelete();
        //$this->slip_service->deleteReferSlip($id);

        Session::flash('success', 'Refer Slip has been deleted successfully.');

        return redirect('referslip');
    }
}
