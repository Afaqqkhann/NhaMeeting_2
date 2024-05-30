<?php

namespace App\Http\Controllers;

use App\Models\Cadre;
use App\Models\Desig;
use App\Models\Order;
use App\Models\Section;
use App\Models\Wing;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Validator;
use Input;

class SectionController extends Controller
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
        $page_title = 'Section';
        $data = DB::table('TBL_SECTION')->orderBy('section_id', 'DESC')
            ->join('TBL_WING', 'TBL_SECTION.WING_ID', '=', 'TBL_WING.WING_ID')
            ->get();


        return view('section.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add Section';
        //// Get Sections
        $wing = ['' => 'Select Wing'] +Wing::lists('wing_name', 'wing_id')->all();

        return view('section.create', compact('page_title','wing'));
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
                'section_name'  => 	'required',
                'wing'  => 	'required',


                //'duration_days' =>  'required',
                //'edoc'	    =>	'mimes:jpeg,bmp,png,jpg|max:1000',
            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $record = Section::orderBy('section_id', 'desc')->first();
        $book = new Section();
        $book->section_id = ($record) ? $record->section_id + 1 : 1;
        $book->wing_id = $request->input('wing');
        $book->section_name = $request->input('section_name');
        $book->section_status = 1;
        $book->save();
        Session::flash('success', 'section added successfully.');
        return redirect('section');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Section';
        $data = Section::find($id);
        return view('section.show', compact('page_title','data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'section';
        $data = Section::find($id);
        $wing = ['' => 'Select Wing'] + Wing::lists('wing_name', 'wing_id')->all();



        return view('section.edit', compact('page_title','data','wing'));

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
            'wing.required' => 'The Wing Name field is required.',


        ];

        $validation = Validator::make($request->all(),
            [
                'wing'  => 	'required',

            ],$messages);


        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $departmentArray = array(
            'section_name' => $request->input('section'),
            'wing_id' => $request->input('wing'),


        );

        //print_r($departmentArray);die;

        DB::table('TBL_SECTION')->where('section_id', '=', $id)->update($departmentArray);

        Session::flash('success', 'Section updated successfully.');

        return redirect('section');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         DB::table('TBL_SECTION')->where('section_id', '=', $id)->delete();

        Session::flash('success', 'Section has been deleted successfully.');

        return redirect('section');
    }
}
