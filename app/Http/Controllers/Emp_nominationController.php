<?php

namespace App\Http\Controllers;

use App\Models\Cadre;
use App\Models\Carrier;
use App\Models\Course;
use App\Models\Desig;
use App\Models\Education;
use App\Models\Emp_Nomination;
use App\Models\Employees\Employees;
use App\Models\Experience;
use App\Models\Family;
use App\Models\Order;
use App\Models\Post;
use App\Models\Relation;
use App\Models\Section;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Validator;
use Input;

class Emp_nominationController extends Controller
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
//        $page_title = 'family';
//        $data = DB::table('TBL_FAMILY ')->orderBy('FAMILY_ID', 'DESC')
//            ->join('TBL_EMP', 'TBL_FAMILY.emp_id', '=', 'TBL_EMP.emp_id')
//            ->join('TBL_RELATION', 'TBL_FAMILY.relationship', '=', 'TBL_RELATION.r_id')
//            ->get();
//
//        return view('family.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function emp_select($selectid){
       // echo $selectid; die;
        ini_set('max_execution_time', 5000);
        ini_set('memory_limit', '5000M');
        $carrier =DB::table('v_carrier')->where('emp_id', '=',$selectid )->orderBy('carrier_id', 'DESC')->get();
        $statusArr = array();
        foreach ($carrier as $status) {
            $object2 = new \stdClass();
            $object2->carrier_id = $status->carrier_id;
            $object2->post_name = $status->post_name;
            $object2->place_type = $status->place_type;
            $object2->region_name = $status->region_name;
            $object2->zone_title = $status->zone_title;
            $object2->wing_name = $status->wing_name;
            $object2->wing_head = $status->wing_head;
            array_push($statusArr, $object2);
        }
        return response()->json(['data' => $statusArr]);
    }
    public function create($tc_id,$trn_id)
    {
       // echo "test"; die;
        $page_title = 'Add Emp Nomination';
         $emp = Employees::orderBy('emp_name','asc')->get();
        return view('emp_nom.create', compact('page_title', 'tc_id','trn_id', 'emp'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $messages = [
            'state.required' => 'The Emp  Name field is required.',
            'city.required' => 'The Carrier field is required.',

        ];
        $validation = Validator::make($request->all(),
            [
               'state' => 'required',
                'city' => 'required',
            ], $messages);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $record = Emp_Nomination::orderBy('tn_id', 'desc')->first();
        $nomination = new Emp_Nomination();
        $nomination->tn_id = ($record) ? $record->tn_id + 1 : 1;
        $nomination->emp_id = $request->input('state');
        $nomination->carrier_id = $request->input('city');
        $nomination->tc_id = $request->input('tc_id');
        $nomination->training_id = $request->input('trn_id');
        if($request->hasFile('e_doc')) {
            $file = $request->file('e_doc');
            $new_filename = 'emp_nomination'.$nomination->training_id;
            $path = 'public/NHA-IS/TRAININGS';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $nomination->tn_edoc = $completeUrl;
        }
  
        $nomination->save();
        Session::flash('success', 'Employee Nomination  added successfully.');
        return Redirect('/training_course/'.$request->input('tc_id'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Employee Nomination';
        $data = Emp_Nomination::find($id);
       // echo "<pre>"; print_r($data); die();
        return view('emp_nom.show', compact('page_title','data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
//        echo $id; die;
        $page_title = 'Employee Nomination';
        $data = Emp_Nomination::find($id);
         $carrier = $data->carrier_id;
        $carrier =DB::table('v_carrier')->where('emp_id', '=',$id )->orderBy('carrier_id', 'DESC')->get();
        foreach($carrier as $row){
            $carriers[$row->carrier_id] = $row->post_name;//.'-'. $row->designation;
        }

        $emp = ['' => 'Select Employee'] + Employees::orderBy('emp_name','asc')->lists('emp_name', 'emp_id')->all();

        $t_course = DB::table('v_training_course')->orderBy('tc_id','DESC')->get();
        foreach($t_course as $row){
            $charge[$row->tc_id] = $row->course;//.'-'. $row->designation;
        }
        $training = DB::table('v_trainings')->distinct()->orderBy('training_id','DESC')->get();
        foreach($training as $row){
            $trainigs[$row->training_id] = $row->training_typed.' '.$row->organization;//.'-'. $row->designation;
        }
        //echo "<pre>"; print_r($trainigs); die;
        return view('emp_nom.edit', compact('page_title','data','emp', 'charge', 'trainigs', 'carriers'));
    }
	
	 public function edit1($id)
    {
        //echo "test"; die;
        $page_title = 'Employee Nomination';
        $data = Emp_Nomination::find($id);
        $carrier = $data->carrier_id;
        $carrier =DB::table('v_carrier')->where('emp_id', '=',$id )->orderBy('carrier_id', 'DESC')->get();
        foreach($carrier as $row){
            $carriers[$row->carrier_id] = $row->post_name;//.'-'. $row->designation;
        }

        $emp = ['' => 'Select Employee'] + Employees::lists('emp_name', 'emp_id')->all();

        $t_course = DB::table('v_training_course')->orderBy('tc_id','DESC')->get();
        foreach($t_course as $row){
            $charge[$row->tc_id] = $row->course;//.'-'. $row->designation;
        }
        $training = DB::table('v_trainings')->distinct()->orderBy('training_id','DESC')->get();
        foreach($training as $row){
            $trainigs[$row->training_id] = $row->training_typed.' '.$row->organization;//.'-'. $row->designation;
        }
        //echo "<pre>"; print_r($trainigs); die;
        return view('emp_nom.edit1', compact('page_title','data','emp', 'charge', 'trainigs', 'carriers'));
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
            'emp_name.required' => 'The Employee Name field is required.',
        ];
        $validation = Validator::make($request->all(),
            [
                'emp_name'  => 	'required',
            ],$messages);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $order = Emp_Nomination::find($id);
        if($request->hasFile('e_doc')) {
            $file = $request->file('e_doc');
            /// new file name
            $new_filename = 'emp_nomination'.$id;
            $path = 'public/NHA-IS/TRAININGS';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $orderEdoc = $completeUrl;
        }
        else{
            if($order->e_doc)
                $orderEdoc = $order->e_doc;
            else
                $orderEdoc = '';

        }
        $departmentArray = array(
            //'tn_id' => $request->input('tn_id'),
            'emp_id' => $request->input('emp_name'),
            'carrier_id' => $request->input('carrier'),
            'tc_id' => $request->input('tc_id'),
            'training_id' => $request->input('trn_id'),
            'tn_edoc' =>$orderEdoc,
            'tn_status' => 1,
            );
        DB::table('TBL_TRAINING_NOMINATION')->where('TN_ID', '=', $id)->update($departmentArray);
        Session::flash('success', 'Employee Nomination  updated successfully.');
        return Redirect('/training_course/'.$request->input('tc_id'));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $data = Emp_Nomination::find($id);
        Emp_Nomination::where('tn_id', '=', $id)->delete();
        Session::flash('success', 'Employee Nomination has been deleted successfully.');
        return Redirect('/training_course/'.$data->tc_id);
    }
}
