<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employees\Employees;
use App\Models\Leaves;
use App\User;
use Auth;
use Datatables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Redirect;
use Session;
use URL;
use Validator;
//use Dompdf\Dompdf;

class EmployeesController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');
    }

    public function add_employee()
    {

        $page_title = 'Add Employee';
        return view('employees.add_employee', compact('page_title'));
    }

    public function set_employee()
    {
        $input = Request::except('_token');

        // custom validation messages
        $messages = array(
            'required' => 'The :attribute field is required.',
        );

        $validator = Validator::make(Request::all(), [
            'emp_name' => 'required|max:20',
            'dob' => 'required|date',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        } else {
            $row = DB::table('TBL_EMP')->orderBy('emp_id', 'DESC')->first();

            $data = array(
                'EMP_ID' => $row->emp_id + 1,
                'EMP_NAME' => $input['emp_name'],
                'F_H_NAME' => $input['f_h_name'],
                'DOB' => date('Y-m-d', strtotime($input['dob'])),
                'GENDER' => $input['gender'],
                'CNIC' => $input['cnic'],
                'DOMICILE' => $input['domicile'],
                'PLACE_OF_POSTING' => $input['place_of_posting'],
                'MARITAL_STATUS' => $input['marital_status'],
                'RELIGION' => $input['religion'],
                'HOME_DISTRICT' => $input['home_district'],
                'VI_MARK' => $input['vi_mark'],
                'LANGUAGES' => $input['languages'],
                'HIGHEST_QUALIFICATION' => $input['highest_qualification'],
                'DUAL_NATIONALITY' => $input['dual_nationality'],
                'REF_FILE_NO' => $input['ref_file_no'],
                'DESIGNATION' => $input['designation'],
                'BS' => $input['bs'],
                'SG' => $input['sg'],
                'SERVICE_TYPE' => $input['service_type'],
                'CADRE' => $input['cadre'],
                'SENIORITY_POSITION' => $input['seniority_position'],
                'SECTION' => $input['section'],
                'WING' => $input['wing'],
                'REGION' => $input['region'],
                'CURRENT_STATUS' => $input['current_status'],
                'SALARY_ACCOUNT' => $input['salary_account'],
                'APP_AGAINST_PROJECT' => $input['app_against_project'],
                'OFF_PHONE_NO' => $input['off_phone_no'],
                'OFF_EXT_NO' => $input['off_ext_no'],
                'RES_PHONE_NO' => $input['res_phone_no'],
                'MOBILE_NO' => $input['mobile_no'],
                'EMRGCY_CONTACT_NAME' => $input['emrgcy_contact_name'],
                'EMRGCY_CONTACT_PHONENO' => $input['emrgcy_contact_phoneno'],
                'EMRGCY_CONTACT_ADDRESS' => $input['emrgcy_contact_address'],
                'DATE_OF_JOINING' => ($input['date_of_joining'] == '') ? '' : date('Y-m-d', strtotime($input['date_of_joining'])),
                'RECRUITMENT_QUOTA' => $input['recruitment_quota'],
                'QUOTA_ADV_DATE' => ($input['quota_adv_date'] == '') ? '' : date('Y-m-d', strtotime($input['quota_adv_date'])),
                'DATE_OF_EXPIRY' => ($input['date_of_expiry'] == '') ? '' : date('Y-m-d', strtotime($input['date_of_expiry'])),
                'DATE_OF_25YR_SERVICE' => ($input['date_of_25yr_service'] == '') ? '' : date('Y-m-d', strtotime($input['date_of_25yr_service'])),
                'DATE_OF_SUPERANNUATION' => ($input['date_of_superannuation'] == '') ? '' : date('Y-m-d', strtotime($input['date_of_superannuation'])),
                'PROBATION' => $input['probation'],
                'MAILING_ADDRESS' => $input['mailing_address'],
                'PERMENENT_ADDRESS' => $input['permenent_address'],
                'WORKING_AS_DESIG' => $input['working_as_desig'],
                'WORKING_AS_BS' => $input['working_as_bs'],
                'FAX_NO' => $input['fax_no'],
                'BLOOD_GROUP' => $input['blood_group'],
                'TTL_LEAVE_BAL' => $input['ttl_leave_bal'],
                'MOVE_OVER' => $input['move_over'],
                'VERIFIED_ON' => $input['verified_on'],
                'PERS_NO' => $input['pers_no'],
                'REGULARIZATION_FROM' => $input['regularization_from'],
                'REGULARIZATION_DATE' => ($input['regularization_date'] == '') ? '' : date('Y-m-d', strtotime($input['regularization_date'])),
                'REGULARIZATION_RECOMEND' => $input['regularization_recomend'],
                'NATURE_OF_APPOINTMENT' => $input['nature_of_appointment'],
                'ADJUSTED_AGAINST' => $input['adjusted_against'],
            );

            // now insert new record
            DB::table('TBL_EMP')->insert($data);
        }

        return Redirect::to('/employees/employees_list');
    }

    public function employees_list()
    {
        if (!Auth::user()->can('employees_detail')) {
            abort(403);
        }

        if (Auth::user()->username == 'ddpersonnel-2') {
            $data = DB::table('V_EMP')->select([
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
                'DESIGNATION', 'HIGHIEST_QUALIFICATION', 'AGE_YEAR', 'PLACE_OF_POSTING', 'CURRENT_POST', 'CURRENT_BS',
                'CADRE_NAME', 'JOB_STATUS',
            ])->where('BS', '<=', 17)->orWhere('BS', '=', [null])->orderBy('emp_id', 'DESC')->orderBy('emp_name', 'ASC')->get();
        } else if (Auth::user()->username == 'ddpersonnel-1') {
            $data = DB::table('V_EMP')->select([
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
                'DESIGNATION', 'HIGHIEST_QUALIFICATION', 'AGE_YEAR', 'PLACE_OF_POSTING', 'CURRENT_POST', 'CURRENT_BS',
                'CADRE_NAME', 'JOB_STATUS',
            ])->where('BS', '>=', 17)->orWhere('BS', '=', null)->orderBy('emp_id', 'DESC')->orderBy('emp_name', 'ASC')->get();
        } else {
            $data = DB::table('V_EMP')->select([
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
                'DESIGNATION', 'HIGHIEST_QUALIFICATION', 'AGE_YEAR', 'PLACE_OF_POSTING', 'CURRENT_POST', 'CURRENT_BS',
                'CADRE_NAME', 'JOB_STATUS',
            ])->orderBy('emp_id', 'DESC')->orderBy('emp_name', 'ASC')->get();
        }
        $page_title = 'Employees';

        return view('employees.employees_list', compact('page_title', 'data'));
    }

    /////////////
    public function retired_employees()
    {
        /*if(!Auth::user()->can('employees_detail'))
        abort(403);*/

        $data = DB::table('V_EMP')->select([
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
        ])->where('emp_pension_status', '=', 1)->get();

        //print_r($data);die;

        $page_title = 'Retired Employees';
        return view('employees.employees_list', compact('page_title', 'data'));
    }

    public function employees_list_data()
    {
        if (!Auth::user()->can('employees_detail')) {
            abort(403);
        }

        $emp = DB::table('V_EMP')->select(['FP_ID', 'EMP_ID', 'EMP_NAME', 'F_H_NAME', 'DOB', 'CNIC', 'DOMICILE', 'BS', 'REF_FILE_NO',
            'DATE_OF_APPOINTMENT', 'TYPE_OF_APPOINTMENT']);

        return Datatables::of($emp)
            ->addColumn('action', function ($emp) {
                $profile_view = (Auth::user()->hasRole('admin')) ? '<a href="' . URL::to('/employee/profile') . '/' . $emp->emp_id . '" class="btn btn-xs btn-default"><i class=" fa fa-picture-o"></i> Profile</a>' : '';
                return '<a href="' . URL::to('/employee/icp_chart') . '/' . $emp->emp_id . '" class="btn btn-xs btn-primary"><i class="fa fa-file-text-o"></i>  ICP Chart</a>' . $profile_view;
            })
        /*->addColumn('profile_picture', function($emp) {
        return '<img src="'.URL::to('/storage/emp_pic').'/'.$emp->fp_id.'" class="img">';
        })*/
            ->editColumn('fp_id', function ($emp) {
                if (file_exists('storage/emp_pic/' . $emp->fp_id . '.jpg')) {
                    return '<img src="' . URL::to('/storage/emp_pic') . '/' . $emp->fp_id . '.jpg" style="width:60px;" class="img-responsive">';
                } else {
                    return '<img src="' . URL::to('/storage/emp_pic') . '/default.png" style="width:60px;" class="img-responsive">';
                }

            })
            ->make();

    }

    /**
     *  Show ICP chart of an employee
     *
     *  @param int $id
     *  @return
     */

   public function emp_detail($id)
    {

        $emp = DB::table('TBL_EMP')->where('EMP_ID', $id)->first();
        $appoint = DB::table('TBL_APPOINTMENT')->where('EMP_ID', $id)->orderBy('appointment_id', 'DESC  ')->get();
        $family = DB::table('TBL_FAMILY')->where('EMP_ID', $id)->orderBy('FAMILY_ID', 'DESC  ')->get();

        $eduction = DB::table('TBL_EDUCATION')
            ->leftJoin('TBL_EDUCTION_TYPE', 'TBL_EDUCATION.DOCUMENT_TYPE_ID', '=', 'TBL_EDUCTION_TYPE.EDU_TYPE_ID')
            ->where('TBL_EDUCATION.EMP_ID', $id)->orderBy('TBL_EDUCATION.EDUCATION_ID', 'DESC  ')->get();

        $experience = DB::table('TBL_EXPERIENCE')->where('EMP_ID', $id)->orderBy('EXP_ID', 'DESC  ')->get();
        $reward = DB::table('TBL_REWARD')->where('EMP_ID', $id)->orderBy('REWARD_ID', 'DESC  ')->get();
        $leave = DB::table('TBL_leave')->where('EMP_ID', $id)->orderBy('leave_id', 'DESC  ')->get();
        $extension = DB::table('TBL_EMP_EXTENSION')->where('EMP_ID', $id)->orderBy('EXT_ID', 'DESC ')->get();
        $noc = DB::table('TBL_NOC')->join('TBL_EMP', 'TBL_NOC.EMP_ID', '=', 'TBL_EMP.EMP_ID')->where('TBL_NOC.EMP_ID', $id)->orderBy('noc_id', 'DESC  ')->get();

        $promotion = DB::table('V_CARRIER')
            ->select('V_CARRIER.POST_NAME',
                'V_CARRIER.CARRIER_ID', 'V_CARRIER.BS', 'V_CARRIER.CHARGE_TITLE', 'V_CARRIER.PLACE_ID'
                , 'V_CARRIER.SECTION_ID', 'V_CARRIER.PLACE_TITLE', 'V_CARRIER.SECTION_NAME', 'V_CARRIER.JOINING_DATE',
                'V_CARRIER.ORDER_ID')
            ->where('EMP_ID', $id)
            ->where('CHARGE_ID', '>=', 100)->where('CHARGE_ID', '<', 199)
            ->orderBy('carrier_id', 'DESC')->get();

        $posting = DB::table('V_CARRIER')
            ->select('TBL_SANCTION.STRENGTH_NAME', 'V_CARRIER.POST_NAME',
                'V_CARRIER.CARRIER_ID', 'V_CARRIER.BS', 'V_CARRIER.CHARGE_TITLE', 'V_CARRIER.PLACE_ID'
                , 'V_CARRIER.SECTION_ID', 'V_CARRIER.PLACE_TITLE', 'V_CARRIER.SECTION_NAME', 'V_CARRIER.JOINING_DATE'
                , 'V_CARRIER.RELIEVING_DATE', 'V_CARRIER.REMARKS', 'V_CARRIER.ORDER_ID')
            ->leftJoin('TBL_SANCTION', 'V_CARRIER.REPORTING_OFF_ID', '=', 'TBL_SANCTION.SANCTION_ID')
            ->where('EMP_ID', $id)
            ->where('CHARGE_ID', '>=', 200)->where('CHARGE_ID', '<', 299)
            ->orderBy('carrier_id', 'DESC')->get();

        $misc = DB::table('V_CARRIER')
            ->select('TBL_SANCTION.STRENGTH_NAME', 'V_CARRIER.POST_NAME',
                'V_CARRIER.CARRIER_ID', 'V_CARRIER.BS', 'V_CARRIER.CHARGE_TITLE', 'V_CARRIER.PLACE_ID'
                , 'V_CARRIER.SECTION_ID', 'V_CARRIER.PLACE_TITLE', 'V_CARRIER.SECTION_NAME', 'V_CARRIER.JOINING_DATE'
                , 'V_CARRIER.RELIEVING_DATE', 'V_CARRIER.REMARKS', 'V_CARRIER.ORDER_ID')
            ->leftJoin('TBL_SANCTION', 'V_CARRIER.REPORTING_OFF_ID', '=', 'TBL_SANCTION.SANCTION_ID')
            ->where('EMP_ID', $id)
            ->where('CHARGE_ID', '>=', 300)->where('CHARGE_ID', '<', 399)
            ->orderBy('carrier_id', 'DESC')->get();
        $carrier = DB::table('V_CARRIER')
            ->select('TBL_SANCTION.STRENGTH_NAME', 'V_CARRIER.POST_NAME',
                'V_CARRIER.CARRIER_ID', 'V_CARRIER.BS', 'V_CARRIER.CHARGE_TITLE', 'V_CARRIER.PLACE_ID'
                , 'V_CARRIER.SECTION_ID', 'V_CARRIER.PLACE_TITLE', 'V_CARRIER.SECTION_NAME', 'V_CARRIER.JOINING_DATE'
                , 'V_CARRIER.RELIEVING_DATE', 'V_CARRIER.REMARKS', 'V_CARRIER.ORDER_ID')
            ->leftJoin('TBL_SANCTION', 'V_CARRIER.REPORTING_OFF_ID', '=', 'TBL_SANCTION.SANCTION_ID')
            ->where('EMP_ID', $id)
            ->where('CHARGE_ID', '>=', 400)->where('CHARGE_ID', '<', 499)
            ->orderBy('carrier_id', 'DESC')->get();

        return view('employees.emp_detail', compact('emp', 'appoint', 'family', 'eduction', 'experience', 'carrier', 'reward', 'leave', 'noc', 'extension', 'promotion', 'posting', 'misc'));
    }

	
        
    public function employee_icp($emp_id = null)
    {
        /*if(!Auth::user()->can('employees_detail'))
        abort(403);*/
        //echo 'tt';die;
		
		$empId = (!$emp_id) ? auth()->user()->emp_id : $emp_id;
        

        $emp = DB::table('V_EMP')->select('EMP_ID', 'EMP_NAME', 'F_H_NAME', 'DOB', 'AGE_YEAR',
            'TAX_FILLER', 'DOR', 'TT_EL', 'TT_ACC_EL',
            'GENDER', 'CNIC', 'HIGHIEST_QUALIFICATION', 'DOMICILE', 'DESIGNATION',
            'MOBILE_NO', 'RELIGION', 'PERMENENT_ADDRESS', 'MAILING_ADDRESS', 'HOME_DISTRICT',
            'CADRE_NAME', 'CURRENT_POST', 'CURRENT_BS', 'PLACE_OF_POSTING', 'VI_MARK',
            'REF_FILE_NO', 'APPOINTMENT_ID', 'FP_ID', 'BS', 'SANCTION_ID',
            'DATE_OF_APPOINTMENT', 'TYPE_OF_APPOINTMENT', 'THROUGH_ADVER', 'SERVICE_TYPE', 'POSTING_DATE',
            'POSTING_TENURE', 'TRANSFER_DATE', 'TRANSFER_TENURE', 'TOTAL_POSTING', 'TOTAL_TRANSFER',
            'ACR_TOTAL', 'ACR_SUBMITTIED', 'ACR_REMAINING', 'ASSET_TOTAL', 'ASSET_SUBMITTIED',
            'ASSET_REMAINING', 'TOTAL_REWARD', 'TOTAL_DEPENDENTS', 'TOTAL_PANELTIES', 'TOTAL_TRAINING',
            'JOB_STATUS', 'EMP_PENSION_STATUS', 'DATE_OF_JOINING')->where('EMP_ID', $empId)->first();

        $date1 = date("m/d/Y");
        $date2 = date('m/d/Y', strtotime($emp->date_of_joining));

        $date1Timestamp = strtotime($date1);
        $date2Timestamp = strtotime($date2);
        $difference = $date1Timestamp - $date2Timestamp;

        $years = floor($difference / (365 * 60 * 60 * 24));

        $months = floor(($difference - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));

        $days = floor(($difference - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

        $service_length = $years . " " . "Years" . "," . $months . "  " . "Months" . "  " . " ," . $days . " " . "Days";

        $family = DB::table('TBL_FAMILY')->where('EMP_ID', $empId)->orderBy('FAMILY_ID', 'DESC  ')->get();
        //echo $service_length; die;

        //echo"<pre>";print_r($emp);die;
        $appointments = DB::table('V_APPOINTMENT')->select('APPOINTMENT_ID', 'POST_NAME', 'BS', 'DATE_OF_APPOINTMENT', 'TYPE_OF_APPOINTMENT', 'APPOINTMENT_QUOTA', 'SERVICE_TYPE', 'QUOTA_ADV_DATE', 'THROUGH_ADVER', 'EXAM_HELD', 'MERIT_NO')->where('EMP_ID', $emp->emp_id)->orderBy('APPOINTMENT_ID', 'DESC')->get();
        $education = DB::table('TBL_EDUCATION')->where('EMP_ID', $emp->emp_id)->orderBy('sessions', 'DESC')->get();

        $transfer = DB::table('V_CARRIER')->where('charge_id', '>=', 200)->where('charge_id', '<', 300)->where('EMP_ID', $emp->emp_id)->orderBy('carrier_status', 'DESC')->orderBy('joining_date', 'DESC')->orderBy('BS', 'DESC')->get(); //->orderBy('BS', 'DESC')->orderBy('RELIEVING_DATE', 'DESC')
        $promotion = DB::table('V_CARRIER')->where('charge_id', '>=', 100)->where('charge_id', '<', 200)->where('EMP_ID', $emp->emp_id)->orderBy('joining_date', 'DESC')->get();

        $misc = DB::table('V_CARRIER')->where('charge_id', '>=', 300)->where('charge_id', '<', 400)->where('EMP_ID', $emp->emp_id)->orderBy('carrier_status', 'DESC')->orderBy('joining_date', 'DESC')->orderBy('BS', 'DESC')->get();

        $training_local = DB::table('V_TRAINING_DONE')->where('PLACE', '=', 'Pakistan')
            ->where(function ($query) {
                $query->where('TRAINING_TYPE', '=', 'Training')
                    ->OrWhere('TRAINING_TYPE', '=', 'Seminar');
            })
            ->where('EMP_ID', $emp->emp_id)
            ->orderBy('start_date', 'DESC')->get();
        //echo "<pre>"; print_r($transfer); die;

        $training_forign = DB::table('V_TRAINING_DONE')->where('PLACE', '!=', 'Pakistan')
            ->where('EMP_ID', $emp->emp_id)->where(function ($query) {
            $query->where('TRAINING_TYPE', '=', 'Training')
                ->OrWhere('TRAINING_TYPE', '=', 'Seminar');
        })
            ->where('EMP_ID', $emp->emp_id)
            ->orderBy('start_date', 'DESC')->get();

        $offical_visits = DB::table('V_TRAINING_DONE')->where('PLACE', '!=', 'Pakistan')
            ->where(function ($query) {
                $query->where('TRAINING_TYPE', '!=', 'Training')
                    ->OrWhere('TRAINING_TYPE', '!=', 'Seminar');
            })
            ->where('EMP_ID', $emp->emp_id)
            ->orderBy('start_date', 'DESC')->get();
        //echo"<pre>"; print_r($offical_visits);die;

        $reward = DB::table('V_REWARDS')->where('EMP_ID', $emp->emp_id)->orderBy('letter_date', 'DESC')->get();
        $penalties = DB::table('V_PENALTIE')->where('EMP_ID', $emp->emp_id)->orderBy('off_order_date', 'DESC')->get();
        $experience = DB::table('TBL_EXPERIENCE')->where('EMP_ID', $emp->emp_id)->orderBy('joining_date', 'DESC')->get();
        $assets = DB::table('TBL_ASSETS')->join('TBL_YEAR', 'TBL_ASSETS.YEAR_ID', '=', 'TBL_YEAR.YEAR_ID')->where('TBL_ASSETS.EMP_ID', $emp->emp_id)->orderBy('year_title', 'DESC')->get();
        //print_r($assets);die;
        //$acrs         = DB::table('V_EMP_ASSETS_ACRS')->where('EMP_ID', $emp->emp_id)->orderBy('YEAR_ID','DESC')->get();
        //echo "<pre>";
        $acrs = DB::table('V_ACR')->where('EMP_ID', $emp->emp_id)->orderBy('year_title', 'DESC')->get();
        //echo"<pre>";print_r($acrs);die;
        //echo"test"; die;
        //$monthly_attendance = DB::connection('sqlsrv')->table('VIEWMONTHLYDATA')->where('EMPNO', '=', //$emp->fp_id)->get();

        $conn = DB::connection('sqlsrv_fin');
        // Sp_PaySlip ("17301-8391053-1", 1, 2016)
        $finance = $conn->select('EXEC dbo.Sp_EmpPayments ?', array($emp->cnic));

        if ($emp) {
            $page_title = $emp->emp_name;
        } else {
            Session::flash('error', 'No Record Found!!');
            $page_title = '';
        }

        return view('employees.employee_detail', compact('page_title', 'service_length', 'family', 'emp', 'appointments', 'education', 'transfer', 'misc', 'promotion', 'training_local', 'training_forign', 'offical_visits', 'reward', 'penalties', 'experience', 'assets', 'monthly_attendance', 'finance', 'acrs'));
    }

    public function acr_list()
    {
        $page_title = 'ACR';
        return view('employees.acr_list', compact('page_title'));
    }

    public function acr_data()
    {
        $acr = DB::table('TBL_ACR acr')->select('acr.ACR_ID', 'emp.EMP_NAME', 'acr.DATE_FROM', 'acr.DATE_TO')->rightjoin('TBL_EMP emp', 'acr.EMP_ID', '=', 'emp.EMP_ID')->orderby('acr.DATE_FROM', 'ASC');
        return Datatables::of($acr)->make();
    }

    public function employee_reward()
    {
        $page_title = "Employee Reward";
        $reward_types = DB::table('TBL_REWARD_TYPE')->select('REWARD_TYPE_ID', 'RT_TITLE')->get();
        $reward_purpose = DB::table('TBL_REWARD_PURPOSE')->select('REWARD_PURPOSE_ID', 'RP_TITLE')->get();

        return view('employees.employee_reward', compact('page_title', 'reward_types', 'reward_purpose'));
    }

    public function empLeaves()
    {

        if (!Auth::user()->can('earned_leaves')) {
            abort(403);
        }

        $page_title = "Earned Leaves Detail";

        if (Auth::user()->hasRole('user')) {
            //$emp = DB::table('TBL_EMP')->where('EMP_ID', Auth::user()->emp_id)->first();
            $leaves = Leaves::where('EMP_ID', Auth::user()->emp_id)->get();
        } else {
            $leaves = Leaves::all();
        }

        //print_r($leaves);die;

        return view('employees.leave', compact('page_title', 'leaves'));

    }

    /*****Added by SH**/
    public function employeeLeavesDetail()
    {
        /*if(!Auth::user()->can('employees_detail'))
        abort(403);*/

        $emp = DB::table('TBL_EMP')->where('EMP_ID', Auth::user()->emp_id)->first();
        $leaves = Leaves::where('EMP_ID', 472)->get();

        if ($emp) {
            $page_title = $emp->emp_name;
        } else {
            Session::flash('error', 'No Record Found!!');
            $page_title = '';
        }
        /*echo "<pre>";
        print_r($leaves);
        echo "</pre>";
        die;*/

        return view('employees.employee_leaves_detail', compact('page_title', 'emp', 'leaves'));
    }

    /// Employee Attendance - Biometric
    public function empAttendance()
    {
        if (!Auth::user()->can('employee_attendance_info')) {
            abort(403);
        }

        $page_title = "Biometric/Attendance Info";

        $emp = DB::table('TBL_EMP')->where('EMP_ID', Auth::user()->emp_id)->first();
        $monthly_attendance = DB::connection('sqlsrv')->table('VIEWMONTHLYDATA')->where('EMPNO', '=', $emp->fp_id)->get();

        //print_r($monthly_attendance);die;
        return view('employees.employee_attendance_detail', compact('page_title', 'emp', 'monthly_attendance'));
    }
	///// Salary Slip PDF
	/// Salary Slip PDF
    public function pdfSalarySlip($cnic, $month, $year)
    {

        if (!Auth::user()->can('employee_salary_info')) {
            abort(403);
        }

        $page_title = "Pay Slip";

        // reference the Dompdf namespace

        // instantiate and use the dompdf class
        /*  $dompdf = new Dompdf();
        $dompdf->loadHtml('hello world');

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream();
        echo 'fff';die; */

        $emp = DB::table('TBL_EMP')->where('EMP_ID', Auth::user()->emp_id)->first();
        $conn = DB::connection('sqlsrv_fin');
        // Sp_PaySlip ("17301-8391053-1", 1, 2016)
        $finance = $conn->select('EXEC dbo.Sp_EmpPayments ?', array($emp->cnic));
        //print_r($finance);die;

        // Sp_PaySlip ("17301-8591053-1", 1, 2016)
        $conn = DB::connection('sqlsrv_fin');
        $result = $conn->select('EXEC dbo.Sp_PaySlip ?, ?, ?', array($cnic, $month, $year));
		//echo "<pre>";
       // print_r($result);die;

        // Send data to the view using loadView function of PDF facade
        $pdf = Dompdf::loadView('employees.salary_slip_pdf', compact('data', 'emp', 'result'));
        // If you want to store the generated pdf to the server then you can use the store function
        $pdf->save(storage_path() . '_filename.pdf');
        // Finally, you can download the file using download function
        return $pdf->download('salryslip.pdf');
    }

    /// Employee Salary - Pay Slip Info
    public function empSalary()
    {
        if (!Auth::user()->can('employee_salary_info')) {
            abort(403);
        }

        $page_title = "Pay Slip";

        $emp = DB::table('TBL_EMP')->where('EMP_ID', Auth::user()->emp_id)->first();
        $conn = DB::connection('sqlsrv_fin');
        // Sp_PaySlip ("17301-8391053-1", 1, 2016)
        $finance = $conn->select('EXEC dbo.Sp_EmpPayments ?', array($emp->cnic));
        //print_r($finance);die;

        return view('employees.employee_salary_detail', compact('page_title', 'emp', 'finance'));
    }

    // employee detail view
    public function detail()
    {

        $emp = DB::table('TBL_EMP')->where('EMP_ID', Auth::user()->emp_id)->first();
        //print_r($emp);die;
        $appointments = DB::table('V_APPOINTMENT')->select('APPOINTMENT_ID', 'POST_NAME', 'BS', 'DATE_OF_APPOINTMENT', 'TYPE_OF_APPOINTMENT', 'APPOINTMENT_QUOTA', 'SERVICE_TYPE', 'QUOTA_ADV_DATE', 'THROUGH_ADVER')->where('EMP_ID', $emp->emp_id)->orderBy('APPOINTMENT_ID', 'DESC')->get();
        $education = DB::table('TBL_EDUCATION')->where('EMP_ID', $emp->emp_id)->orderBy('sessions', 'DESC')->get();
        $career = DB::table('V_CARRIER')->where('EMP_ID', $emp->emp_id)->orderBy('BS', 'DESC')->orderBy('JOINING_DATE', 'DESC')->orderBy('RELIEVING_DATE', 'DESC')->get();
        $training = DB::table('TRAININGS')->where('EMP_ID', $emp->emp_id)->orderBy('start_date', 'DESC')->get();
        $reward = DB::table('V_REWARDS')->where('EMP_ID', $emp->emp_id)->orderBy('letter_date', 'DESC')->get();
        $penalties = DB::table('V_PENALTIE')->where('EMP_ID', $emp->emp_id)->orderBy('off_order_date', 'DESC')->get();
        $experience = DB::table('TBL_EXPERIENCE')->where('EMP_ID', $emp->emp_id)->orderBy('joining_date', 'DESC')->get();
        $assets = DB::table('TBL_ASSETS')->join('TBL_YEAR', 'TBL_ASSETS.YEAR_ID', '=', 'TBL_YEAR.YEAR_ID')->where('TBL_ASSETS.EMP_ID', $emp->emp_id)->orderBy('year_title', 'DESC')->get();
        $acrs = DB::table('V_EMP_ASSETS_ACRS')->where('EMP_ID', $emp->emp_id)->orderBy('YEAR_ID', 'DESC')->get();
        $promotion = DB::table('V_CARRIER')->where('charge_id', '>=', 100)->where('charge_id', '<', 200)->where('EMP_ID', $emp->emp_id)->orderBy('joining_date', 'DESC')->get();
        $family = DB::table('TBL_FAMILY')->where('EMP_ID', $emp->emp_id)->orderBy('FAMILY_ID', 'DESC')->get();

        $transfer = DB::table('V_CARRIER')->where('charge_id', '>=', 200)->where('charge_id', '<', 300)->where('EMP_ID', $emp->emp_id)->orderBy('joining_date', 'DESC')->get();
        $misc = DB::table('V_CARRIER')->where('charge_id', '>=', 300)->where('charge_id', '<', 400)->where('EMP_ID', $emp->emp_id)->orderBy('joining_date', 'DESC')->get();
        //print_r($emp);die;
        //$monthly_attendance = DB::connection('sqlsrv')->table('VIEWMONTHLYDATA')->where('EMPNO', '=', $emp->fp_id)->get();

        /*$conn = DB::connection('sqlsrv_fin');
        // Sp_PaySlip ("17301-8391053-1", 1, 2016)
        $finance = $conn->select('EXEC dbo.Sp_EmpPayments ?', array($emp->cnic));*/

        if ($emp) {
            $page_title = $emp->emp_name;
        } else {
            $page_title = '';
            Session::flash('error', 'No Record Found against given CNIC');
        }

        return view('employees.employee_detail', compact('page_title', 'appointments', 'promotion', 'family', 'transfer', 'misc', 'emp', 'education', 'career', 'training', 'reward', 'penalties', 'experience', 'assets', 'acrs'));
    }

    /// Biometric ///

    /// Salary /////

    // employee temporary removed
    public function employee_detail($id)
    {

        $emp = DB::table('TBL_EMP')->where('EMP_ID', $id)->first();
        $education = DB::table('TBL_EDUCATION')->where('EMP_ID', $emp->emp_id)->get();
        $career = DB::table('V_CARRIER')->where('EMP_ID', $emp->emp_id)->orderBy('BS', 'DESC')->orderBy('JOINING_DATE', 'DESC')->orderBy('RELIEVING_DATE', 'DESC')->get();
        $training = DB::table('TRAININGS')->where('EMP_ID', $emp->emp_id)->get();
        $reward = DB::table('V_REWARDS')->where('EMP_ID', $emp->emp_id)->get();
        $penalties = DB::table('V_PENALTIE')->where('EMP_ID', $emp->emp_id)->get();
        $experience = DB::table('TBL_EXPERIENCE')->where('EMP_ID', $emp->emp_id)->get();
        $assets = DB::table('TBL_ASSETS')->join('TBL_YEAR', 'TBL_ASSETS.YEAR_ID', '=', 'TBL_YEAR.YEAR_ID')->where('TBL_ASSETS.EMP_ID', $emp->emp_id)->get();
        //$monthly_attendance = DB::connection('sqlsrv')->table('VIEWMONTHLYDATA')->where('EMPNO', '=', '3829')->get();
        $monthly_attendance = DB::connection('sqlsrv')->table('VIEWMONTHLYDATA')->where('EMPNO', '=', $emp->fp_id)->get();

        $conn = DB::connection('sqlsrv_fin');
        // Sp_PaySlip ("17301-8391053-1", 1, 2016)
        $finance = $conn->select('EXEC dbo.Sp_EmpPayments ?', array($emp->cnic));

        if ($emp) {
            $page_title = $emp->emp_name;
        } else {
            Session::flash('error', 'No Record Found against given employee');
        }

        return view('employees.employee_detail', compact('page_title', 'emp', 'education', 'career', 'training', 'reward', 'penalties', 'experience', 'assets', 'monthly_attendance', 'finance'));
    }

    /*
     *
     *  Get Employee Attendance record
     *  @access public
     *  @param  id
     *  @return json
     *
     */
    public function get_attendance($emp_id)
    {
        //$monthly_attendance = DB::connection('sqlsrv')->table('VIEWMONTHLYDATA')->where('EMPNO', '=', '3829')->get();
        $emp = DB::table('TBL_EMP')->where('EMP_ID', $emp_id)->first();
        $monthly_attendance = DB::connection('sqlsrv')->table('VIEWMONTHLYDATA')->orderBy('Period', 'DESC')->where('AbDays', '>', 0)->where('EMPNO', '=', $emp->fp_id)->get();

        $data = $result = array();
        $month = array('name' => 'Absent');

        if (!$monthly_attendance) {
            return;
        }

        foreach ($monthly_attendance as $row) {

            $tmp = substr($row->Period, 0, strlen($row->Period) - 4) . '/01/' . substr($row->Period, -4);

            $month['data'][] = date('m', strtotime($tmp)) . '-' . date('y', strtotime($tmp));
            $data['data'][] = $row->AbDays;
        }

        array_push($result, $month['data']);
        array_push($result, array('data' => $data['data'], 'name' => 'Absent Days'));
        return json_encode($result, JSON_NUMERIC_CHECK);
    }

    public function ajax_get_payslip($c, $m, $y)
    {
        // Sp_PaySlip ("17301-8591053-1", 1, 2016)
        $conn = DB::connection('sqlsrv_fin');
        $result = $conn->select('EXEC dbo.Sp_PaySlip ?, ?, ?', array($c, $m, $y));

        return response()->json($result);
    }

    public function profile($id)
    {
        if (!Auth::user()->can('employees_detail')) {
            abort(403);
        }

        $user = User::where('EMP_ID', '=', $id)->first();
        $login_user = Employees::find($id);

        return view('employees.edit_profile', compact('user', 'login_user'));
    }

    public function save_profile(Request $request)
    {
        if (!Auth::user()->can('employees_detail')) {
            abort(403);
        }

        $validation = Validator::make($request->all(),
            [
                'password' => 'min:5',
                'password_confirmation' => 'same:password',
                'profile_picture' => 'mimes:jpeg,bmp,png,jpg|max:1000',
            ]);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $user = User::find($request->input('user_id'));
        $emp = Employees::find($user->emp_id);
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $imageName = $emp->fp_id . '.' . $file->getClientOriginalExtension();
            $request->file('profile_picture')->move(
                base_path() . '/storage/emp_pic/', $imageName
            );
        }

        $user->save();

        Session::flash('success', 'Profile updated successfully.');

        return Redirect::to('/employees/employees_list');
    }

    public function employee_current()
    {
        return view('employees.employee_current');
    }

    public function employee_current_data()
    {

        $emp = DB::table('V_EMP_CURRENT')->select(['EMP_NAME', 'F_H_NAME', 'DOB', 'GENDER', 'CNIC', 'RELIGION', 'HOME_DISTRICT', 'FP_ID', 'REF_FILE_NO', 'BS', 'JOINING_DATE', 'RELIEVING_DATE', 'STRENGTH_NAME', 'REMARKS', 'POST_NAME', 'ZONE_TITLE', 'REGION_NAME', 'PLACE_TITLE', 'PLACE_TYPE', 'WING_NAME', 'SECTION_NAME', 'WING_HEAD']);
        return Datatables::of($emp)
            ->editColumn('dob', '{{date("d-m-Y", strtotime($dob))}}')
            ->editColumn('joining_date', '{{date("d-m-Y", strtotime($joining_date))}}')
            ->editColumn('relieving_date', '{{date("d-m-Y", strtotime($relieving_date))}}')
            ->make();
    }
}
