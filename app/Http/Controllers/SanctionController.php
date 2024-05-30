<?php

namespace App\Http\Controllers;

use App\Models\Cadre;
use App\Models\Desig;
use App\Models\Order;
use App\Models\Post;
use App\Models\Sanction;
use App\Models\Section;
use App\Models\Wing;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Validator;
use Input;

class SanctionController extends Controller
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
        $page_title = 'Sanction';
        $data = DB::table('TBL_SANCTION')->orderBy('sanction_id', 'DESC')
            ->join('TBL_POST', 'TBL_SANCTION.post_id', '=', 'TBL_POST.post_id')
            ->get();
//        echo "<pre>";
//        print_r($data); die;


        return view('sanction.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add Sanction';
        //// Get Sections
        $post = ['' => 'Select Post'] +Post::lists('post_name', 'post_id')->all();

        return view('sanction.create', compact('page_title','post'));
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
                'bs'  => 	'required',
                'post'  => 	'required',


                //'duration_days' =>  'required',
                //'edoc'	    =>	'mimes:jpeg,bmp,png,jpg|max:1000',
            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $record = Sanction::orderBy('sanction_id', 'desc')->first();
        $book = new Sanction();
        $book->sanction_id = ($record) ? $record->sanction_id + 1 : 1;
        $book->bs = $request->input('bs');
        $book->post_id = $request->input('post');
        $book->strength_type = 0;
        $book->approved = 0;
        $book->sanction_status = 0;
        $book->save();
        Session::flash('success', 'Sanction added successfully.');
        return redirect('sanction');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'sanction';
        $data = Sanction::find($id);
        return view('sanction.show', compact('page_title','data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'sanction';
        $data = Sanction::find($id);
        $post = ['' => 'Select post'] + Post::lists('post_name', 'post_id')->all();



        return view('sanction.edit', compact('page_title','data','post'));

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
            'bs.required' => 'The bs field is required.',
            'post.required' => 'The bs post is required.',


        ];

        $validation = Validator::make($request->all(),
            [
                'bs'  => 	'required',
                'post'  => 	'required',

            ],$messages);


        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $departmentArray = array(
            'bs' => $request->input('bs'),
            'post_id' => $request->input('post'),


        );

        //print_r($departmentArray);die;

        DB::table('TBL_SANCTION')->where('sanction_id', '=', $id)->update($departmentArray);

        Session::flash('success', 'Sanction updated successfully.');

        return redirect('sanction');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         DB::table('TBL_SANCTION')->where('sanction_id', '=', $id)->delete();

        Session::flash('success', 'Sanction has been deleted successfully.');

        return redirect('sanction');
    }
}
