<?php

namespace App\Http\Controllers;

use App\Models\Cadre;
use App\Models\Desig;
use App\Models\Education;
use App\Models\Employees\Employees;
use App\Models\Experience;
use App\Models\Extension;
use App\Models\Family;
use App\Models\Noc;
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

class NocController extends Controller
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
    public function create($id)
    {
        $page_title = 'Add Noc';
        $noctype = [''=> 'Select NOC Type','Passport' => 'Passport','Study Admission' => 'Study Admission','Job' => 'Job','Arms License' => 'Arms License'];
        $order = ['' => 'Select Order'] + Order::lists('order_subject', 'order_id')->all();
        return view('noc.create', compact('page_title', 'id', 'order','noctype'));
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
                'order' => 'numeric'
                
            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
		$record = Noc::orderBy('noc_id', 'desc')->first();
        $book = new Noc();

        $book->noc_id = ($record) ? $record->noc_id + 1 : 1;
        $book->emp_id = $request->input('id');
        $book->noc_type = $request->input('noc_type');
		
       // $book->approval_date = ($request->input('approval_date')) ? date('Y-m-d', strtotime($request->input('approval_date'))) : '';
       // $book->application_date = ($request->input('app_date')) ? date('Y-m-d', strtotime($request->input('app_date'))) : '';
        $book->issue_date = ($request->input('issue_date')) ? date('Y-m-d', strtotime($request->input('issue_date'))) : '';
        $book->noc_file_no = $request->input('file');
        $book->order_id = $request->input('order');
        $book->noc_status = 1;
        $book->save();
        Session::flash('success', 'Noc added successfully.');
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
        $page_title = 'Noc';
        $data = Noc::find($id);
        //echo "<pre>"; print_r($data); die;
        return view('noc.show', compact('page_title','data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Noc';
        $data = Noc::find($id);
        $noctype = [''=> 'Select NOC Type','Passport' => 'Passport','Study Admission' => 'Study Admission','Job' => 'Job','Arms License' => 'Arms License'];
        $order = ['' => 'Select Order'] + Order::lists('order_subject', 'order_id')->all();
        return view('noc.edit', compact('page_title','data','order','noctype'));
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
			'order.numeric' => 'The Order ID must be number.'

        ];
        $validation = Validator::make($request->all(),
            [
				'order' => 'numeric'
            ],$messages);
			
			

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
		
		//$application_date = ($request->input('application_date')) ? date('Y-m-d', strtotime($request->input('application_date'))) : '';
		$issue_date = ($request->input('issue_date')) ? date('Y-m-d', strtotime($request->input('issue_date'))) : '';
		//$approval_date = ($request->input('approval_date')) ? date('Y-m-d', strtotime($request->input('approval_date'))) : '';
       
		$nocArray = array(
            'noc_type' => $request->input('noc_type'),
            'emp_id' => $request->input('id'),
            //'application_date' => $application_date,
            //'approval_date' => $approval_date,
            'issue_date' => $issue_date,
            'noc_file_no' => $request->input('file'),
            'order_id' => $request->input('order'),
            'noc_status' => 1,
            );
        DB::table('TBL_NOC')->where('NOC_ID', '=', $id)->update($nocArray);
        Session::flash('success', 'Noc updated successfully.');
        return Redirect('employee/emp_detail/'.$request->input('id'));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Noc::find($id);
        DB::table('TBL_NOC')->where('NOC_ID', '=', $id)->delete();
        Session::flash('success', 'Noc has been deleted successfully.');
        return Redirect('employee/emp_detail/'.$data->emp_id);
    }
}
