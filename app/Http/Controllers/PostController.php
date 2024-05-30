<?php

namespace App\Http\Controllers;

use App\Models\Cadre;
use App\Models\Desig;
use App\Models\Order;
use App\Models\Post;
use App\Models\Section;
use App\Models\Sanction;
use App\Models\SanctionApproved;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Validator;
use Input;

class PostController extends Controller
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
        $page_title = 'Post';
        //DB::enableQueryLog();
        $data = Post::with([
            'sanction',
            'cadre', 'section', 'designation'
        ])
            ->orderBy('post_id', 'desc')->get(); //die;
        //dd(DB::getQueryLog()); 
        /* $data = DB::table('TBL_POST ')->orderBy('post_id', 'DESC')
            ->join('TBL_CADRE', 'TBL_POST.cadre_id', '=', 'TBL_CADRE.cadre_id')
            ->join('TBL_SECTION', 'TBL_POST.section_id', '=', 'TBL_SECTION.section_id')
            ->join('TBL_DESIGNATION', 'TBL_POST.designation_id', '=', 'TBL_DESIGNATION.desig_id')
            ->get(); */
        //echo "<pre>";
        //print_r($data); die;

        return view('post.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add Post';

        $cadre = ['' => 'Select Cadre'] + Cadre::lists('cadre_name', 'cadre_id')->all();
        $section = ['' => 'Select Section'] + Section::lists('section_name', 'section_id')->all();
        $desig = ['' => 'Select Designation'] + Desig::lists('desig_name', 'desig_id')->all();
        return view('post.create', compact('page_title', 'cadre', 'section', 'desig'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'post_name'  =>     'required',
                'cadre'  =>     'required',
                'section'  =>     'required',
                'designation'  =>     'required',
                'post_type'  =>     'required',
            ]
        );

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        try {
            return DB::transaction(function () use ($request) {
                $record = Post::orderBy('post_id', 'desc')->first();
                $post = new Post();
                $post->post_id = ($record) ? $record->post_id + 1 : 1;
                $post->cadre_id = $request->input('cadre');
                $post->section_id = $request->input('section');
                $post->designation_id = $request->input('designation');
                $post->bs = $request->input('bs');
                $post->post_name = $request->input('post_name');
                $post->post_status = 1;
                $post->technical = 0;
                $post->save();

                $postTypeMax = Sanction::orderBy('sanction_id', 'desc')->first();
                $postType = new Sanction();
                $postType->sanction_id = ($postTypeMax) ? $postTypeMax->sanction_id + 1 : 1;
                $postType->approved = $request->input('post_type');
                $postType->post_id = $post->post_id;
                $postType->strength_name = $request->input('post_name');
                $postType->bs = $request->input('bs');
                $postType->save();

                Session::flash('success', 'Post added successfully.');
                return redirect('post');
            });
        } catch (\Illuminate\Database\QueryException $e) {
            echo $e->getMessage();
            //
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Post';
        $data = Post::with([
            'sanction',
            'cadre', 'section', 'designation'
        ])->find($id);
        return view('post.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'post';
        $data = Post::find($id);
        $cadre = ['' => 'Select Cadre'] + Cadre::lists('cadre_name', 'cadre_id')->all();
        $section = ['' => 'Select Section'] + Section::lists('section_name', 'section_id')->all();
        $desig = ['' => 'Select Designation'] + Desig::lists('desig_name', 'desig_id')->all();

        return view('post.edit', compact('page_title', 'data', 'cadre', 'section', 'desig'));
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
            'post_type.required' => 'The post type  field is required.',


        ];

        $validation = Validator::make(
            $request->all(),
            [
                'post_name'  =>     'required',
                'cadre'  =>     'required',
                'section'  =>     'required',
                'designation'  =>     'required',
                'post_type'  =>     'required',
            ]
        );

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }


        try {
            return DB::transaction(function () use ($request, $id) {
                $post = Post::findOrFail($id);
                $post->cadre_id = $request->input('cadre');
                $post->section_id = $request->input('section');
                $post->designation_id = $request->input('designation');
                $post->bs = $request->input('bs');
                $post->post_name = $request->input('post_name');
                $post->post_status = 1;
                $post->technical = 0;
                $post->save();

                $postType = Sanction::where('post_id', $post->post_id)->first();
				dd($postType);

                $postType->approved = $request->input('post_type');
                $postType->post_id = $post->post_id;
                $postType->strength_name = $request->input('post_name');
                $postType->bs = $request->input('bs');
                $postType->save();


                Session::flash('success', 'Post updated successfully.');
                return redirect('post');
            });
        } catch (\Illuminate\Database\QueryException $e) {
            echo $e->getMessage();
            //
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                Sanction::where('post_id', $id)->delete();
                Post::destroy($id);

                Session::flash('success', 'Post has been deleted successfully.');
                return redirect('post');
            });
        } catch (\Illuminate\Database\QueryException $e) {
            echo $e->getMessage();
        }
    }
}
