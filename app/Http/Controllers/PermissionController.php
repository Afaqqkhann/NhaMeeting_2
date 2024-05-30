<?php

namespace App\Http\Controllers;

use App\Permission;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Auth;
class PermissionController extends Controller
{
    public function __construct() {

        $this->middleware('auth');
        if(!Auth::user()->can('permission'))
            abort(403);
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $permissions = Permission::where('parent','=',1)->orderBy('parent','ASC')->orderBy('sort','ASC')->get();
		
		//echo '<pre>';print_r($permissions);die;
        return view('permission.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
		$data = Permission::where('parent', '=', 1)->get();
		$parent = array('1' => 'Select Parent');
		foreach($data as $row)
			$parent[$row->id] = $row->display_name;

        return view('permission.create', compact('parent'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $permissionId = Permission::orderBy('id', 'desc')->first()->id;

        $permission = new Permission();
        $permission->id = ($permissionId == null) ? 1 : $permissionId + 1;
        $permission->name = $request->input('name');
        $permission->display_name = $request->input('display_name');
		$permission->parent = $request->parent;
		$permission->icon = $request->icon;
		$permission->link = $request->link;
		$permission->sort = $request->sort;
        $permission->description = $request->input('description');
        $permission->save();

        return Redirect::to('permission');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $permission = Permission::find($id);

        return view('permission.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
		$data = Permission::where('parent', '=', 1)->get();
		$parent = array('1' => 'Select Parent');
		foreach($data as $row)
			$parent[$row->id] = $row->display_name;
			
        $permission = Permission::find($id);

        return view('permission.edit', compact('permission', 'parent'));
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
        $permission = Permission::find($id);
        $permission->name = $request->input('name');
        $permission->display_name = $request->input('display_name');
        $permission->description = $request->input('description');
		$permission->icon = $request->icon;
		$permission->link = $request->link;
		$permission->sort = $request->sort;
		$permission->parent = isset($request->parent) ? $request->parent : 1;
        $permission->save();

        return Redirect::to('permission');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $permission = Permission::find($id);
		DB::table('PERMISSION_ROLE')->where('permission_id','=',$id)->delete();
        $permission->delete();

        return Redirect::to('permission');
    }
}
