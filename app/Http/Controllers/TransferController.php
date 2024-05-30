<?php

namespace App\Http\Controllers;

use App\Models\Cadre;
use App\Models\Carrier;
use App\Models\Charge;
use App\Models\Desig;
use App\Models\Education;
use App\Models\Emp;
use App\Models\Employees\Employees;
use App\Models\Experience;
use App\Models\Extension;
use App\Models\Family;
use App\Models\Order;
use App\Models\Place;
use App\Models\Post;
use App\Models\Relation;
use App\Models\Sanction;
use App\Models\Section;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Validator;
use Input;

class TransferController extends Controller
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
    public function create($id)
    {
        $page_title = 'Add Transfer';
        $reporting_officer = ['' => 'Select Reporting Officer'] + Emp::lists('emp_name', 'emp_id')->all();
        $order = ['' => 'Select Order'] + Order::lists('order_subject', 'order_id')->all();
        $post = ['' => 'Select Post'] + Post::lists('post_name', 'post_name')->all();
        $place = ['' => 'Select Place'] + Place::lists('place_title', 'place_id')->all();
        $charge = array(null => 'Select Charge');
        $emp = DB::table('TBL_CHARGE')->where('charge_id','>=', 200)->where('charge_id','<=', 299)->orderBy('charge_id','DESC')->get();
        foreach($emp as $row){
            $charge[$row->charge_id] = $row->charge_title;//.'-'. $row->designation;
        }
        $sanction = ['' => 'Select Sanction'] + Sanction::lists('strength_name', 'sanction_id')->all();
        $current_status = [''=> 'Select Station Status','Releived' => 'Releived','Releiving Awaited' => 'Releiving Awaited'];
        $posting_status = [''=> 'Select Posting Status','Joined' => 'Joined','Joining Awaited' => 'Joining Awaited','Abeyance'=>'Abeyance', 'Cancelled'=>'Cancelled'];
        return view('carrier.create', compact('page_title', 'id', 'post', 'charge','sanction','place','current_status','posting_status','order',
            'reporting_officer' ));
    }
	   public function create1($id)
    {
        $page_title = 'Add Transfer Order';
        $reporting_officer = ['' => 'Select Reporting Officer'] + Emp::lists('emp_name', 'emp_id')->all();
        $order_emp = ['' => 'Select Employee'] +Emp::lists('emp_name', 'emp_id')->all();
        $post = ['' => 'Select Post'] + Post::lists('post_name', 'post_name')->all();
        $place = ['' => 'Select Place'] + Place::lists('place_title', 'place_id')->all();
        $charge = array(null => 'Select Charge');
        $emp = DB::table('TBL_CHARGE')->where('charge_id','>=', 200)->where('charge_id','<=', 299)->orderBy('charge_id','DESC')->get();
        foreach($emp as $row){
            $charge[$row->charge_id] = $row->charge_title;//.'-'. $row->designation;
        }
        $sanction = ['' => 'Select Sanction'] + Sanction::lists('strength_name', 'sanction_id')->all();
        $current_status = [''=> 'Select Station Status','Releived' => 'Releived','Releiving Awaited' => 'Releiving Awaited'];
        $posting_status = [''=> 'Select Posting Status','Joined' => 'Joined','Joining Awaited' => 'Joining Awaited','Abeyance'=>'Abeyance', 'Cancelled'=>'Cancelled'];
        return view('carrier.transfar_create', compact('page_title', 'id', 'post', 'charge','sanction','place','current_status','posting_status','order_emp',
            'reporting_officer' ));
    }
    public function store1(Request $request)
    {
        $validation = Validator::make($request->all(),
            [
//                'e_from'  => 	'required',
//                'e_to'  => 	'required',
            ]);
        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $record = Carrier::orderBy('carrier_id', 'desc')->first();
        $book = new Carrier();
        $book->carrier_id = ($record) ? $record->carrier_id + 1 : 1;
        $book->order_id = $request->input('id');
        $book->post_name = $request->input('post_name');
        $book->charge_id = $request->input('charge');
        $book->reporting_off_id = $request->input('report_off');
        $book->sanction_id = $request->input('sanction');
        $book->place_id = $request->input('place');
        $book->emp_id = $request->input('order_emp');
        $book->remarks = $request->input('remarks');
        $book->station_status= $request->input('current_status');
        $book->relieving_date = ($request->input('current_date')) ? date('Y-m-d', strtotime(str_replace('/', '-',$request->input('current_date')))) : '';
        $book->posting_status= $request->input('posting_status');
        $book->joining_date= ($request->input('post_date')) ? date('Y-m-d', strtotime(str_replace('/', '-',$request->input('post_date')))) : '';
        $book->carrier_status = 0;
        $book->save();
        Session::flash('success', 'Transfer added successfully.');
        return Redirect('order/order_detail/'.$request->input('id'));
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
//                'e_from'  => 	'required',
//                'e_to'  => 	'required',
            ]);
        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $record = Carrier::orderBy('carrier_id', 'desc')->first();
        $book = new Carrier();
        $book->carrier_id = ($record) ? $record->carrier_id + 1 : 1;
        $book->emp_id = $request->input('id');
        $book->post_name = $request->input('post_name');
        $book->charge_id = $request->input('charge');
        $book->reporting_off_id = $request->input('report_off');
        $book->sanction_id = $request->input('sanction');
        $book->place_id = $request->input('place');
        $book->order_id = $request->input('order');
        $book->remarks = $request->input('remarks');
        $book->station_status= $request->input('current_status');
        $book->relieving_date = ($request->input('current_date')) ? date('Y-m-d', strtotime(str_replace('/', '-',$request->input('current_date')))) : '';
        $book->posting_status= $request->input('posting_status');
        $book->joining_date= ($request->input('post_date')) ? date('Y-m-d', strtotime(str_replace('/', '-',$request->input('post_date')))) : '';
        $book->carrier_status = 0;
        $book->save();
        Session::flash('success', 'Transfer added successfully.');
        return Redirect('employee/emp_detail/'.$request->input('id'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Transfer';
        $data = Carrier::find($id);
        return view('carrier.show', compact('page_title','data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Transfer';
        $data = Carrier::find($id);
        $reporting_officer = ['' => 'Select Reporting Officer'] + Emp::lists('emp_name', 'emp_id')->all();
        $order = ['' => 'Select Order'] + Order::lists('order_subject', 'order_id')->all();
        $post = ['' => 'Select Post'] + Post::lists('post_name', 'post_name')->all();
        $place = ['' => 'Select Place'] + Place::lists('place_title', 'place_id')->all();
        $charge = array(null => 'Select Charge');
        $emp = DB::table('TBL_CHARGE')->where('charge_id','>=', 200)->where('charge_id','<=', 299)->orderBy('charge_id','DESC')->get();
        foreach($emp as $row){
            $charge[$row->charge_id] = $row->charge_title;//.'-'. $row->designation;
        }
        $sanction = ['' => 'Select Sanction'] + Sanction::lists('strength_name', 'sanction_id')->all();
        $current_status = [''=> 'Select Station Status','Releived' => 'Releived','Releiving Awaited' => 'Releiving Awaited'];
        $posting_status = [''=> 'Select Posting Status','Joined' => 'Joined','Joining Awaited' => 'Joining Awaited','Abeyance'=>'Abeyance', 'Cancelled'=>'Cancelled'];
        return view('carrier.edit', compact('page_title','data' ,'id', 'post', 'charge','sanction','place','current_status','posting_status','order',
            'reporting_officer' ));

    }
	public function edit1($id)
    {
        $page_title = 'Transfer Edit';
        $data = Carrier::find($id);
        // echo "<pre>"; print_r($data);die;
        $reporting_officer = ['' => 'Select Reporting Officer'] + Emp::lists('emp_name', 'emp_id')->all();
        $order = ['' => 'Select Employee'] + Emp::lists('emp_name', 'emp_id')->all();
        $post = ['' => 'Select Post'] + Post::lists('post_name', 'post_name')->all();
        $place = ['' => 'Select Place'] + Place::lists('place_title', 'place_id')->all();
        $charge = array(null => 'Select Charge');
        $emp = DB::table('TBL_CHARGE')->where('charge_id','>=', 200)->where('charge_id','<=', 299)->orderBy('charge_id','DESC')->get();
        foreach($emp as $row){
            $charge[$row->charge_id] = $row->charge_title;//.'-'. $row->designation;sssss
        }
        $sanction = ['' => 'Select Sanction'] + Sanction::lists('strength_name', 'sanction_id')->all();
        $current_status = [''=> 'Select Station Status','Releived' => 'Releived','Releiving Awaited' => 'Releiving Awaited'];
        $posting_status = [''=> 'Select Posting Status','Joined' => 'Joined','Joining Awaited' => 'Joining Awaited','Abeyance'=>'Abeyance', 'Cancelled'=>'Cancelled'];
        return view('promotion.transfer_edit', compact('page_title','data' ,'id', 'post', 'charge','sanction','place','current_status','posting_status','order',
            'reporting_officer' ));

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

//            'e_from.required' => 'The Extension From field is required.',
//            'e_to.required' => 'The Extension To field is required.',
        ];
        $validation = Validator::make($request->all(),
            [
//                'e_from'  => 	'required',
//                'e_to'  => 	'required',
            ],$messages);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $departmentArray = array(
            'post_name' => $request->input('post_name'),
            'order_id' => $request->input('id'),
            'charge_id' => $request->input('charge_name'),
            'reporting_off_id' => $request->input('report_officer'),
            'sanction_id' => $request->input('sanction'),
            'place_id' => $request->input('place'),
            'emp_id' => $request->input('order'),
            'station_status' => $request->input('current_status'),
            'relieving_date' => $request->input('current_date'),
            'posting_status' => $request->input('post_status'),
            'joining_date' => $request->input('post_date'),
            'remarks' => $request->input('remarks'),
            'carrier_status' => 0,
            );
        DB::table('TBL_CARRIER')->where('CARRIER_ID', '=', $id)->update($departmentArray);
        Session::flash('success', 'Transfer updated successfully.');
        return Redirect('order/order_detail/'.$request->input('id'));

		}
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Carrier::find($id);
        DB::table('TBL_CARRIER')->where('CARRIER_ID', '=', $id)->delete();
        Session::flash('success', 'Transfer has been deleted successfully.');
        return Redirect('employee/emp_detail/'.$data->emp_id);
    }
}
