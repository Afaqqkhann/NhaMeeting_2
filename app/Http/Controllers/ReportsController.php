<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Emp_Nomination;
use App\Http\Controllers\Controller;
use Auth;
use Session;
use DB;
use yajra\Datatables\Datatables;
use URL;

class ReportsController extends Controller
{
    public function __construct() {
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
    }

    //
    public function employee_report() {
        //if(!Auth::user()->can('employee_report'))
        //    abort(403);

        $page_title = 'Employees Report';
        $data = DB::table('V_EMP')->select(['FP_ID', 'EMP_ID', 'EMP_NAME', 'F_H_NAME', 'DOB','GENDER', 'CNIC', 'DOMICILE','JOB_STATUS','MOBILE_NO','RELIGION',
            'MAILING_ADDRESS','PERMENENT_ADDRESS','HOME_DISTRICT','CADRE_NAME','CURRENT_POST','CURRENT_BS','DATE_OF_APPOINTMENT',
             'SERVICE_TYPE','TYPE_OF_APPOINTMENT','THROUGH_ADVER',"ACR_TOTAL",
            "ACR_SUBMITTIED",
            "ACR_REMAINING",
            "ASSET_TOTAL",
            "ASSET_SUBMITTIED",
            "ASSET_REMAINING",
            "TOTAL_POSTING",
            "TOTAL_TRANSFER",
            "TOTAL_REWARD",
            "TOTAL_DEPENDENTS",
            "TOTAL_PANELTIES",
            "TOTAL_TRAINING",])
            ->get();

        return view('reports.employee_report_list', compact('page_title', 'data'));
    }

    public function employee_report_data(){
        $emp = DB::table('V_EMP')->select(['FP_ID', 'EMP_ID', 'EMP_NAME', 'F_H_NAME', 'DOB','GENDER', 'CNIC', 'DOMICILE','JOB_STATUS','MOBILE_NO','RELIGION',
            'MAILING_ADDRESS','PERMENENT_ADDRESS','HOME_DISTRICT','CADRE_NAME','CURRENT_POST','CURRENT_BS','DATE_OF_APPOINTMENT',
            'SERVICE_TYPE','TYPE_OF_APPOINTMENT','THROUGH_ADVER',"ACR_TOTAL",
            "ACR_SUBMITTIED",
            "ACR_REMAINING",
            "ASSET_TOTAL",
            "ASSET_SUBMITTIED",
            "ASSET_REMAINING",
            "TOTAL_POSTING",
            "TOTAL_TRANSFER",
            "TOTAL_REWARD",
            "TOTAL_DEPENDENTS",
            "TOTAL_PANELTIES",
            "TOTAL_TRAINING",]);

        return Datatables::of($emp)
            ->addColumn('action', function ($emp) {
                $profile_view = (Auth::user()->hasRole('admin')) ? '<a href="'.URL::to('/employee/profile').'/'.$emp->emp_id.'" class="btn btn-xs btn-default"><i class=" fa fa-picture-o"></i> Profile</a>' : '';
                return '<a href="'.URL::to('/employee/icp_chart').'/'.$emp->emp_id.'" class="btn btn-xs btn-primary"><i class="fa fa-file-text-o"></i>  ICP Chart</a>'.$profile_view;
            })
            /*->addColumn('profile_picture', function($emp) {
                return '<img src="'.URL::to('/storage/emp_pic').'/'.$emp->fp_id.'" class="img">';
            })*/
            ->editColumn('fp_id', function($emp) {
                if(file_exists('storage/emp_pic/'.$emp->fp_id.'.jpg'))
                    return '<img src="'.URL::to('/storage/emp_pic').'/'.$emp->fp_id.'.jpg" style="width:60px;" class="img-responsive">';
                else
                    return '<img src="'.URL::to('/storage/emp_pic').'/default.png" style="width:60px;" class="img-responsive">';
            })
            ->make();
    }

    public function training_foreign() {
        $page_title = 'Training Foreign';

        $data = DB::table('V_TRAINING_FOREIGN')
            ->select(
                [
                    "EMP_ID",
                    "EMP_NAME",
                    "F_H_NAME",
                    "DOB",
                    "GENDER",
                    "CNIC",
                    "RELIGION",
                    "HOME_DISTRICT",
                    "FP_ID",
                    "REF_FILE_NO",
                    "DESIGNATION",
                    "DESIG_BS",
                    "P_CHARGE_ID",
                    "CARRIER_ID",
                    "SANCTION_ID",
                    "BS",
                    "STATION_STATUS",
                    "POSTING_STATUS",
                    "JOINING_DATE",
                    "RELIEVING_DATE",
                    "ORDER_ID",
                    "REPORTING_OFF_ID",
                    "STRENGTH_NAME",
                    "REMARKS",
                    "CARRIER_STATUS",
                    "SECTION_ID",
                    "POST_NAME",
                    "ZONE_TITLE",
                    "REGION_NAME",
                    "PLACE_TITLE",
                    "PLACE_TYPE",
                    "WING_NAME",
                    "SECTION_NAME",
                    "WING_HEAD",
                    "TENURE_INFO",
                    "TENURE_YEAR"
                ])->get();

        return view('reports.training_foreign', compact('page_title', 'data'));
    }
	
	  public function regist_submit($id){
        $data = Emp_Nomination::where('emp_id', '=',$id )->first();
        return view('reports.register_submit', compact( 'data'));
    }
	public function regist_submit_store(Request $request){


    $user = Emp_Nomination::where('emp_id', '=', $request->input('emp_id'))->first();
    if ($request->hasFile('e_doc')) {
        $file = $request->file('e_doc');
        $new_filename = 'regist' . $request->input('e_doc');
        $path = 'public/Nomination';
        $path = str_replace('&', '_', $path);
        $extension = $file->getClientOriginalExtension();
        $file->move($path, $new_filename . '.' . $extension);
        $completeUrl = $path . '/' . $new_filename . '.' . $extension;
        $user->t_edoc = $completeUrl;
    }
    $departmentArray = array(
        'emp_id' => $request->input('emp_id'),
        'tn_edoc' => $user->t_edoc,
        'status' =>0,
    );

   // echo "<pre>";print_r($departmentArray);die;

    DB::table('TBL_TRAINING_NOMINATION')->where('emp_id', '=', $request->input('emp_id'))->update($departmentArray);

    Session::flash('success', 'Image updated successfully.');

    return Redirect('report/training_done');
}
	
	
	
	
	
	
	
	
	
	
    public function view($id){
      //  echo $id; die;
        return view('reports.view',compact('id'));
    }

    public function training_done() {
        $page_title = 'Training Done';
//echo"test";
        $id = 1;
        if (Auth::user()->hasrole('superadmin'))
			$data = DB::table('V_TRAINING_DONE')->get();
        elseif (Auth::user()->hasrole('hrtc_role'))
            $data = DB::table('V_TRAINING_DONE')->where('TRAINING_ID', '=', 16)->get();
		elseif (Auth::user()->hasrole('HRD_TRAINING'))
			$data = DB::table('V_TRAINING_DONE')->where('TRAINING_ID', '=', 16)->get();
        else        
			$data = DB::table('V_TRAINING_DONE')->where('TRAINING_ID', '=', 16)->where('EMP_ID', '=', Auth::user()->emp_id)->get();
        return view('reports.training_done', compact('page_title', 'data', 'id'));
    }
	 public function training_done1($id) {
        //echo $id; die;
        $page_title = 'Training Done';
        if (Auth::user()->hasrole('superadmin'))
            $data = DB::table('V_TRAINING_DONE')->get();
        elseif (Auth::user()->hasrole('hrtc_role'))
            $data = DB::table('V_TRAINING_DONE')->where('TRAINING_ID', '=', 16)->get();
        else
            //echo Auth::id();die;
            $data = DB::table('V_TRAINING_DONE')->where('TRAINING_ID', '=', 16)->where('EMP_ID', '=', Auth::user()->emp_id)->get();
        return view('reports.training_done', compact('page_title','data', 'id'));
    }
	
	
	

    public function training_foreign_data() {
        ini_set('max_execution_time', 300);
        $data = DB::table('V_TRAINING_FOREIGN')
            ->select(
                [
                    "EMP_ID",
                    "EMP_NAME",
                    "F_H_NAME",
                    "DOB",
                    "GENDER",
                    "CNIC",
                    "RELIGION",
                    "HOME_DISTRICT",
                    "FP_ID",
                    "REF_FILE_NO",
                    "DESIGNATION",
                    "DESIG_BS",
                    "P_CHARGE_ID",
                    "CARRIER_ID",
                    "SANCTION_ID",
                    "BS",
                    "STATION_STATUS",
                    "POSTING_STATUS",
                    "JOINING_DATE",
                    "RELIEVING_DATE",
                    "ORDER_ID",
                    "REPORTING_OFF_ID",
                    "STRENGTH_NAME",
                    "REMARKS",
                    "CARRIER_STATUS",
                    "SECTION_ID",
                    "POST_NAME",
                    "ZONE_TITLE",
                    "REGION_NAME",
                    "PLACE_TITLE",
                    "PLACE_TYPE",
                    "WING_NAME",
                    "SECTION_NAME",
                    "WING_HEAD",
                    "TENURE_INFO",
                    "TENURE_YEAR"
                ]);

        return Datatables::of($data)
            /*->addColumn('action', function ($data) {
                $profile_view = (Auth::user()->hasRole('admin')) ? '<a href="'.URL::to('/employee/profile').'/'.$emp->emp_id.'" class="btn btn-xs btn-default"><i class=" fa fa-picture-o"></i> Profile</a>' : '';
                return '<a href="'.URL::to('/employee/icp_chart').'/'.$emp->emp_id.'" class="btn btn-xs btn-primary"><i class="fa fa-file-text-o"></i>  ICP Chart</a>'.$profile_view;
            })*/
            /*->addColumn('profile_picture', function($emp) {
                return '<img src="'.URL::to('/storage/emp_pic').'/'.$emp->fp_id.'" class="img">';
            })*/
           /* ->editColumn('fp_id', function($emp) {
                if(file_exists('storage/emp_pic/'.$emp->fp_id.'.jpg'))
                    return '<img src="'.URL::to('/storage/emp_pic').'/'.$emp->fp_id.'.jpg" style="width:60px;" class="img-responsive">';
                else
                    return '<img src="'.URL::to('/storage/emp_pic').'/default.png" style="width:60px;" class="img-responsive">';
            })*/
            ->make();
    }

    public function training_local() {
        //DB::setDateFormat('DD/MM/YY');
        $page_title = 'Training Local';

        $data = DB::table('V_TRAINING_LOCAL')
            ->select(
                [
                    "EMP_ID",
                    "EMP_NAME",
                    "F_H_NAME",
                    "DOB",
                    "GENDER",
                    "CNIC",
                    "RELIGION",
                    "HOME_DISTRICT",
                    "FP_ID",
                    "REF_FILE_NO",
                    "DESIGNATION",
                    "DESIG_BS",
                    "P_CHARGE_ID",
                    "CARRIER_ID",
                    "SANCTION_ID",
                    "BS",
                    "STATION_STATUS",
                    "POSTING_STATUS",
                    "JOINING_DATE",
                    "RELIEVING_DATE",
                    "ORDER_ID",
                    "REPORTING_OFF_ID",
                    "STRENGTH_NAME",
                    "REMARKS",
                    "CARRIER_STATUS",
                    "SECTION_ID",
                    "POST_NAME",
                    "ZONE_TITLE",
                    "REGION_NAME",
                    "PLACE_TITLE",
                    "PLACE_TYPE",
                    "WING_NAME",
                    "SECTION_NAME",
                    "WING_HEAD",
                    "TENURE_INFO",
                    "TENURE_YEAR"
                ])->get();

        return view('reports.training_local', compact('page_title', 'data'));
    }

    ////////// Penalties
    public function employee_penalty() {
        //DB::setDateFormat('DD/MM/YY');
        $page_title = 'Penalties';

        $data = DB::table('V_PENALTIE')
            ->select(
                [
                    "EMP_ID",
                    "EMP_NAME",
                    "F_H_NAME",
                    "DOB",
                    "GENDER",
                    "CNIC",
                    "DOMICILE",
                    "RELIGION",
                    "HOME_DISTRICT",
                    "CURRENT_POST",
                    "CURRENT_BS",
                   /* "PLACE_OF_POSTING",*/
                    "JOB_STATUS",
                    "ALLEGATION",
                    "NATURE_OF_PENALTY",
                    "OFF_ORDER_NO",
                    "OFF_ORDER_DATE",
                    "APP_AUTH",
                ])->get();

        return view('reports.penalty', compact('page_title', 'data'));
    }
    //////////////// End

    ////////// Case Report
    public function employee_case() {
        //DB::setDateFormat('DD/MM/YY');
        $page_title = 'Case';

        $data = DB::table('V_CASE')
            ->select(
                [
                    "EMP_ID",
                    "EMP_NAME",
                    "F_H_NAME",
                    "DOB",
                    "GENDER",
                    "CNIC",
                    "DOMICILE",
                    "RELIGION",
                    "HOME_DISTRICT",
                    "CURRENT_POST",
                    "CURRENT_BS",
                    "JOB_STATUS",
                    "CASE_TITLE",
                    "CASE_ID",
                    "FILE_NO",
                    "CASE_TYPE",
                    "CASE_RECIEVE_FROM",
                    "CASE_RECIEVE_INFO",
                    "CASE_RECIEVE_DATE",
                    "CASE_NATURE",
                    "CASE_CATEGORY",
                    "AMOUNT_INVOLVED",
                    "CASE_INITIATE_DATE",
                    "CASE_SUBMITTIED_DATE",
                    "ACTION",
                    "CASE_COMMENTS",
                    "CASE_EDOC",
                    "CASE_STATUS",
                    "CE_ID",
                ])->get();

        return view('reports.case', compact('page_title', 'data'));
    }
    //////////////// End

    ////////// NOC Report
    public function employee_noc() {
        //DB::setDateFormat('DD/MM/YY');
        $page_title = 'NOC';

        $data = DB::table('V_NOC')
            ->select(
                [
                    "EMP_ID",
                    "EMP_NAME",
                    "F_H_NAME",
                    "DOB",
                    "GENDER",
                    "CNIC",
                    "DOMICILE",
                    "RELIGION",
                    "HOME_DISTRICT",
                    "CURRENT_POST",
                    "CURRENT_BS",
                    "JOB_STATUS",
                    "NOC_ID",
                    "NOC_TYPE",
                    "APPROVAL_DATE",
                    "APPLICATION_DATE",
                    "ISSUE_DATE",
                    "ORDER_ID",
                    "NOC_FILE_NO",
                    "NOC_STATUS",
                    "NOC_EDOC",
                ])->get();

        return view('reports.noc', compact('page_title', 'data'));
    }
    //////////////// End

    public function employee_rewards() {
        //DB::setDateFormat('DD/MM/YY');
        $page_title = 'Employee Rewards';

        $data = DB::table('V_REWARD_EMP')
            ->select(
                [
                    "EMP_ID",
                    "EMP_NAME",
                    "F_H_NAME",
                    "DOB",
                    "GENDER",
                    "CNIC",
                    "DOMICILE",
                    "RELIGION",
                    "HOME_DISTRICT",
                    "CURRENT_POST",
                    "CURRENT_BS",
                    "JOB_STATUS",
                    "KIND_OF_REWARD",
                    "PURPOSE",
                    "LETTER_DATE",
                    "APP_AUTH",
                    "LETTER_NO"
                ])->get();

        return view('reports.employee_reward', compact('page_title', 'data'));
    }

    public function posting_history() {
        //DB::setDateFormat('DD/MM/YY');
        ini_set('max_execution_time', 50000);
        ini_set('memory_limit', '50000M');
        $page_title = 'Posting History';

        $data = DB::table('V_POSTING_HISTORY')
            ->select(
                [
                    "EMP_ID",
                    "EMP_NAME",
                    "F_H_NAME",
                    "CURRENT_POST",
                    "CURRENT_BS",
                    "DOB",
                    "GENDER",
                    "CNIC",
                    "DOMICILE",
                    "JOB_STATUS",
                    "RELIGION",
                    "HOME_DISTRICT",
                    "STATION_STATUS",
                    "POSTING_STATUS",
                    "JOINING_DATE",
                    "RELIEVING_DATE",
                    "REMARKS",
                    "POST_NAME",
                    "PLACE_TYPE",
                    "PLACE_TITLE",
                    "REGION_NAME",
                    "ZONE_TITLE",
                    "WING_NAME",
                    "WING_HEAD",
                    "SECTION_NAME",
                    "CHARGE_TITLE",
                    "POSTING_TENURE",
                    "SERVICE_TYPE",
                ])->orderBy('EMP_ID','ASC')->get(); //->take(20000)

        return view('reports.posting_history', compact('page_title', 'data'));
    }

    public function emp_profile() {
        $page_title = 'Employee Profile';

        $data = DB::table('V_EMP_PROFILE')
            ->select(
                [
                    "EMP_ID",
                    "EMP_NAME",
                    "F_H_NAME",
                    "CURRENT_DESIGNATION",
                    "CURRENT_BS",
                    "JOB_STATUS",
                    "DOB",
                    "GENDER",
                    "CNIC",
                    "DOMICLE",
                    "HOME_DISTRICT",
                    "RELIGION",
                    "MARITAL_STATUS",
                    "REF_FILE_NO",
                    "FP_ID",
                    "SERVICE_TYPE",
                    "WORKING_POST",
                    "WORKING_BS",
                    "STATION_STATUS",
                    "POSTING_STATUS",
                    "JOINING_DATE",
                    "RELIEVING_DATE",
                    "POSTING_DATE",
                    "POSTING_TENURE",
                    "TRANSFER_DATE",
                    "TRANSFER_TENURE",
                    "ORDER_ID",
                    "REPORTING_OFFICER",
                    "REMARKS",
                    "ZONE_TITLE",
                    "REGION_NAME",
                    "PLACE_TITLE",
                    "PLACE_TYPE",
                    "PACKAGE_NAME",
                    "WING_NAME",
                    "SECTION_NAME",
                    "WING_HEAD",
                    "CHARGE_INFO",
                    "CARRIER_ID",
                    "SANCTION_ID",
                    "ACR_TOTAL",
                    "ACR_SUBMITTIED",
                    "ACR_REMAINING",
                    "ASSET_TOTAL",
                    "ASSET_SUBMITTIED",
                    "ASSET_REMAINING",
                    "TOTAL_CONTRACT_EXT",
                    "TOTAL_EXT_DURATION",
                    "TOTAL_REWARD",
                    "TOTAL_DEPENDENTS",
                    "TOTAL_PANELTIES",
                    "TOTAL_POSTING",
                    "TOTAL_TRANSFER",
                    "TOTAL_TRAINING",
					"HIGHEST_QUALIFICATION",
                ])->get();

        return view('reports.employee_profile', compact('page_title', 'data'));
    }

    public function emp_profile_data() {
        ini_set('max_execution_time', 300);
        $data = DB::table('V_EMP_PROFILE')
            ->select(
                [
                    "EMP_ID",
					"EMP_NAME",
					"F_H_NAME",
					"CURRENT_DESIGNATION",
					"CURRENT_BS",
                    "JOB_STATUS",
					"DOB",
					"GENDER",
					"CNIC",
					"DOMICLE",
					"HOME_DISTRICT",
					"RELIGION",
					"MARITAL_STATUS",
					"DUAL_NATIONALITY",
					"HIGHEST_QUALIFICATION",
					"REF_FILE_NO",
					"OFF_PHONE_NO",
					"OFF_EXT_NO",
					"RES_PHONE_NO",
					"MOBILE_NO",
					"PERMENENT_ADDRESS",
					"FP_ID",
					"APPOINTMENT_POST",
					"APPOINTMENT_BS",
					"DATE_OF_APPOINTMENT",
					"TYPE_OF_APPOINTMENT",
					"APPOINTMENT_QUOTA",
					"SERVICE_TYPE",
					"THROUGH_ADVER",
					"TOTAL_CONTRACT_EXT",
					"TOTAL_EXT_DURATION",
					"WORKING_POST",
					"WORKING_BS",
					"STATION_STATUS",
					"POSTING_STATUS",
					"JOINING_DATE",
					"RELIEVING_DATE",
					"POSTING_DATE",
					"POSTING_TENURE",
					"TRANSFER_DATE",
					"TRANSFER_TENURE",
					"TOTAL_POSTING",
					"TOTAL_TRANSFER",
					"ORDER_ID",
					"REPORTING_OFF_ID",
					"REPORTING_OFFICER",
					"REMARKS",
					"CARRIER_STATUS",
					"SECTION_ID",
					"ZONE_TITLE",
					"REGION_NAME",
					"PLACE_TITLE",
					"PLACE_TYPE",
					"PACKAGE_ID",
					"PACKAGE_NAME",
					"WING_NAME",
					"SECTION_NAME",
					"WING_HEAD",
					"P_CHARGE_ID",
					"CHARGE_INFO",
					"CARRIER_ID",
					"SANCTION_ID",
					"ACR_TOTAL",
					"ACR_SUBMITTIED",
					"ACR_REMAINING",
					"ASSET_TOTAL",
					"ASSET_SUBMITTIED",
					"ASSET_REMAINING",
					"TOTAL_REWARD",
					"TOTAL_DEPENDENTS",
					"TOTAL_PANELTIES",
					"TOTAL_TRAINING",
                ]);

        return Datatables::of($data)
            /*->addColumn('action', function ($data) {
                $profile_view = (Auth::user()->hasRole('admin')) ? '<a href="'.URL::to('/employee/profile').'/'.$emp->emp_id.'" class="btn btn-xs btn-default"><i class=" fa fa-picture-o"></i> Profile</a>' : '';
                return '<a href="'.URL::to('/employee/icp_chart').'/'.$emp->emp_id.'" class="btn btn-xs btn-primary"><i class="fa fa-file-text-o"></i>  ICP Chart</a>'.$profile_view;
            })*/
            /*->addColumn('profile_picture', function($emp) {
                return '<img src="'.URL::to('/storage/emp_pic').'/'.$emp->fp_id.'" class="img">';
            })*/
           /* ->editColumn('fp_id', function($emp) {
                if(file_exists('storage/emp_pic/'.$emp->fp_id.'.jpg'))
                    return '<img src="'.URL::to('/storage/emp_pic').'/'.$emp->fp_id.'.jpg" style="width:60px;" class="img-responsive">';
                else
                    return '<img src="'.URL::to('/storage/emp_pic').'/default.png" style="width:60px;" class="img-responsive">';
            })*/
            ->make();
    }

    public function acr() {
        $page_title = 'ACR Report';

        $data = DB::table('V_ACR')
            ->select(
                [
                    "EMP_ID",
                    "EMP_NAME",
                    "F_H_NAME",
                    "DOB",
                    "GENDER",
                    "CNIC",
                    "DOMICILE",
                    "RELIGION",
                    "HOME_DISTRICT",
                    "CURRENT_POST",
                    "CURRENT_BS",
                    "ACR_ID",
                    "DATE_FROM",
                    "DATE_TO",
                    "RO_NAME",
                    "CO_NAME",
                    "ACR_EDOC",
                    "YEAR_ID",
                    "JOB_STATUS",
                    "ACR_STATUS",
                    "YEAR_TITLE"
                ])->get();

        return view('reports.acr', compact('page_title', 'data'));
    }

    public function assets() {
        $page_title = 'Assets Report';

        $data = DB::table('V_ASSETS')->get();
		//echo "<pre>";
		//print_r($data);die;
            /*->select(
                [
                    "EMP_ID",
                    "EMP_NAME",
                    "F_H_NAME",
                    "DOB",
                    "GENDER",
                    "CNIC",
                    "DOMICILE",
                    "RELIGION",
                    "HOME_DISTRICT",
                    "CURRENT_POST",
                    "CURRENT_BS",
                    "PLACE_OF_POSTING",
                    "DATE_OF_RECEIPT",
                    "ASSETS_STATUS",
                    "YEAR_TITLE"
                ])->get();*/

        return view('reports.assets', compact('page_title', 'data'));
    }

}
