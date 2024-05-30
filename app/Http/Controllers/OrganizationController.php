<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Session;
use DB;
use yajra\Oci8\Query\Grammars\OracleGrammar;

class OrganizationController extends Controller
{

    public function __construct()
    {
        DB::setDateFormat('DD-Mon-YY');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Organization';
        $data = Organization::all();

        return view('organization.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add Organization';

        return view('organization.create', compact('page_title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(),
            [
                'org_name'  => 	'required',
                //'edoc'	    =>	'mimes:jpeg,bmp,png,jpg|max:1000',
            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $record = Organization::orderBy('org_id', 'desc')->first();

        $organization = new Organization();
        $organization->org_id = ($record) ? $record->org_id + 1 : 1;
        $organization->org_name = $request->input('org_name');
        $organization->org_status = 1;
        $organization->save();

        Session::flash('success', 'Organization created successfully.');

        return redirect('organization');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Organization';
        $data = Organization::find($id);

        return view('organization.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Organization';
        $data = Organization::find($id);

        return view('organization.edit', compact('page_title', 'data'));
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
        $validation = Validator::make($request->all(),
            [
                'org_name'  => 	'required',
                //'duration_days' =>  'required',
                //'edoc'	    =>	'mimes:jpeg,bmp,png,jpg|max:1000',
            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $organization = Organization::find($id);
        $organization->org_name = $request->input('org_name');
        $organization->org_status = 1;
        $organization->save();

        Session::flash('success', 'Organization updated successfully.');

        return redirect('organization');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Organization::where('ORG_ID', '=', $id)->delete();
        Session::flash('success', 'Organization has been deleted successfully.');

        return redirect('organization');
    }
}
