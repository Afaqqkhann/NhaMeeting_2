<?php

namespace App\Http\Controllers;

use App\Models\Cadre;
use App\Models\Desig;
use App\Models\Order;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Validator;
use Input;

class DesigController extends Controller
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
        $page_title = 'Designation';
        $data = DB::table('TBL_DESIGNATION')->orderBy('desig_id', 'DESC')->get();
//        echo "<pre>";
//        print_r($data); die;

        return view('designation.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add Designation';
        //// Get Sections


        return view('designation.create', compact('page_title'));
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
                'desig_name'  => 	'required',
                //'duration_days' =>  'required',
                //'edoc'	    =>	'mimes:jpeg,bmp,png,jpg|max:1000',
            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $record = Desig::orderBy('desig_id', 'desc')->first();

        $book = new Desig();
        $book->desig_id = ($record) ? $record->desig_id + 1 : 1;
        $book->desig_name = $request->input('desig_name');
        $book->desig_status = 1;



        $book->save();

        Session::flash('success', 'Designation added successfully.');

        return redirect('designation');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Designation';
        $data = Desig::find($id);


        return view('designation.show', compact('page_title','data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Designation';
        $data = Desig::find($id);



        return view('designation.edit', compact('page_title','data'));

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
            'desig_name.required' => 'The Designation Name field is required.',


        ];

        $validation = Validator::make($request->all(),
            [
                'desig_name'  => 	'required',

            ],$messages);


        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $departmentArray = array(
            'desig_name' => $request->input('desig_name'),


        );

        //print_r($departmentArray);die;

        DB::table('TBL_DESIGNATION')->where('desig_id', '=', $id)->update($departmentArray);

        Session::flash('success', 'Designation updated successfully.');

        return redirect('designation');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         DB::table('TBL_DESIGNATION')->where('desig_id', '=', $id)->delete();

        Session::flash('success', 'Designation has been deleted successfully.');

        return redirect('designation');
    }
}
