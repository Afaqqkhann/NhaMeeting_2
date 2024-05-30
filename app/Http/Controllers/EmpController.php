<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employees\Employees;
use App\Role;
use App\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Redirect;
use Session;
use Validator;
use Datatables;
use URL;
use App\Models\Emp;
use Naeem\Helpers\Helper;

class EmpController extends Controller
{

    public function __construct()
    {
        if (!Auth::user()->can('manage_employees')) {
            abort(403);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $page_title = "Employees";

        return view('emp.index', compact('page_title', 'data'));
    }

    public function setDuplicateEmployees(Request $request)
    {

        $emp = Emp::where('emp_id', $request->emp_id)->first();
        $emp->duplicate_record = 1;
        $emp->save();
        return response()->json(['msg' => 'Record updated ']);
    }

    public function getEmployeesData()
    {

        $employees = Employees::select('fp_id', 'duplicate_record', 'emp_id', 'emp_name', 'f_h_name', 'cnic', 'designation', 'gender', 'bs');
        return Datatables::of($employees)
            ->addColumn('action', function ($emp) {
                $empProfile = (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('SuperVisior')) ? '<a href="' . URL::to('/employee/profile') . '/' . $emp->emp_id . '"  target="_blank" class="btn btn-xs btn-default"><i class=" fa fa-user-secret" title="Employee Profile"></i></a>' : '';

                $empICPChart = (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('SuperVisior') || Auth::user()->hasRole('Personnel')) ? '<a href="' . URL::to('/employee/icp_chart') . '/' . $emp->emp_id . '"  target="_blank" class="btn btn-xs btn-default"><i class=" fa fa-picture-o" title="Employee ICP Chart"></i></a>' : '';

                $empDuplicate = (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('SuperVisior') || Auth::user()->hasRole('Personnel')) ? '<button id="emp_duplicte_' . $emp->emp_id . '" data-empId="' . $emp->emp_id . '" class="emp-duplicte btn btn-xs btn-info"><i class="fa fa-angellist " title="Employee Duplicate Entry"></i></button>' : '';

                return '<a href="' . URL::to('/employee/emp_detail') . '/' . $emp->emp_id . '"  target="_blank" class="btn btn-xs btn-warning"><i class="fa fa-eye" title="Employee Detail"></i></a>&nbsp;<a href="' . URL::to('/emp') . '/' . $emp->emp_id . '/edit" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-edit" title="Edit Employee"></i> </a>&nbsp;' . $empProfile . '&nbsp;' . $empICPChart . '&nbsp' . $empDuplicate;
            })
            ->addColumn('picture', function ($emp) {
                if (($emp->fp_id) && $emp->fp_id != null)
                    return '<img width="60" src="' . URL::to("storage/emp_pic/" . $emp->fp_id . ".jpg") . '" />';
                else
                    return '<img width="70" height="70"  src=" URL::to("storage/emp_pic/default.jpg")';
            })

            /*->editColumn('dob',function ($emp) {
                return Helper::get_date_format($emp->dob);
            })*/
            ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $page_title = "Add Employee";

        $genders = ['' => 'Select Gender', 'Male' => 'Male', 'Female'   => 'Female'];
        $maritals = ['' => 'Select Marital Status', 'Married' => 'Married', 'Un-Married' => 'Un-Married'];

        return view('emp.create', compact('page_title', 'genders', 'maritals'));
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


        ];
        $validation = Validator::make(
            $request->all(),
            [
                'emp_name' => 'required',
                'f_h_name' => 'required',
                'dob' => 'required',
                'gender' => 'required',
                'cnic' => 'required|unique:tbl_emp',


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
        $record->mailing_address = request('mailing_address');
        $record->permenent_address = request('permenent_address');
        $record->ref_file_no = request('ref_file_no');
        $record->verified_on = ($request->input('verified_on')) ? date('Y-m-d', strtotime($request->input('verified_on'))) : '';
        $record->seniority_position = request('seniority_position');
        $record->pers_no = request('pers_no');
        $record->dual_nationality = request('dual_nationality');
        $record->blood_group = request('blood_group');

        if ($record->save()) {
            $last_emp = Employees::orderby('emp_id', 'desc')->first();
            /// Store image
            /*if ($last_emp->fp_id && $request->hasFile('picture')) {
                $image = $request->file('picture');
                $name = $last_emp->fp_id . '.jpg';
                $destinationPath = base_path() . '\storage\emp_pic';
                $image->move($destinationPath, $name);
            }*/
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

        Session::flash('success', 'Employe has been added successfully.');
        return redirect('employees/employees_list');
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
        $maritals = ['' => 'Select Marital Status', 'Married' => 'Married', 'Un-Married' => 'Un-Married'];

        return view('emp.edit', compact('page_title', 'genders', 'maritals', 'data'));
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

        ];
        $validation = Validator::make(
            $request->all(),
            [
                'emp_name' => 'required',
                'f_h_name' => 'required',
                'dob' => 'required',
                'gender' => 'required',
                'cnic' => 'required',

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
        $record->mailing_address = request('mailing_address');
        $record->permenent_address = request('permenent_address');
        $record->ref_file_no = request('ref_file_no');
        $record->verified_on = ($request->input('verified_on')) ? date('Y-m-d', strtotime($request->input('verified_on'))) : '';
        $record->seniority_position = request('seniority_position');
        $record->pers_no = request('pers_no');
        $record->dual_nationality = request('dual_nationality');
        $record->blood_group = request('blood_group');

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
            $user->name = request('emp_name');
            $user->username = request('cnic');

            $user->save();
        }

        Session::flash('success', 'Employe has been updated successfully.');
        return redirect('employees/employees_list');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {

        $user = User::where('emp_id', '=', $id)->first();

        DB::table('ROLE_USER')->where('user_id', '=', $user->id)->delete();

        DB::table('TBL_EMP')->where('EMP_ID', $id)->delete();

        Session::flash('success', 'Employee has been deleted successfully.');
        return redirect('employees/employees_list');
    }
}
