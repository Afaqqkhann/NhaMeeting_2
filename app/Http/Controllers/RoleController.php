<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Auth;
class RoleController extends Controller
{
    public function __construct() {

        /*$this->middleware('auth');
        if(!Auth::user()->can('roles'))
            abort(403);*/
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //echo 'tt';die;
		$roles = Role::orderBy('id')->get();

        return view('role.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::where('parent', '=', 1)->orderBy('sort','ASC')->get();

        return view('role.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $permissions = $request->input('permissionsToAssign');

        $roleId = Role::orderBy('id', 'desc')->first()->id;

        $role = new Role();
        $role->id = ($roleId == null) ? 1 : $roleId + 1;
        $role->name = $request->input('name');
        $role->display_name = $request->input('display_name');
        $role->description = $request->input('description');
        if($role->save()){
            $role->attachPermissions($permissions);
        }
        return Redirect::to('role');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        return view('role.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $role = Role::find($id);
        $permissions = Permission::where('parent', '=', 1)->orderBy('sort','ASC')->get();

        return view('role.edit', compact('role', 'permissions'));
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

        $permissions = $request->input('permissionsToAssign');

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->display_name = $request->input('display_name');
        $role->description = $request->input('description');
        if($role->save()){
            DB::table('permission_role')->where('role_id', '=', $id)->delete();
            $role->attachPermissions($permissions);
        }
        return Redirect::to('role');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        $role->delete();

        return Redirect::to('role');
    }



}
