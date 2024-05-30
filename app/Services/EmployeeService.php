<?php

namespace App\Services;

use App\Models\Appoint;
use App\Models\Emp;
use App\Models\Employees\Employees;
use App\Models\VEmp;
use DB;

class EmployeeService
{
    public function __construct()
    {
    }
    public function empEducationInfo($empId)
    {
        return DB::table('TBL_EDUCATION')->where('EMP_ID', $empId)->orderBy('sessions', 'DESC')->get();
    }
    public function empAppointmentInfo($empId)
    {
        return DB::table('TBL_APPOINTMENT')->select('APPOINTMENT_ID', 'POST_NAME', 'BS', 'DATE_OF_APPOINTMENT', 'TYPE_OF_APPOINTMENT', 'APPOINTMENT_QUOTA', 'SERVICE_TYPE', 'QUOTA_ADV_DATE', 'THROUGH_ADVER', 'EXAM_HELD', 'MERIT_NO')->where('EMP_ID', $empId)->orderBy('APPOINTMENT_ID', 'DESC')->get();
    }
    public function empInitialBS($empId)
    {
        return Appoint::where('emp_id', $empId)->orderBy('appointment_id', 'ASC')->first(['BS']);
    }
    public function empTransferInfo($empId)
    {
        return DB::table('V_CARRIER')->where('charge_id', '>=', 200)->where('charge_id', '<', 400)->where('EMP_ID', $empId)->orderBy('carrier_status', 'DESC')->orderBy('joining_date', 'DESC')->orderBy('BS', 'DESC')->get();
    }

    public function empPersonalInfo($empId)
    {
        return VEmp::select(
            'EMP_ID',
            'EMP_NAME',
            'F_H_NAME',
            'DOB',
            'AGE_YEAR',
            'TAX_FILLER',
            'DOR',
            'TT_EL',
            'TT_ACC_EL',
            'GENDER',
            'CNIC',
            'HIGHIEST_QUALIFICATION',
            'DOMICILE',
            'DESIGNATION',
            'MOBILE_NO',
            'RELIGION',
            'PERMENENT_ADDRESS',
            'MAILING_ADDRESS',
            'HOME_DISTRICT',
            'CADRE_NAME',
            'CURRENT_POST',
            'CURRENT_BS',
            'PLACE_OF_POSTING',
            'VI_MARK',
            'MARITAL_STATUS',
            'REF_FILE_NO',
            'APPOINTMENT_ID',
            'FP_ID',
            'BS',
            'SANCTION_ID',
            'DATE_OF_APPOINTMENT',
            'TYPE_OF_APPOINTMENT',
            'THROUGH_ADVER',
            'SERVICE_TYPE',
            'POSTING_DATE',
            'POSTING_TENURE',
            'TRANSFER_DATE',
            'TRANSFER_TENURE',
            'TOTAL_POSTING',
            'TOTAL_TRANSFER',
            'ACR_TOTAL',
            'ACR_SUBMITTIED',
            'ACR_REMAINING',
            'ASSET_TOTAL',
            'ASSET_SUBMITTIED',
            'ASSET_REMAINING',
            'TOTAL_REWARD',
            'TOTAL_DEPENDENTS',
            'TOTAL_PANELTIES',
            'TOTAL_TRAINING',
            'JOB_STATUS',
            'EMP_PENSION_STATUS',
            'DATE_OF_JOINING',
            'DUAL_NATIONALITY'
        )->where('EMP_ID', $empId)->first();
    }

    public function updateEmployeeInfo($emp_id)
    {
        /* echo $emp_id;
        die; */
        Employees::where('emp_id', $emp_id)->update([
            'DESIGNATION' => DB::raw('CURRENT_POST(' . $emp_id . ', 1)'),
            'BS' => DB::raw('CURRENT_POST(' . $emp_id . ', 2)'),
            'WORKING_AS_DESIG' => DB::raw('WORKING_AS_DESG(' . $emp_id . ')'),
            'WORKING_AS_BS' => DB::raw('WORKING_AS_BS(' . $emp_id . ')'),
            'PLACE_ID' => DB::raw('WORKING_AS_CARRIER_ID(' . $emp_id . ', 1)'),
            'PLACE_OF_POSTING' => DB::raw('WORKING_AS_CARRIER_ID(' . $emp_id . ', 2)'),
            'REGION_ID' => DB::raw('WORKING_AS_CARRIER_ID(' . $emp_id . ', 3)'),
            'REGION' => DB::raw('WORKING_AS_CARRIER_ID(' . $emp_id . ', 4)'),
            'ZONE_ID' => DB::raw('WORKING_AS_CARRIER_ID(' . $emp_id . ', 5)'),
            'ZONE' => DB::raw('WORKING_AS_CARRIER_ID(' . $emp_id . ', 6)'),
            'SECTION_ID' => DB::raw('WORKING_AS_CARRIER_ID(' . $emp_id . ', 7)'),
            'SECTION' => DB::raw('WORKING_AS_CARRIER_ID(' . $emp_id . ', 8)'),
            'WING_ID' => DB::raw('WORKING_AS_CARRIER_ID(' . $emp_id . ', 9)'),
            'WING' => DB::raw('WORKING_AS_CARRIER_ID(' . $emp_id . ', 10)')
        ]);
    }
}
