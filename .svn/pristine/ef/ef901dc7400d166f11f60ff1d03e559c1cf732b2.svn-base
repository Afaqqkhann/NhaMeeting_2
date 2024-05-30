<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Role;

use Datatables;
use DB;
use URL;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
class UserManagementController extends Controller
{
    public function __construct() {

        $this->middleware('auth');
        if(!Auth::user()->can('user_management_sub'))
            abort(403);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
		$users = User::all();		
		return view('user_management.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
		$roles = array(
			'0'		=>	'Select Role'
		);
		
		$role = Role::lists('display_name', 'id');
		
		foreach($role as $key => $value) {
			array_push($roles, $value);
		}
		
		$user = User::find($id);
		
		return view('user_management.edit', compact('roles', 'user'));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
                return '<center><a href="'.URL::to('/user_management/edit').'/'.$users->id.'" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i>  Edit</a></center>';
            })
			->removeColumn('id')
            ->make();
    }
	
}
