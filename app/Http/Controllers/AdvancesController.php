<?php

namespace App\Http\Controllers;

use App\Models\Advances;
use App\Models\Cadre;
use App\Models\Carrier;
use App\Models\Charge;
use App\Models\Desig;
use App\Models\Education;
use App\Models\Emp;
use App\Models\Employees\Employees;
use App\Models\Experience;
use App\Models\Extension;
use App\Models\Family;
use App\Models\Order;
use App\Models\Place;
use App\Models\Post;
use App\Models\Relation;
use App\Models\Sanction;
use App\Models\Section;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Validator;
use Input;

class AdvancesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        ini_set('max_execution_time', 5000);
        ini_set('memory_limit', '5000M');
        $page_title = 'Advances';
        $data = DB::table('TBL_ADVANCES ')->orderBy('ADV_ID', 'DESC')->get();


        return view('advances.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add Advances';
        $status = [''=> 'Select Status','1' => 'HBA','2' => 'Car Adv'];
        return view('advances.create', compact('page_title', 'status'));
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
               'adv_title'  => 	'required',
//                'e_to'  => 	'required',
            ]);
        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $record = Advances::orderBy('adv_id', 'desc')->first();
        $adv = new Advances();
        $adv->adv_id = ($record) ? $record->adv_id + 1 : 1;
        $adv->adv_title = $request->input('adv_title');
        $adv->status = $request->input('status');
        $adv->save();
        Session::flash('success', 'Advance added successfully.');
        return Redirect('advances');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Advances';
        $data = Advances::find($id);
        return view('advances.show', compact('page_title','data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Advances';
        $data = Advances::find($id);
        $status = [''=> 'Select Status','1' => 'HBA','2' => 'Car Adv'];
       return view('advances.edit', compact('page_title', 'data', 'status'));

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
        $messages = [

            'adv_title.required' => 'The Advance title From field is required.',
//            'e_to.required' => 'The Extension To field is required.',
        ];
        $validation = Validator::make($request->all(),
            [
                'adv_title'  => 	'required',
//                'e_to'  => 	'required',
            ],$messages);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $departmentArray = array(
            'adv_title' => $request->input('adv_title'),
            'status' => $request->input('status'),
            );
        DB::table('TBL_ADVANCES')->where('ADV_ID', '=', $id)->update($departmentArray);
        Session::flash('success', 'Advances updated successfully.');
        return Redirect('advances');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Carrier::find($id);
        DB::table('TBL_ADVANCES')->where('ADV_ID', '=', $id)->delete();
        Session::flash('success', 'Advances has been deleted successfully.');
        return Redirect('advances');
    }
}
