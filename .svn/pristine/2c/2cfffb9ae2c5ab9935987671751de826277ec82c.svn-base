<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Models\Category;
use Validator;
use Session;
use DB;

class SubCategoryController extends Controller
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
        $page_title = 'Subcategory';
        $data = SubCategory::all();

        return view('subcategory.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add Subcategory';
        $categories = ['' => 'Select Category'] + Category::lists('category_title', 'category_id')->all();

        return view('subcategory.create', compact('page_title', 'categories'));
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
                'sc_title'  => 	'required',
                'category_id' =>  'required',
                //'edoc'	    =>	'mimes:jpeg,bmp,png,jpg|max:1000',
            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $record = SubCategory::orderBy('sc_id', 'desc')->first();

        $subcategory = new SubCategory();
        $subcategory->sc_id = ($record) ? $record->sc_id + 1 : 1;
        $subcategory->sc_title = $request->input('sc_title');
        $subcategory->category_id = $request->input('category_id');
        $subcategory->sc_status = 1;
        $subcategory->save();

        Session::flash('success', 'Subcategory created successfully.');

        return redirect('subcategory');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Subcategory';
        $data = SubCategory::find($id);

        return view('subcategory.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Edit Subcategory';
        $data = SubCategory::find($id);
        $categories = ['' => 'Select Category'] + Category::lists('category_title', 'category_id')->all();

        return view('subcategory.edit', compact('page_title', 'data', 'categories'));
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
                'sc_title'  => 	'required',
                'category_id' =>  'required',
                //'edoc'	    =>	'mimes:jpeg,bmp,png,jpg|max:1000',
            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $subcategory = SubCategory::find($id);
        $subcategory->sc_title = $request->input('sc_title');
        $subcategory->category_id = $request->input('category_id');
        $subcategory->sc_status = 1;
        $subcategory->save();

        Session::flash('success', 'Subcategory updated successfully.');

        return redirect('subcategory');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        SubCategory::where('SC_ID', '=', $id)->delete();
        Session::flash('success', 'Subcategory has been deleted successfully.');

        return redirect('subcategory');
    }
}
