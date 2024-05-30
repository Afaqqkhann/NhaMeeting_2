<?php

namespace App\Http\Controllers;

use App\Models\Reason;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Models\Category;
use Validator;
use Session;
use DB;

class InterneeReasonController extends Controller
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
        $page_title = 'Internee Reason';
        $data = Reason::all();
        return view('reason.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //echo "test"; die;
        $page_title = 'Add Subcategory';
       // $categories = ['' => 'Select Category'] + Category::lists('category_title', 'category_id')->all();

        return view('reason.create', compact('page_title'));
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
                'reason_title'  => 	'required',
//                'category_id' =>  'required',
                //'edoc'	    =>	'mimes:jpeg,bmp,png,jpg|max:1000',
            ]);
        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $record = Reason::orderBy('reason_id', 'desc')->first();
        $subcategory = new Reason();
        $subcategory->reason_id = ($record) ? $record->reason_id + 1 : 1;
        $subcategory->title = $request->input('reason_title');
        $subcategory->save();
        Session::flash('success', 'Reason created successfully.');
        return redirect('internee_reason');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Reasons';
        $data = Reason::find($id);
        return view('reason.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Edit Reason';
        $data = Reason::find($id);
        return view('reason.edit', compact('page_title', 'data'));
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
                'reason_title'  => 	'required',

                //'edoc'	    =>	'mimes:jpeg,bmp,png,jpg|max:1000',
            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $subcategory = Reason::find($id);
        $subcategory->title = $request->input('reason_title');
       // echo "<pre>"; print_r($subcategory); die;
        $subcategory->save();

        Session::flash('success', 'Reason updated successfully.');

        return redirect('internee_reason');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Reason::where('REASON_ID', '=', $id)->delete();
        Session::flash('success', 'Reason has been deleted successfully.');

        return redirect('internee_reason');
    }
}
