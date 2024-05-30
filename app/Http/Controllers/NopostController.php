<?php

namespace App\Http\Controllers;

use App\Models\Cadre;
use App\Models\Desig;
use App\Models\Order;
use App\Models\Post;
use App\Models\Section;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Validator;
use Input;

class NoPostController extends Controller
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
        $page_title = 'No Post';
        $data = DB::table('TBL_POST ')->orderBy('post_id', 'DESC')
            ->join('TBL_CADRE', 'TBL_POST.cadre_id', '=', 'TBL_CADRE.cadre_id')
            ->join('TBL_SECTION', 'TBL_POST.section_id', '=', 'TBL_SECTION.section_id')
            ->join('TBL_DESIGNATION', 'TBL_POST.designation_id', '=', 'TBL_DESIGNATION.desig_id')
            ->get();
//        echo "<pre>";
//        print_r($data); die;

        return view('nopost.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add No Post';
        //// Get Sections

        $cadre = ['' => 'Select Cadre'] + Cadre::lists('cadre_name', 'cadre_id')->all();
        $section = ['' => 'Select Section'] + Section::lists('section_name', 'section_id')->all();
        $desig = ['' => 'Select Designation'] + Desig::lists('desig_name', 'desig_id')->all();
        return view('nopost.create', compact('page_title','cadre','section','desig'));
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
                'post_name'  => 	'required',
                'cadre'  => 	'required',
                'section'  => 	'required',
                'designation'  => 	'required',

            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $record = Post::orderBy('post_id', 'desc')->first();

        $book = new Post();
        $book->post_id = ($record) ? $record->post_id + 1 : 1;
        $book->cadre_id = $request->input('cadre');
        $book->section_id = $request->input('section');
        $book->designation_id = $request->input('designation');
        $book->bs = $request->input('bs');
        $book->post_name = $request->input('post_name');
        $book->post_status = 1;
        $book->technical = 0;



        $book->save();

        Session::flash('success', 'Post added successfully.');

        return redirect('no_post');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'No Post';
        $data = Post::find($id);


        return view('nopost.show', compact('page_title','data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Edit No Post';
        $data = Post::find($id);
        $cadre = ['' => 'Select Cadre'] + Cadre::lists('cadre_name', 'cadre_id')->all();
        $section = ['' => 'Select Section'] + Section::lists('section_name', 'section_id')->all();
        $desig = ['' => 'Select Designation'] + Desig::lists('desig_name', 'desig_id')->all();

        return view('nopost.edit', compact('page_title','data','cadre','section','desig'));

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
            'cadre.required' => 'The Cadre  field is required.',
            'section.required' => 'The section  field is required.',
            'designation.required' => 'The designation  field is required.',
            'post_name.required' => 'The post name  field is required.',


        ];

        $validation = Validator::make($request->all(),
            [
                'cadre'  => 	'required',
                'section'  => 	'required',
                'designation'  => 	'required',
                'post_name'  => 	'required',

            ],$messages);


        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $departmentArray = array(
            'post_name' => $request->input('post_name'),
            'bs' => $request->input('bs'),
            'cadre_id' => $request->input('cadre'),
            'section_id' => $request->input('section'),
            'designation_id' => $request->input('designation'),


        );

        //print_r($departmentArray);die;

        DB::table('TBL_POST')->where('post_id', '=', $id)->update($departmentArray);

        Session::flash('success', 'Post updated successfully.');

        return redirect('no_post');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         DB::table('TBL_POST')->where('post_id', '=', $id)->delete();
        Session::flash('success', 'Post has been deleted successfully.');

        return redirect('no_post');
    }
}
