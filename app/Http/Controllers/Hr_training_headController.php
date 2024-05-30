<?php

namespace App\Http\Controllers;

use App\Models\HEAD;
use App\Models\HRD;
use App\Models\Order;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Validator;
use Input;

class Hr_training_headController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($th_type_id=null, $course_id=null)
    {
        $page_title = 'Add Training Head';
        $TH_type = ['' => 'Select Training Head Type', '1' => 'Organization/Institute', '2' => 'Course Title', '3' => 'Sponser Head', '4' => 'Benefit','5' => 'Training Category', '6' => 'Place Title','7' => 'Venue'];
        return view('hrd.create', compact('page_title','TH_type', 'th_type_id','course_id'));
    }
	 public function create1($id)
    {
        //echo "test"; die;
        $page_title = 'Add Training Head';
         $TH_type = [ '3' => 'Course Title'];
        return view('hrd.create1', compact('page_title','TH_type', 'id'));
    }
	 public function sponcer($id=NULL)
    {
        //echo "test"; die;
        $page_title = 'Add Training Head';
        $TH_type = [ '2' => 'Sponser Head'];
        return view('hrd.create1', compact('page_title','TH_type', 'id'));
    }
    public function place($id)
    {
        //echo "test"; die;
        $page_title = 'Add Training Head';
        $TH_type = [ '6' => 'Place Title'];
        return view('hrd.create1', compact('page_title','TH_type', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = array(
            'required' => 'The :attribute field is required.',
        );

        $validator = Validator::make($request->all(), [

            'th_type' => 'required',
            'th_title' => 'required',

        ], $messages);
        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
		$ord = HEAD::orderBy('TH_ID', 'desc')->first();
		//echo "<pre>"; print_r($ord); die;
        $order = new Head();
        $order->TH_ID = ($ord) ? $ord->th_id + 1: 1;
        //echo "<pre>"; print_r($order->TH_ID); die;
        $order->TH_TITLE = $request->input('th_title');
        $order->TH_TYPE = $request->input('th_type');       

        $order->TH_STATUS = 1;

        $order->save();
        if($request->input('course_id') && !empty($request->input('course_id'))){
            return redirect('t_courses/create/'.$request->input('course_id')); 
        }
        Session::flash('success', 'Hr Training Head created successfully.');
        return redirect('hrd');
    }
	
	  public function store1(Request $request)
    {
        //echo "test"; die;
        $messages = array(
            'required' => 'The :attribute field is required.',
        );

        $validator = Validator::make($request->all(), [

            'th_type' => 'required',
            'th_title' => 'required',

        ], $messages);
        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
        $ord = HEAD::orderBy('TH_ID', 'desc')->first();
        //echo "<pre>"; print_r($ord); die;
        $order = new Head();
        $order->TH_ID = ($ord) ? $ord->th_id + 1: 1;
        //echo "<pre>"; print_r($order->TH_ID); die;
        $order->TH_TITLE = $request->input('th_title');
        $order->TH_TYPE = $request->input('th_type');
        $order->TH_STATUS = 1;

        $order->save();
        Session::flash('success', 'Hr Training Head created successfully.');
        return redirect('t_courses/create/'.$request->input('id'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Training Head';
        $data = HEAD::find($id);

        return view('hrd.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Edit Training Head';
        $data = HEAD::find($id);

        $TH_type = ['' => 'Select TH Type', '1' => 'Organization/Institute', '2' => 'Course Title', '3' => 'Sponser Head', '4' => 'Benefit',
            '5' => 'Training Category', '6' => 'Place Title ','7' => 'Venue'];

        return view('hrd.edit', compact('page_title','data','TH_type'));

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
        $messages = array(
            'required' => 'The :attribute field is required.',
        );
        $validator = Validator::make($request->all(), [
            'th_type' => 'required',
            'th_title' => 'required',
        ], $messages);
        $departmentArray = array(
            'th_type' => $request->input('th_type'),
            'th_title' => $request->input('th_title'),
        );
        DB::table('TBL_TRAINING_HEAD')->where('TH_ID', '=', $id)->update($departmentArray);

        Session::flash('success', 'Training Head updated successfully.');

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
        Session::flash('success', 'Training Head updated successfully.');
        return redirect('hrd');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         DB::table('TBL_TRAINING_HEAD')->where('TH_ID', '=', $id)->delete();
		 //Order::where('TH_ID', '=', $id)->delete();
        Session::flash('success', 'Training Head has been deleted successfully.');
        return redirect('hrd');
    }
}
