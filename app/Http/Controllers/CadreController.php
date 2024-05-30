<?php

namespace App\Http\Controllers;

use App\Models\Cadre;
use App\Models\Order;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Validator;
use Input;

class CadreController extends Controller
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
        $page_title = 'Cadre';
        $data = DB::table('TBL_CADRE')->orderBy('cadre_id', 'DESC')->get();
//        echo "<pre>";
//        print_r($data); die;

        return view('cadre.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add Cadre';
        //// Get Sections


        return view('cadre.create', compact('page_title'));
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
                'cadre_name'  => 	'required',
                //'duration_days' =>  'required',
                //'edoc'	    =>	'mimes:jpeg,bmp,png,jpg|max:1000',
            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $record = Cadre::orderBy('cadre_id', 'desc')->first();

        $book = new Cadre();
        $book->cadre_id = ($record) ? $record->cadre_id + 1 : 1;
        $book->cadre_name = $request->input('cadre_name');
        $book->cadre_status = 1;



        $book->save();

        Session::flash('success', 'Book added successfully.');

        return redirect('cadre');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Cadre';
        $data = Cadre::find($id);


        return view('cadre.show', compact('page_title','data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Order';
        $data = cadre::find($id);



        return view('cadre.edit', compact('page_title','data'));

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
            'cadre_name.required' => 'The Cadre Name field is required.',


        ];

        $validation = Validator::make($request->all(),
            [
                'cadre_name'  => 	'required',

            ],$messages);


        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $departmentArray = array(
            'cadre_name' => $request->input('cadre_name'),


        );

        //print_r($departmentArray);die;

        DB::table('TBL_CADRE')->where('cadre_id', '=', $id)->update($departmentArray);

        Session::flash('success', 'Cadre updated successfully.');

        return redirect('cadre');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         DB::table('TBL_CADRE')->where('cadre_id', '=', $id)->delete();
        Session::flash('success', 'Cadre has been deleted successfully.');

        return redirect('cadre');
    }
}
