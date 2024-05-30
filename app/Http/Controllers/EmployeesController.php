<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ACR;
use App\Models\Employees\Employees;
use App\Models\Finance\EmpAdvances;
use App\Models\Leaves;
use App\Models\VEmp;
use App\Role;
use App\User;
use Auth;
use Datatables;
//use Dompdf\Dompdf;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Redirect;
use Session;
use URL;
use Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\DependentList;
use App\Models\Finance\AdvAccounts;
use App\Services\EmployeeService;

class EmployeesController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $page_title = "Add Employee";

        $genders = ['' => 'Select Gender', 'Male' => 'Male', 'Female' => 'Female'];
        $maritals = ['' => 'Select Marital Status', 'Married' => 'Married', 'Un-Married' => 'Un-Married', 'Widow' => 'Widow'];
        $taxFilers = ['' => 'Select Tax Filer', 'Yes' => 'Yes', 'No' => 'No'];

        $docimile_verifications = ['' => 'Select Domicile Verification', 'Yes' => 'Yes', 'No' => 'No', 'Awaited' => 'Awaited'];
        $educational_verifications = ['' => 'Select Educational Verification', 'Yes' => 'Yes', 'No' => 'No', 'Awaited' => 'Awaited'];
        $taxFilers = ['' => 'Select Tax Filer', 'Yes' => 'Yes', 'No' => 'No'];
        $hajjPerform = ['No' => 'No', 'Yes' => 'Yes'];

        $religions = ['' => 'Select Religion'];
        $districts = ['' => 'Select District'];
        $domiciles = ['' => 'Select Domicile'];

        $regs = Employees::select('religion')->distinct()->get();
        if (!empty($regs)) {
            foreach ($regs as $reg) {
                $religions[$reg->religion] = $reg->religion;
            }
        }

        $domcs = Employees::select('domicile')->distinct()->get();
        if (!empty($domcs)) {
            foreach ($domcs as $dom) {
                $domiciles[$dom->domicile] = $dom->domicile;
            }
        }

        $distrcts = Employees::select('home_district')->distinct()->get();
        if (!empty($distrcts)) {
            foreach ($distrcts as $dist) {
                $districts[$dist->home_district] = $dist->home_district;
            }
        }

        $countries = [
            '' => 'Select Country', 'United States of America' => 'United States of America', 'United Kingdom' => 'United Kingdom', 'Canada' => 'Canada', 'Italy' => 'Italy', 'Germany' => 'Germany', 'France' => 'France'
        ];

        return view('employees.create', compact('page_title', 'hajjPerform', 'educational_verifications', 'countries', 'docimile_verifications', 'taxFilers', 'genders', 'maritals', 'religions', 'districts', 'domiciles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $messages = [
            'emp_name.required' => 'The Employee Name field is required.',
            'f_h_name.required' => 'The Father/Husband field is required.',
            'dob.required' => 'The Date of Birth field is required.',
            'gender.required' => 'The Gender field is required.',
            'cnic.required' => 'The CNIC field is required.',
            'cnic.unique' => 'The CNIC filed is already exist.',
            'remarks.required' => 'The Remarks field is required.',
            'edoc.mimes' => 'The Dependent List Edoc must be in pdf format.',
            // 'picture.required' => 'The Picture field is required.',

        ];
        $validation = Validator::make(
            $request->all(),
            [
                'emp_name' => 'required',
                'f_h_name' => 'required',
                'dob' => 'required',
                'gender' => 'required',
                'cnic' => 'required|unique:tbl_emp',
                'edoc' => 'mimes:pdf',


            ],
            $messages
        );

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $latest = Employees::orderby('emp_id', 'desc')->first();
        $new_id = ($latest) ? $latest->emp_id + 1 : 1;

        $record = new Employees();
        $record->emp_id = $new_id;
        $record->fp_id = request('fp_id');
        $record->emp_name = request('emp_name');
        $record->f_h_name = request('f_h_name');
        $record->dob = ($request->input('dob')) ? date('Y-m-d', strtotime($request->input('dob'))) : '';
        $record->gender = request('gender');
        $record->cnic = request('cnic');
        $record->domicile = request('domicile');
        $record->home_district = request('home_district');
        $record->vi_mark = request('vi_mark');
        $record->religion = request('religion');
        $record->languages = request('languages');
        $record->marital_status = request('marital_status');
        $record->mobile_no = request('mobile_no');
        $record->off_phone_no = request('off_phone_no');
        $record->off_ext_no = request('off_ext_no');
        $record->fax_no = request('fax_no');
        $record->res_phone_no = request('res_phone_no');
        $record->emrgcy_contact_name = request('emrgcy_contact_name');
        $record->emrgcy_contact_phoneno = request('emrgcy_contact_phoneno');
        $record->emrgcy_contact_address = request('emrgcy_contact_address');
        $record->tax_filler = request('tax_filler');
        $record->mailing_address = request('mailing_address');
        $record->permenent_address = request('permenent_address');
        $record->ref_file_no = request('ref_file_no');
        $record->verified_on = ($request->input('verified_on')) ? date('Y-m-d', strtotime($request->input('verified_on'))) : '';
        $record->seniority_position = request('seniority_position');
        $record->pers_no = request('pers_no');
        $record->dual_nationality = request('dual_nationality');
        $record->blood_group = request('blood_group');
        $record->domicile_verification = request('domicile_verification');
        $record->educational_verification = request('educational_verification');
        $record->hajj_perform = request('hajj_perform');

        if ($record->save()) {
            $last_emp = Employees::orderby('emp_id', 'desc')->first();
            /// Store image
            if ($last_emp->fp_id && $request->hasFile('picture')) {
                $image = $request->file('picture');
                $name = $last_emp->fp_id . '.jpg';
                $destinationPath = base_path() . '\storage\emp_pic';
                $image->move($destinationPath, $name);
            }
            /// Add Employee as System User in USERS TABLE
            $role = Role::find(22); // user role id - 22

            $last_user = User::orderby('id', 'desc')->first();
            $new_user_id = ($last_user) ? $last_user->id + 1 : 1;
            $user = new User();
            $user->id = $new_user_id;
            $user->name = request('emp_name');
            $user->username = request('cnic');
            $user->password = Hash::make('demo1234');
            $user->p_read = 'demo1234';
            $user->emp_id = $last_emp->emp_id;

            if ($user->save()) {
                $user->attachRole($role);
            }
        }

        if ($request->hasFile('edoc')) {
            $latDependentList = DependentList::orderBy('dl_id', 'desc')->first();
            $lat_dl_id = ($latDependentList) ? $latDependentList->dl_id + 1 : 1;
            $dependentList = new DependentList();
            $dependentList->dl_id = $lat_dl_id;
            $dependentList->emp_id = $record->emp_id;

            $file = $request->file('edoc');
            $new_filename = 'dependent_list_' . $record->emp_id . '_' . $lat_dl_id;
            $path = 'storage/emp_pic/dependentList_' . $record->emp_id;
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $dependentList->edoc = $completeUrl;



            $dependentList->edoc_date = date('Y-m-d');
            $dependentList->save();
            $employee = Employees::where('emp_id', '=', $record->emp_id)->first();
            $employee->dependent_list_dated = date('Y-m-d');
            $employee->save();
        }

        Session::flash('success', 'Employe has been added successfully.');
        return redirect('employees');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $page_title = "Edit Employee";

        $data = Employees::find($id);

        $genders = ['' => 'Select Gender', 'Male' => 'Male', 'Female' => 'Female'];
        $maritals = ['' => 'Select Marital Status', 'Married' => 'Married', 'Un-Married' => 'Un-Married', 'Widow' => 'Widow'];
        $docimile_verifications = ['' => 'Select Domicile Verification', 'Yes' => 'Yes', 'No' => 'No', 'Awaited' => 'Awaited'];
        $educational_verifications = ['' => 'Select Educational Verification', 'Yes' => 'Yes', 'No' => 'No', 'Awaited' => 'Awaited'];
        $hajjPerform = ['No' => 'No', 'Yes' => 'Yes'];

        $taxFilers = ['' => 'Select Tax Filer', 'Yes' => 'Yes', 'No' => 'No'];
        $religions = ['' => 'Select Religion'];
        $districts = ['' => 'Select District'];
        $domiciles = ['' => 'Select Domicile'];

        $regs = Employees::select('religion')->distinct()->get();
        if (!empty($regs)) {
            foreach ($regs as $reg) {
                $religions[$reg->religion] = $reg->religion;
            }
        }

        $domcs = Employees::select('domicile')->distinct()->get();
        if (!empty($domcs)) {
            foreach ($domcs as $dom) {
                $domiciles[$dom->domicile] = $dom->domicile;
            }
        }

        $distrcts = Employees::select('home_district')->distinct()->get();
        if (!empty($distrcts)) {
            foreach ($distrcts as $dist) {
                $districts[$dist->home_district] = $dist->home_district;
            }
        }

        $countries = [
            '' => 'Select Country', 'United States of America' => 'United States of America', 'United Kingdom' => 'United Kingdom', 'Canada' => 'Canada', 'Italy' => 'Italy', 'Germany' => 'Germany', 'France' => 'France'
        ];

        $dependentList = DependentList::where('emp_id', $id)->orderBy('dl_id', 'desc')->first();

        return view('employees.edit', compact('page_title', 'countries', 'dependentList', 'hajjPerform', 'educational_verifications', 'docimile_verifications', 'genders', 'taxFilers', 'maritals', 'data', 'districts', 'domiciles', 'religions'));
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
            'emp_name.required' => 'The Employee Name field is required.',
            'f_h_name.required' => 'The Father/Husband field is required.',
            'dob.required' => 'The Date of Birth field is required.',
            'gender.required' => 'The Gender field is required.',
            'cnic.required' => 'The CNIC field is required.',
            'remarks.required' => 'The Remarks field is required.',
            'edoc.mimes' => 'The Dependent List Edoc must be in pdf format.',


        ];
        $validation = Validator::make(
            $request->all(),
            [
                'emp_name' => 'required',
                'f_h_name' => 'required',
                'dob' => 'required',
                'gender' => 'required',
                'cnic' => 'required',
                'edoc' => 'mimes:pdf',

            ],
            $messages
        );

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $record = Employees::find($id);
        $record->emp_name = request('emp_name');
        $record->fp_id = request('fp_id');
        $record->f_h_name = request('f_h_name');
        $record->dob = ($request->input('dob')) ? date('Y-m-d', strtotime($request->input('dob'))) : date('Y-m-d');
        $record->gender = request('gender');
        $record->cnic = request('cnic');
        $record->domicile = request('domicile');
        $record->home_district = request('home_district');
        $record->vi_mark = request('vi_mark');
        $record->religion = request('religion');
        $record->languages = request('languages');
        $record->marital_status = request('marital_status');
        $record->mobile_no = request('mobile_no');
        $record->off_phone_no = request('off_phone_no');
        $record->off_ext_no = request('off_ext_no');
        $record->fax_no = request('fax_no');
        $record->res_phone_no = request('res_phone_no');
        $record->emrgcy_contact_name = request('emrgcy_contact_name');
        $record->emrgcy_contact_phoneno = request('emrgcy_contact_phoneno');
        $record->emrgcy_contact_address = request('emrgcy_contact_address');
        $record->tax_filler = request('tax_filler');
        $record->mailing_address = request('mailing_address');
        $record->permenent_address = request('permenent_address');
        $record->ref_file_no = request('ref_file_no');
        $record->verified_on = ($request->input('verified_on')) ? date('Y-m-d', strtotime($request->input('verified_on'))) : '';
        $record->seniority_position = request('seniority_position');
        $record->pers_no = request('pers_no');
        $record->dual_nationality = request('dual_nationality');
        $record->blood_group = request('blood_group');
        $record->job_status = request('job_status');
        $record->domicile_verification = request('domicile_verification');
        $record->educational_verification = request('educational_verification');
        $record->hajj_perform = request('hajj_perform');

        if ($record->save()) {
            /// store image
            if ($record->fp_id && $request->hasFile('picture')) {
                $image = $request->file('picture');
                $filename = $request->file('picture')->getClientOriginalName();
                $name = $record->fp_id . '.jpg';
                $destinationPath = base_path() . '\storage\emp_pic';

                $image->move($destinationPath, $name);
            }
            /// Update USERS TABLE
            $user = User::where('emp_id', '=', $record->emp_id)->first();
            if ($user) {
                $user->name = request('emp_name');
                $user->username = request('cnic');

                $user->save();
            }
        }

        if ($request->hasFile('edoc')) {
            $latDependentList = DependentList::orderBy('dl_id', 'desc')->first();
            $lat_dl_id = ($latDependentList) ? $latDependentList->dl_id + 1 : 1;
            $dependentList = new DependentList();
            $dependentList->dl_id = $lat_dl_id;
            $dependentList->emp_id = $record->emp_id;

            $file = $request->file('edoc');
            $new_filename = 'dependent_list_' . $record->emp_id . '_' . $lat_dl_id;
            $path = 'storage/emp_pic/dependentList_' . $record->emp_id;
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $dependentList->edoc = $completeUrl;

            $dependentList->edoc_date = date('Y-m-d');
            $dependentList->save();
            $employee = Employees::where('emp_id', '=', $id)->first();
            $employee->dependent_list_dated = date('Y-m-d');
            $employee->save();
        }

        Session::flash('success', 'Employe has been updated successfully.');
        return redirect('employees');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id, Request $request)
    {
        //echo 'kjj'.$id;die;
        DB::beginTransaction();
        /*try {
            $user = User::where('emp_id', '=', $id)->first();

            DB::table('ROLE_USER')->where('user_id', '=', $user->id)->delete();
            DB::table('TBL_USER')->where('EMP_ID', $id)->delete();
            DB::table('TBL_EMP')->where('EMP_ID', $id)->delete();

            DB::commit();

            Session::flash('success', 'Employee has been deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('Error', 'Record could not delete. Try again.');
        }*/



        return redirect('employees');
    }

    /**
     * Delete Employee with relevant detail and user account.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroyEmpDetail($id)
    {

        try {
            return DB::transaction(function () use ($id) {

                DB::table('TBL_APPOINTMENT')->where('EMP_ID', $id)->delete();
                DB::table('TBL_FAMILY')->where('EMP_ID', $id)->delete();
                DB::table('TBL_ASSETS')->where('EMP_ID', $id)->delete();
                DB::table('TBL_EDUCATION')->where('EMP_ID', $id)->delete();
                DB::table('TBL_EXPERIENCE')->where('EMP_ID', $id)->delete();
                DB::table('TBL_CARRIER')->where('EMP_ID', $id)->delete();
                DB::table('TBL_REWARD')->where('EMP_ID', $id)->delete();
                DB::table('TBL_PENALTIE')->where('EMP_ID', $id)->delete();
                DB::table('TBL_ACR')->where('EMP_ID', $id)->delete();
                DB::table('TBL_LEAVE')->where('EMP_ID', $id)->delete();
                DB::table('TBL_NOC')->where('EMP_ID', $id)->delete();
                DB::table('TBL_CASE_EMP')->where('EMP_ID', $id)->delete();
                /** Master Table */
                DB::table('TBL_EMP')->where('EMP_ID', $id)->delete();
                /** Relevant System User */
                $user = User::where('emp_id', '=', $id)->first();
                DB::table('ROLE_USER')->where('user_id', '=', $user->id)->delete();
                DB::table('USERS')->where('EMP_ID', $id)->delete();

                Session::flash('success', 'Employee has been deleted successfully.');
                return redirect('employees/employees_list');
            });
        } catch (\Exception $e) {

            Session::flash('Error', 'Record could not delete. Try again.');
            return redirect('employees/employees_list');
        }
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
        $page_title = 'Employees';
        $where = [];

        if (Auth::user()->username == 'ddpersonnel-2') {
            $where[] = where('BS', '<=', 17)->orWhere('BS', '=', [null]);
        } else if (Auth::user()->username == 'ddpersonnel-1') {
            $where[] = where('BS', '>=', 17)->orWhere('BS', '=', null);
        }
        $data = VEmp::select([
            'FP_ID', 'EMP_ID', 'EMP_NAME', 'F_H_NAME',
            'DOB', 'CNIC', 'DOMICILE', 'BS', 'REF_FILE_NO',
            'MOBILE_NO', 'DATE_OF_APPOINTMENT', 'TYPE_OF_APPOINTMENT',
            'RELIGION', 'GENDER', 'EMP_PENSION_STATUS', 'DESIGNATION',
            'HIGHIEST_QUALIFICATION', 'AGE_YEAR', 'PLACE_OF_POSTING', 'SERVICE_TYPE',
            'CURRENT_POST', 'CURRENT_BS', 'CADRE_NAME', 'JOB_STATUS',
        ])->where($where)->orderBy('emp_id', 'DESC')->orderBy('emp_name', 'ASC')
            ->get();

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

        $emp = DB::table('V_EMP')->select([
            'FP_ID', 'EMP_ID', 'EMP_NAME', 'F_H_NAME', 'DOB', 'CNIC', 'DOMICILE', 'BS', 'REF_FILE_NO',
            'DATE_OF_APPOINTMENT', 'TYPE_OF_APPOINTMENT'
        ]);

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
        $pen = DB::table('TBL_PENALTIE')->where('EMP_ID', $id)->orderBy('penalitie_id', 'DESC  ')->get();
        $emp = DB::table('TBL_EMP')->where('EMP_ID', $id)->first();
        $appoint = DB::table('TBL_APPOINTMENT')->where('EMP_ID', $id)->orderBy('appointment_status', 'DESC')->orderBy('appointment_id', 'DESC  ')->get();
        $family = DB::table('TBL_FAMILY')->where('EMP_ID', $id)->orderBy('FAMILY_ID', 'DESC  ')->get();

        $eduction = DB::table('TBL_EDUCATION')
            ->leftJoin('TBL_EDUCTION_TYPE', 'TBL_EDUCATION.DOCUMENT_TYPE_ID', '=', 'TBL_EDUCTION_TYPE.EDU_TYPE_ID')
            ->where('TBL_EDUCATION.EMP_ID', $id)
            ->orderBy('TBL_EDUCATION.SESSIONS', 'DESC')->get();

        $experience = DB::table('TBL_EXPERIENCE')
            ->where('EMP_ID', $id)
            ->select('TBL_EXPERIENCE.*', (DB::raw("
            trunc(months_between(ending_date, joining_Date) / 12) as exp_years
        ,trunc(mod(months_between(ending_date, joining_Date), 12)) as exp_months
        ,trunc(ending_date - (add_months(joining_Date, trunc(months_between(ending_date, joining_Date))))) as exp_days
 ")))
            ->orderBy('joining_Date', 'DESC  ')->get();
        $reward = DB::table('TBL_REWARD')->where('EMP_ID', $id)->orderBy('REWARD_ID', 'DESC  ')->get();
        $leave = DB::table('TBL_LEAVE')
            ->leftJoin('TBL_LEAVE_TYPE', 'TBL_LEAVE.LEAVE_TYPE_ID', '=', 'TBL_LEAVE_TYPE.LT_ID')
            ->where('TBL_LEAVE.EMP_ID', $id)->orderBy('TBL_LEAVE.leave_id', 'DESC  ')->get();
        $extension = DB::table('TBL_EMP_EXTENSION')->where('EMP_ID', $id)
            ->select('TBL_EMP_EXTENSION.*', (DB::raw("
            trunc(months_between(ext_to, ext_from) / 12) as ext_years
        ,trunc(mod(months_between(ext_to, ext_from), 12)) as ext_months
        ,trunc(ext_to - add_months(ext_from, trunc(months_between(ext_to, ext_from)))) as ext_days
 ")))->orderBy('EXT_ID', 'DESC ')->get();

        //echo "<pre>";
        // print_r($extension);die;
        $noc = DB::table('TBL_NOC')->join('TBL_EMP', 'TBL_NOC.EMP_ID', '=', 'TBL_EMP.EMP_ID')->where('TBL_NOC.EMP_ID', $id)->orderBy('noc_id', 'DESC  ')->get();

        $promotion = DB::table('V_CARRIER')
            ->select(
                'V_CARRIER.POST_NAME',
                'V_CARRIER.CHARGE_ID',
                'V_CARRIER.CARRIER_ID',
                'V_CARRIER.BS',
                'V_CARRIER.CHARGE_TITLE',
                'V_CARRIER.PLACE_ID',
                'V_CARRIER.SECTION_ID',
                'V_CARRIER.PLACE_TITLE',
                'V_CARRIER.SECTION_NAME',
                'V_CARRIER.JOINING_DATE',
                'V_CARRIER.ORDER_ID',
                'V_CARRIER.CARRIER_STATUS'
            )
            ->where('EMP_ID', $id)
            ->where('CHARGE_ID', '>=', 100)->where('CHARGE_ID', '<', 199)
            ->orderBy('carrier_status', 'DESC')
            ->orderBy('joining_date', 'DESC')->get();

        $posting = DB::table('V_CARRIER')
            ->select(
                'TBL_SANCTION.STRENGTH_NAME',
                'V_CARRIER.POST_NAME',
                'V_CARRIER.CARRIER_ID',
                'V_CARRIER.BS',
                'V_CARRIER.CHARGE_TITLE',
                'V_CARRIER.PLACE_ID',
                'V_CARRIER.SECTION_ID',
                'V_CARRIER.PLACE_TITLE',
                'V_CARRIER.SECTION_NAME',
                'V_CARRIER.JOINING_DATE',
                'V_CARRIER.RELIEVING_DATE',
                'V_CARRIER.REMARKS',
                'V_CARRIER.ORDER_ID',
                'V_CARRIER.CARRIER_STATUS'
            )
            ->leftJoin('TBL_SANCTION', 'V_CARRIER.REPORTING_OFF_ID', '=', 'TBL_SANCTION.SANCTION_ID')
            ->where('EMP_ID', $id)
            ->where('CHARGE_ID', '>=', 200)->where('CHARGE_ID', '<', 299)
            ->orderBy('carrier_status', 'DESC')
            ->orderBy('joining_date', 'DESC')->get();

        $misc = DB::table('V_CARRIER')
            ->select(
                'TBL_SANCTION.STRENGTH_NAME',
                'V_CARRIER.POST_NAME',
                'V_CARRIER.CARRIER_ID',
                'V_CARRIER.BS',
                'V_CARRIER.CHARGE_TITLE',
                'V_CARRIER.PLACE_ID',
                'V_CARRIER.SECTION_ID',
                'V_CARRIER.PLACE_TITLE',
                'V_CARRIER.SECTION_NAME',
                'V_CARRIER.JOINING_DATE',
                'V_CARRIER.RELIEVING_DATE',
                'V_CARRIER.REMARKS',
                'V_CARRIER.ORDER_ID',
                'V_CARRIER.CARRIER_STATUS'
            )
            ->leftJoin('TBL_SANCTION', 'V_CARRIER.REPORTING_OFF_ID', '=', 'TBL_SANCTION.SANCTION_ID', 'V_CARRIER.CARRIER_STATUS')
            ->where('EMP_ID', $id)
            ->where('CHARGE_ID', '>=', 300)->where('CHARGE_ID', '<', 399)
            ->orderBy('carrier_status', 'DESC')
            ->orderBy('joining_date', 'DESC')->get();
        $carrier = DB::table('V_CARRIER')
            ->select(
                'TBL_SANCTION.STRENGTH_NAME',
                'V_CARRIER.POST_NAME',
                'V_CARRIER.CARRIER_ID',
                'V_CARRIER.BS',
                'V_CARRIER.CHARGE_TITLE',
                'V_CARRIER.PLACE_ID',
                'V_CARRIER.SECTION_ID',
                'V_CARRIER.PLACE_TITLE',
                'V_CARRIER.SECTION_NAME',
                'V_CARRIER.JOINING_DATE',
                'V_CARRIER.RELIEVING_DATE',
                'V_CARRIER.REMARKS',
                'V_CARRIER.ORDER_ID',
                'V_CARRIER.CARRIER_STATUS'
            )
            ->leftJoin('TBL_SANCTION', 'V_CARRIER.REPORTING_OFF_ID', '=', 'TBL_SANCTION.SANCTION_ID')
            ->where('EMP_ID', $id)
            ->where('CHARGE_ID', '>=', 400)->where('CHARGE_ID', '<', 499)
            ->orderBy('carrier_status', 'DESC')
            ->orderBy('joining_date', 'DESC')->get();
        $acrs = ACR::where('EMP_ID', $emp->emp_id)->orderBy('year_id', 'desc')->get();
        $assets = DB::table('V_ASSETS')->where('emp_id', '=', $id)->orderBy('year_title', 'DESC')->get();

        return view('employees.emp_detail', compact('emp', 'appoint', 'assets', 'acrs', 'family', 'eduction', 'experience', 'carrier', 'reward', 'leave', 'noc', 'extension', 'promotion', 'posting', 'misc', 'pen'));
    }

    public function calculateServiceLength($appointment_date)
    {
        $date1 = date("m/d/Y");
        $date2 = date('m/d/Y', strtotime($appointment_date));

        $date1Timestamp = strtotime($date1);
        $date2Timestamp = strtotime($date2);
        $difference = $date1Timestamp - $date2Timestamp;

        $years = floor($difference / (365 * 60 * 60 * 24));

        $months = floor(($difference - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));

        $days = floor(($difference - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

        return $service_length = $years . " " . "Years" . "," . $months . "  " . "Months" . "  " . " ," . $days . " " . "Days";
    }

    public function misAssets($empId)
    {


        $emp = DB::table('V_EMP')->select(
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
            'DATE_OF_JOINING'
        )->where('EMP_ID', $empId)->first();

        $misBaseAssets = DB::connection('sqlsrv_cb')->table('NHAEmployee AS e')
            ->join('NHAAssets AS a', 'e.EMPCode', '=', 'a.EMPCode')
            ->join('BaseUnitDetail AS bd', function ($j) {
                $j->on('a.nsetno', '=', 'bd.nsetno')->on('a.item', '=', 'bd.itemname');
            })

            ->select(DB::raw("CONCAT(bd.make,' ',bd.model) AS manufacture"), 'a.newsetno', 'a.item', 'a.unitcost', 'a.hdtdate', 'a.billdate')
            ->where('e.nic', $emp->cnic)
            ->orderBy('a.hdtdate', 'DESC')->get();

        $misOtherAssets = DB::connection('sqlsrv_cb')->table('NHAEmployee AS e')
            ->join('NHAAssets AS a', 'e.EMPCode', '=', 'a.EMPCode')
            ->join('OtherItemsDetail AS other', function ($j) {
                $j->on('a.nsetno', '=', 'other.nsetno')->on('a.item', '=', 'other.itemname');
            })
            ->join('ItemAbbreviations AS abr', 'a.item', '=', 'abr.item_name')
            ->select(DB::raw("CONCAT(other.make,' ',other.model) AS manufacture"), 'abr.category', 'a.newsetno', 'a.item', 'a.unitcost', 'a.hdtdate', 'a.billdate')
            ->where('e.nic', $emp->cnic) //->where('abr.category', 'not like', 'Consumable')
            ->orderBy('a.hdtdate', 'DESC')->get();
        $misConsumAssets = collect($misOtherAssets)->filter(function ($asset) {
            return $asset->category == 'Consumable';
        })->toArray();

        $misNonConsAssets = collect($misOtherAssets)->filter(function ($asset) {
            return $asset->category !== 'Consumable';
        });
        //echo '<pre>';print_r($misNonConsAssets);die;

        $misAssets = array_merge($misBaseAssets, $misNonConsAssets->toArray());
        $dor = ($emp) ? \Carbon\Carbon::parse($emp->dob)->addYears(60)->subDay()->format('d-m-Y') : '';
        return view('employees.mis_assets', compact('emp', 'misAssets', 'misConsumAssets', 'dor'));
    }

    public function empProfileCard($empId)
    {
        $page_title = 'Employee Profile Card';
        $empService = new EmployeeService();
        $empInfo = $empService->empPersonalInfo($empId);
        $empEducation = $empService->empEducationInfo($empId);
        $empAppointment = $empService->empAppointmentInfo($empId);
        $empInitialBS = $empService->empInitialBS($empId);
        $empInfo->initial_bs = $empInitialBS->bs;
        $empTransfer = $empService->empTransferInfo($empId);
        $empInfo->empPic = ($empInfo->fp_id && file_exists('storage/emp_pic/' . $empInfo->fp_id . '.jpg')) ? URL::to('storage/emp_pic/') . '/' . $empInfo->fp_id . '.jpg' : URL::to('storage/emp_pic/default.png');

        return view('employees.employee_profile_card', compact('page_title', 'empInfo', 'empEducation', 'empAppointment', 'empTransfer'));
    }

    public function employee_icp($emp_id = null)
    {
        /*if(!Auth::user()->can('employees_detail'))
        abort(403);*/
        //echo 'tt';die;
        $empId = (!$emp_id) ? auth()->user()->emp_id : $emp_id;

        $emp = DB::table('V_EMP')->select(
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



        $service_length = $this->calculateServiceLength($emp->date_of_appointment);
        /*  $date1 = date("m/d/Y");
        $date2 = date('m/d/Y', strtotime($emp->date_of_joining));

        $date1Timestamp = strtotime($date1);
        $date2Timestamp = strtotime($date2);
        $difference = $date1Timestamp - $date2Timestamp;

        $years = floor($difference / (365 * 60 * 60 * 24));

        $months = floor(($difference - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));

        $days = floor(($difference - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

        $service_length = $years . " " . "Years" . "," . $months . "  " . "Months" . "  " . " ," . $days . " " . "Days"; */

        $family = DB::table('TBL_FAMILY')->where('EMP_ID', $empId)->orderBy('FAMILY_ID', 'DESC  ')->get();
        //echo $service_length; die;
        //echo"<pre>";print_r($emp);die;
        //$appointments = DB::table('V_APPOINTMENT')->select('APPOINTMENT_ID', 'POST_NAME', 'BS', 'DATE_OF_APPOINTMENT', 'TYPE_OF_APPOINTMENT', 'APPOINTMENT_QUOTA', 'SERVICE_TYPE', 'QUOTA_ADV_DATE', 'THROUGH_ADVER', 'EXAM_HELD', 'MERIT_NO')->where('EMP_ID', $emp->emp_id)->orderBy('APPOINTMENT_ID', 'DESC')->get();
        $appointments = DB::table('TBL_APPOINTMENT')->select('APPOINTMENT_ID', 'POST_NAME', 'BS', 'DATE_OF_APPOINTMENT', 'TYPE_OF_APPOINTMENT', 'APPOINTMENT_QUOTA', 'SERVICE_TYPE', 'QUOTA_ADV_DATE', 'THROUGH_ADVER', 'EXAM_HELD', 'MERIT_NO')->where('EMP_ID', $emp->emp_id)->orderBy('APPOINTMENT_ID', 'DESC')->get();
        $education = DB::table('TBL_EDUCATION')->where('EMP_ID', $emp->emp_id)->orderBy('sessions', 'DESC')->get();

        $transfer = DB::table('V_CARRIER')->where('charge_id', '>=', 200)->where('charge_id', '<', 400)->where('EMP_ID', $emp->emp_id)->orderBy('carrier_status', 'DESC')->orderBy('joining_date', 'DESC')->orderBy('BS', 'DESC')->get(); //->orderBy('BS', 'DESC')->orderBy('RELIEVING_DATE', 'DESC')
        $promotion = DB::table('V_CARRIER')->where('charge_id', '>=', 100)->where('charge_id', '<', 200)->where('EMP_ID', $emp->emp_id)->orderBy('joining_date', 'DESC')->get();

        $misc = DB::table('V_CARRIER')->where('charge_id', '>=', 300)->where('charge_id', '<', 400)->where('EMP_ID', $emp->emp_id)->orderBy('carrier_status', 'DESC')->orderBy('joining_date', 'DESC')->orderBy('BS', 'DESC')->get();

        $training_local = DB::table('V_TRAINING_DONE')->where('PLACE', '=', 'Pakistan')
            ->where(function ($query) {
                $query->where('TRAINING_TYPE', '=', 'Training')
                    ->OrWhere('TRAINING_TYPE', '=', 'Seminar');
            })
            ->where('EMP_ID', $emp->emp_id)
            ->orderBy('start_date', 'DESC')->get();

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
        //$acrs = DB::table('V_ACR')->where('EMP_ID', $emp->emp_id)->orderBy('year_title', 'DESC')->get();
        $acrs = ACR::where('EMP_ID', $emp->emp_id)->orderBy('year_id', 'desc')->get();
        /* echo"<pre>";print_r($acrs);die;
        echo"test"; die; */
        //$monthly_attendance = DB::connection('sqlsrv')->table('VIEWMONTHLYDATA')->where('EMPNO', '=', //$emp->fp_id)->get();
        // MIS Inventory


        //echo '<pre>';print_r($misAssets);die;

        $conn = DB::connection('sqlsrv_fin');
        // Sp_PaySlip ("17301-8391053-1", 1, 2016)
        $finance = $conn->select('EXEC dbo.Sp_EmpPayments ?', array($emp->cnic));

        if ($emp) {
            $page_title = $emp->emp_name;
        } else {
            Session::flash('error', 'No Record Found!!');
            $page_title = '';
        }

        return view('employees.employee_detail', compact('page_title', 'misAssets', 'service_length', 'family', 'emp', 'appointments', 'education', 'transfer', 'misc', 'promotion', 'training_local', 'training_forign', 'offical_visits', 'reward', 'penalties', 'experience', 'assets', 'monthly_attendance', 'finance', 'acrs'));
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
    public function pdfSalarySlip($cnic, $month, $year, $emp_id)
    {
        if (!Auth::user()->can('employee_salary_info')) {
            abort(403);
        }
        $page_title = "Pay Slip";
        $emp = DB::table('TBL_EMP')->where('EMP_ID', $emp_id)->first();
        $dor = ($emp) ? \Carbon\Carbon::parse($emp->dob)->addYears(60)->subDay()->format('d-m-Y') : '';

        $conn = DB::connection('sqlsrv_fin');
        $paydata = $conn->select('EXEC dbo.Sp_PaySlip ?, ?, ?', array($cnic, $month, $year));

        return view('employees.salary_slip_pdf', compact('emp', 'dor', 'paydata', 'year'));
    }

    /// Employee Salary - Pay Slip Info
    public function empSalary($emp_id = 0)
    {
        if (!Auth::user()->can('employee_salary_info')) {
            abort(403);
        }

        $page_title = "Pay Slip";

        $empID = ($emp_id === 0) ? Auth::user()->emp_id : $emp_id;
        $emp = DB::table('TBL_EMP')->where('EMP_ID', $empID)->first();
        $conn = DB::connection('sqlsrv_fin');

        $finance = $conn->select('EXEC dbo.Sp_EmpPayments ?, ?', array($emp->cnic, 7104)); //->where('title','not equal','%Salary%');


        $collection = collect($finance)->groupBy('AccountCode');


        $headsTotal = $collection->map(function ($head, $key) {

            $hosLabObj = new \stdclass();
            $hosLabObj->id = $key;
            $hosLabObj->name = $head[0]->COA;
            $hosLabObj->y = $head->sum('Amount');
            $hosLabObj->drilldown = true;
            //$hosLabObj->drilldown = $head[0]->COA;
            return $medExpArr[] = $hosLabObj;
        });

        $medHeads = array_values($headsTotal->all());

        $accountCodes = AdvAccounts::get();
        /* echo '<pre>';
        print_r($accountCodes);
        die; */

        $emp_advances = EmpAdvances::where('cnic', $emp->cnic)->orderBy('refmonth', 'desc')->orderBy('accountcode', 'asc')->get();

        /* echo '<pre>';
        print_r($emp_advances);
        die; */
        $years = ['' => 'Select Year'];

        $year = DB::table('TBL_YEAR')->where('year_status', '=', 1)->orderBy('year_title', 'DESC')->get();
        foreach ($year as $row) {
            $years[$row->year_id] = $row->year_title;     //. '- (' . $row->year_id . ')';
        }


        $headsArr = ['Hospital & Labs', 'Medicines', 'Medical Allowance', 'Reimbursement'];

        //echo '<pre>';print_r($medHeads);die;

        return view('employees.employee_salary_detail', compact('page_title', 'medHeads', 'years', 'emp', 'emp_advances', 'finance'));
    }



    /// Employee Year Wise Medical Expense
    public function empMedExpYear($cnic, $code)
    {
        /*if(strlen($code)==4 && $code!=7104){
			echo 'jjj';die;
		}*/
        //echo 'code -  '.$code;die;
        $conn = DB::connection('sqlsrv_fin');
        $medExp = $conn->select('EXEC dbo.Sp_EmpPayments ?, ?', array($cnic, $code));
        //dd($medExp);
        //$medExpColl = collect($medExp)->groupBy('Year');
        $medExpColl = collect($medExp)->filter(function ($item) use ($code) {
            return ($item->AccountCode === $code) ? $item : '';
        })->groupBy('Year');
        //dd($medDrill);
        $medExpYear = $medExpColl->map(function ($head, $key) {

            $hosLabObj = new \stdclass();
            $hosLabObj->id = $key;
            $hosLabObj->name = $head[0]->Year;
            $hosLabObj->y = $head->sum('Amount');
            //$hosLabObj->drilldown = true;
            //$hosLabObj->drilldown = $head[0]->COA;
            return $medExpYearArr[] = $hosLabObj;
        });
        $medExpYear = array_values($medExpYear->all());
        //dd($medExpYear);
        return response()->json(['data' => $medExpYear]);
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

        //echo "<pre>";print_r($result);die;

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

        $validation = Validator::make(
            $request->all(),
            [
                'password' => 'min:5',
                'password_confirmation' => 'same:password',
                'profile_picture' => 'mimes:jpeg,bmp,png,jpg|max:1000',
            ]
        );

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
            $imageName = $emp->fp_id . '.jpg'; // . $file->getClientOriginalExtension();
            $request->file('profile_picture')->move(
                base_path() . '/storage/emp_pic/',
                $imageName
            );
        }

        $user->save();

        Session::flash('success', 'Profile updated successfully.');

        return Redirect::to('/employees');
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
