<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Validator;
use Session;
use DB;

class CategoryController extends Controller
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
        $page_title = 'Category';
        $data = Category::all();

        return view('category.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Create Category';

        return view('category.create', compact('page_title'));
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
                'category_title'  => 	'required',
                //'edoc'	    =>	'mimes:jpeg,bmp,png,jpg|max:1000',
            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $record = Category::orderBy('category_id', 'desc')->first();

        $category = new Category();
        $category->category_id = ($record) ? $record->category_id + 1 : 1;
        $category->category_title = $request->input('category_title');
        $category->category_status = 1;
        $category->save();

        Session::flash('success', 'Category created successfully.');

        return redirect('category');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Category';
        $data = Category::find($id);

        return view('category.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Category';
        $data = Category::find($id);

        return view('category.edit', compact('page_title', 'data'));
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
                'category_title'  => 	'required',
                //'edoc'	    =>	'mimes:jpeg,bmp,png,jpg|max:1000',
            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $category = Category::find($id);
        $category->category_title = $request->input('category_title');
        $category->save();

        Session::flash('success', 'Category updated successfully.');

        return redirect('category');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Category::where('CATEGORY_ID', '=', $id)->delete();
        Session::flash('success', 'Category has been deleted successfully.');

        return redirect('category');
    }
}
