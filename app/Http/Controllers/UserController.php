<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employees\Employees;
use App\Role;
use App\User;
use Auth;
use Datatables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Redirect;
use Session;
use URL;
use Validator;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $users = User::all();
//print_r(count($users));die;
        return view('user.index', compact('users'));
    }

    public function update_password(Request $request)
    {
        $validation = Validator::make($request->all(),
            ['password' => 'required|min:5|max:15',
                'confirm_password' => 'required|same:password']);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $this->user->password = bcrypt($request->password);
        $this->user->p_read = $request->password;
        $this->user->save();
    }
	/**
	@Reset User Password
	*/
	public function resetPassword(Request $request){
		print_r($request);die;
		$this->user->password = bcrypt('demo1234');
		$this->user->p_read = 'demo1234';
		$this->user->save();
		
		return response()->json(['msg' => 'Password has been reset successfully.']);
		
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $roles = array(null => 'Select Role');
        $role = Role::all();

        foreach ($role as $row) {
            $roles[$row->id] = $row->display_name;
        }

        $employees = array(null => 'Select employee');
        $emp = DB::table('TBL_EMP')->orderBy('bs', 'DESC')->orderBy('emp_name', 'ASC')->get();

        foreach ($emp as $row) {
            $employees[$row->emp_id] = $row->emp_name.'- ('. $row->cnic.')';
        }

        return view('user.create', compact('roles', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
		
        $messages = [
            'username.required' => 'The Username field is required.',
            'username.unique' => 'The Username must be unique.',
           


        ];
        $validation = Validator::make($request->all(),
            [
                'username'  => 	'required|unique:users',
				 'role_id' => 'required',
                
            ],$messages);
		
		

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
		
		
        $userId = User::orderBy('id', 'desc')->first()->id;

        $role = Role::find($request->input('role_id'));

        $user = new User;
        $user->id = ($userId == null) ? 1 : $userId + 1;
        $user->username = $request->input('username');
        $user->name = $request->input('emp_name');

        if ($request->input('password') != "") {
            $user->password = Hash::make($request->input('password'));
            $user->p_read = $request->input('password');
        } else {
            $user->password = Hash::make('demo1234');
            $user->p_read = 'demo1234';
        }

        $user->email = $request->input('email');
        $user->emp_id = $request->input('emp_id');
		
        if ($user->save()) {
            $user->attachRole($role);
        }
		
        Session::flash('success', 'User added successfully.');

        return Redirect::to('user');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return View::make('user.show')
            ->with('user', $user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {

        $user = User::find($id);

        $roles = array(null => 'Select Role');
        $role = Role::all();

        foreach ($role as $row) {
            $roles[$row->id] = $row->display_name;
        }

        $employees = array(null => 'Select employee');
        $emp = DB::table('TBL_EMP')->orderBy('bs', 'DESC')->orderBy('emp_name', 'ASC')->get();

        foreach ($emp as $row) {
            $employees[$row->emp_id] = $row->emp_name; //.'-'. $row->designation;
        }

        return view('user.edit', compact('user', 'roles', 'employees'));
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

        $messages = array(
            'required' => 'The :attribute field is required.',
        );

        $validator = Validator::make($request->all(), [
            'emp_name' => 'required',
            'role_id' => 'required',

        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        //echo $request->input('username');die;
        $user = User::find($id);
        $role = Role::find($request->input('role_id'));

        $username = $request->input('username');
        $email = $request->input('email');
        $empname = $request->input('emp_name');
        $empid = $request->input('emp_id');

        if (!empty($request->input('password'))) {
            $userPassword = Hash::make($request->input('password'));
            $userPassRead = $request->input('password');
        } else {
            $userPassword = $user->password;
            $userPassRead = $user->password;
        }

        $updateFields = array(
            'username' => $username,
            'email' => $email,
            'emp_id' => $empid,
            'name' => $empname,
            'password' => $userPassword,
            'p_read' => $userPassRead,

        );

        //echo '<pre>';print_r($updateFields);die;

        if (DB::table('USERS')->where('id', '=', $id)->update($updateFields)) {

            DB::table('role_user')
                ->where('user_id', $id)
                ->delete();

            $user->attachRole($role);

        }

        Session::flash('success', 'User updated successfully.');

        return Redirect::to('user');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        DB::table('ROLE_USER')->where('user_id', '=', $id)->delete();

        DB::table('USERS')->where('id', '=', $id)->delete();

        Session::flash('success', 'User deleted successfully.');

        return Redirect::to('user');
    }

    public function login()
    {

        $username = Input::get('username');
        $password = Input::get('password');

        $cradentials = array(
            'username' => $username,
            'password' => $password,
        );

        if (Auth::attempt($cradentials)) {
            return Redirect::to('dashboard');
        } else {
            return Redirect::to('login');
        }

    }

    public function update_profile()
    {
        //echo 'test';die;
        $user = Auth::user();

        return view('user.edit_profile', compact('user'));
    }

    public function save_profile(Request $request)
    {

        $validation = Validator::make($request->all(),
            [
                'password' => 'min:5',
                'password_confirmation' => 'same:password',
                //'profile_picture' => 'mimes:jpeg,bmp,png,jpg,jpeg|max:1000',
            ]);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $user = User::find(Auth::id());
        $emp = Employees::find(Auth::user()->emp_id);
        if (!empty($request->password)) {
            $user->password = bcrypt($request->password);
            $user->p_read = $request->password;
        }

        /*if ($request->hasFile('profile_picture')) { //FP ID if not exist
        $file = $request->file('profile_picture');
        $imageName = $emp->fp_id . '.jpg'; // . $file->getClientOriginalExtension();

        $request->file('profile_picture')->move(
        base_path() . '/storage/emp_pic/', $imageName
        );
        }*/

        $user->save();

        Session::flash('success', 'Profile updated successfully.');

        return Redirect::to('/users/update_profile');
    }

    public function get_user_cnic($emp_id)
    {
        $user_info = DB::table('TBL_EMP')->select('CNIC', 'EMP_NAME')->where('emp_id', '=', $emp_id)->first();

        return response()->json(['employee' => $user_info]);
    }

    /**
     * Get data for datatable.
     *
     * @param
     * @return \Illuminate\Http\Response
     */
    public function get_data()
    {
        //
        //$users = User::select(['NAME', 'USERNAME', 'STATUS']);
        $users = DB::table('USERS U')->select(['U.ID', 'U.NAME', 'U.USERNAME', 'R.DISPLAY_NAME', 'U.STATUS'])->join('ROLE_USER RU', 'RU.USER_ID', '=', 'U.ID')->join('ROLES R', 'R.ID', '=', 'RU.ROLE_ID');
        return Datatables::of($users)
            ->editColumn('display_name', '@if ($display_name == "Administrator") <div class="label bg-blue">{{$display_name}}</div> @else <div class="label bg-yellow">{{$display_name}}</div> @endif')
            ->editColumn('status', '@if ($status == 1) <div class="label bg-green">Active</div> @else <div class="label bg-red">Blocked</div> @endif')
            ->addColumn('action', function ($users) {
                return '<center><a href="' . URL::to('/user') . '/' . $users->id . '/edit" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i>  Edit</a>
				</center>';
                /*<form action="{{ url("/users/")}}"'.$users->id.'" method="post" class="pull-left">
        <input type="hidden" name="_method" value="delete" />
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <button class="btn btn-xs">
        <i class="fa fa-times text-danger"></i>
        </button>
        </form>*/
            })
            ->removeColumn('id')
            ->make();
    }
}
