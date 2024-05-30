<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Validator;
use Input;

class OrderController extends Controller
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
        $page_title = 'Order';
        $data = DB::table('V_ORDER')->orderBy('order_id', 'DESC')->get();
      
		$tot_entries = DB::table('V_ORDER')->sum('entries');
		$tot_effectees = DB::table('V_ORDER')->sum('effected_person');
        //$data = Order::all();

        return view('orders.index', compact('page_title', 'data','tot_entries','tot_effectees'));
    }
	
	 public function order_detail($id){
   //echo "test";die;
        $emp = DB::table('v_order')->where('order_id',$id )->first();
        $promotion = DB::table('TBL_CARRIER')->join('TBL_EMP', 'TBL_CARRIER.EMP_ID', '=', 'TBL_EMP.EMP_ID')->where('TBL_CARRIER.ORDER_ID', $id)
            ->where('CHARGE_ID', '>=', 100)->where('CHARGE_ID', '<', 199)
            ->select('TBL_CARRIER.emp_id as emp_id', 'TBL_EMP.emp_name as emp_name', 'TBL_CARRIER.post_name as post_name', 'TBL_EMP.bs as bs',
                'TBL_EMP.bs as bs','TBL_CARRIER.charge_id as charge_id','TBL_CARRIER.place_id as place_id', 'TBL_EMP.place_of_posting as place_of_posting',
                'TBL_EMP.section as section','TBL_CARRIER.SANCTION_ID as sanction_id','TBL_CARRIER.joining_date as joining_date','TBL_CARRIER.order_id as order_id',
                'TBL_CARRIER.CARRIER_ID as carrier_id')->get();
        $posting = DB::table('TBL_CARRIER')->join('TBL_EMP', 'TBL_CARRIER.EMP_ID', '=', 'TBL_EMP.EMP_ID')->where('TBL_CARRIER.ORDER_ID', $id)
            ->where('CHARGE_ID', '>=', 200)->where('CHARGE_ID', '<', 299)
            ->select('TBL_CARRIER.emp_id as emp_id', 'TBL_EMP.emp_name as emp_name', 'TBL_CARRIER.post_name as post_name', 'TBL_EMP.bs as bs',
                'TBL_EMP.bs as bs','TBL_CARRIER.charge_id as charge_id','TBL_CARRIER.place_id as place_id', 'TBL_EMP.place_of_posting as place_of_posting',
                'TBL_EMP.section as section','TBL_CARRIER.SANCTION_ID as sanction_id','TBL_CARRIER.joining_date as joining_date','TBL_CARRIER.order_id as order_id',
                'TBL_CARRIER.CARRIER_ID as carrier_id')->get();
        $misc = DB::table('TBL_CARRIER')->join('TBL_EMP', 'TBL_CARRIER.EMP_ID', '=', 'TBL_EMP.EMP_ID')->where('TBL_CARRIER.ORDER_ID', $id)
            ->where('CHARGE_ID', '>=', 300)->where('CHARGE_ID', '<', 399)
            ->select('TBL_CARRIER.emp_id as emp_id', 'TBL_EMP.emp_name as emp_name', 'TBL_CARRIER.post_name as post_name', 'TBL_EMP.bs as bs',
                'TBL_EMP.bs as bs','TBL_CARRIER.charge_id as charge_id','TBL_CARRIER.place_id as place_id', 'TBL_EMP.place_of_posting as place_of_posting',
                'TBL_EMP.section as section','TBL_CARRIER.SANCTION_ID as sanction_id','TBL_CARRIER.joining_date as joining_date','TBL_CARRIER.order_id as order_id',
                'TBL_CARRIER.CARRIER_ID as carrier_id')->get();
        //echo "<pre>"; print_r($misc); die;
        $carrier = DB::table('TBL_CARRIER')->join('TBL_EMP', 'TBL_CARRIER.EMP_ID', '=', 'TBL_EMP.EMP_ID')->where('TBL_CARRIER.ORDER_ID', $id)
            ->where('CHARGE_ID', '>=', 400)->where('CHARGE_ID', '<', 499)
               ->select('TBL_CARRIER.emp_id as emp_id', 'TBL_EMP.emp_name as emp_name', 'TBL_CARRIER.post_name as post_name', 'TBL_EMP.bs as bs',
                'TBL_EMP.bs as bs','TBL_CARRIER.charge_id as charge_id','TBL_CARRIER.place_id as place_id', 'TBL_EMP.place_of_posting as place_of_posting',
                'TBL_EMP.section as section','TBL_CARRIER.SANCTION_ID as sanction_id','TBL_CARRIER.joining_date as joining_date','TBL_CARRIER.order_id as order_id',
                   'TBL_CARRIER.CARRIER_ID as carrier_id')->get();

       // echo "<pre>"; print_r($carrier); die;


       // return view('employees.emp_detail', compact('emp', 'appoint','family','eduction','experience','carrier','reward','leave','noc','extension', 'promotion','posting', 'misc'));
          return view('orders.order_detail', compact('emp', 'promotion', 'posting', 'misc', 'carrier'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add Order';
        //// Get Sections
        $sections = DB::table('TBL_SECTION')->orderBy('section_id', 'ASC')->get();

        $section_type = array(null => 'Select Section');
        foreach($sections as $section){
            $section_type[$section->section_id] = $section->section_name;
        }
        //// GET App Authority
        $authorities = DB::table('TBL_APP_AUTHORITY')->orderBy('aa_id', 'ASC')->get();

        $app_auth = array(null => 'Select Authority');
        foreach($authorities as $authorty){
            $app_auth[$authorty->aa_id] = $authorty->aa_name; //. ' ' . $package->contract_code;
        }

        $orderTypes = ['' => 'Select Order Type', '1' => 'Order', '2' => 'Circular', '3' => 'Letter', '4' => 'Noting'];

        return view('orders.create', compact('page_title','orderTypes','section_type','app_auth'));
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

            'order_no' => 'required',
            'order_subject' => 'required',
            'order_date' => 'required',
            'aa_id' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
		
		$ord = Order::orderBy('order_id', 'desc')->first();

        $order = new Order();
        $order->order_id = ($ord) ? $ord->order_id + 1 : 1;
        $order->order_section_id = $request->input('order_section_id');
        $order->order_subject = $request->input('order_subject');
        $order->order_no = $request->input('order_no');
        $order->order_date = ($request->input('order_date')) ? date('Y-m-d', strtotime($request->input('order_date'))) : null;
        $order->order_type = $request->input('order_type');
        $order->effected_person = $request->input('effected_person');
        $order->aa_id = $request->input('aa_id');
		
		if($request->hasFile('e_doc')) {
            $file = $request->file('e_doc');            


            /// new file name
            $new_filename = 'Order_'.$order->order_id;

            $path = 'public/NHA-IS/ORDER';

            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);

            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $order->e_doc = $completeUrl;

        }
		
        $order->save();
        //after order save
        /*if(Input::hasFile('e_doc')) {
            $path = 'public/NHA-IS/ORDER';
            $table = 'TBL_ORDER';
            $primary_field = 'ORDER_ID';
            $file = Input::file('e_doc');
            $edoc_field = 'E_DOC ';

            $uploader = new UploadController();
            $uploader->upload_edoc($file, 'ORDER_'.$order->order_id, $path, $table, $primary_field, $order->order_id, $edoc_field);
        }*/
        Session::flash('success', 'Order created successfully.');
        return redirect('order');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Order';
        $data = Order::find($id);

        $section = DB::table('TBL_SECTION')->where('section_id', '=',$data->order_section_id)->first();
        $app_auth = DB::table('TBL_APP_AUTHORITY')->where('aa_id', '=',$data->aa_id)->first();
       // dd($section);

        return view('orders.show', compact('page_title', 'data','section','app_auth'));
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
        $data = Order::find($id);

        //// Get Sections
        $sections = DB::table('TBL_SECTION')->orderBy('section_id', 'ASC')->get();

        $section_type = array(null => 'Select Section');
        foreach($sections as $section){
            $section_type[$section->section_id] = $section->section_name; //. ' ' . $package->contract_code;
        }
        //// GET App Authority
        $authorities = DB::table('TBL_APP_AUTHORITY')->orderBy('aa_id', 'ASC')->get();

        $app_auth = array(null => 'Select Authority');
        foreach($authorities as $authorty){
            $app_auth[$authorty->aa_id] = $authorty->aa_name; //. ' ' . $package->contract_code;
        }

        $orderTypes = ['' => 'Select Order Type', '1' => 'Order', '2' => 'Circular', '3' => 'Letter', '4' => 'Noting'];

        return view('orders.edit', compact('page_title','data','orderTypes','section_type','app_auth'));

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

            'order_no' => 'required',
            'order_subject' => 'required',
            'order_date' => 'required',
            'aa_id' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());



        $order = Order::find($id);
        $orderSecID = $request->input('order_section_id');
        $orderSub = $request->input('order_subject');
        $orderNO = $request->input('order_no');
        $orderDate = ($request->input('order_date')) ? date('Y-m-d', strtotime($request->input('order_date'))) : null;
        $orderType= $request->input('order_type');
        $orderEffPer = $request->input('effected_person');
        $orderAAID = $request->input('aa_id');

        
		if($request->hasFile('e_doc')) {
            $file = $request->file('e_doc');            


            /// new file name
            $new_filename = 'Order_'.$id;

            $path = 'public/NHA-IS/ORDER';

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

        $updateFields = array(
            'order_section_id' => $orderSecID,
            'order_subject' => $orderSub,
            'order_no' => $orderNO,
            'order_date' => $orderDate,
            'order_type' => $orderType,
            'effected_person' => $orderEffPer,
            'aa_id' => $orderAAID,
            'e_doc' => $orderEdoc
        );

        DB::table('TBL_ORDER')->where('order_id', '=', $id)->update($updateFields);


        //after order save
       /* if(Input::hasFile('e_doc')) {
            $path = 'public/NHA-IS/ORDER';
            $table = 'TBL_ORDER';
            $primary_field = 'ORDER_ID';
            $file = Input::file('e_doc');
            $edoc_field = 'E_DOC ';

            $uploader = new UploadController();
            $uploader->upload_edoc($file, 'ORDER_'.$order->order_id, $path, $table, $primary_field, $order->order_id, $edoc_field);
        }*/
        Session::flash('success', 'Order updated successfully.');
        return redirect('order');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         DB::table('TBL_CARRIER')->where('order_id', '=', $id)->delete();
		 Order::where('order_id', '=', $id)->delete();
        Session::flash('success', 'Order has been deleted successfully.');

        return redirect('order');
    }
}
