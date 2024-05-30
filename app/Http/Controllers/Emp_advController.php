<?php

namespace App\Http\Controllers;

use App\Models\Advances;
use App\Models\Cadre;
use App\Models\Desig;
use App\Models\Emp_adv;
use App\Models\Employees\Employees;
use App\Models\Family;
use App\Models\Order;
use App\Models\Post;
use App\Models\Relation;
use App\Models\Section;
use App\Models\Year;
use App\Models\Years;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Validator;
use Input;

class Emp_advController extends Controller
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
		 $eligible_amount = DB::select('SELECT ELIGIBLE_APPLICANT_AMOUNT() eligible_amount FROM dual');
		//echo "<pre>"; print_r($test) ;die;
		
		
        $page_title = 'Employee Advances';
        $data = DB::table('TBL_EMP_ADVANCES ')->orderBy('TBL_EMP_ADVANCES.EA_ID', 'DESC')
            ->join('TBL_EMP', 'TBL_EMP_ADVANCES.EMP_ID','=', 'TBL_EMP.EMP_ID')
            ->join('TBL_YEAR', 'TBL_YEAR.YEAR_ID','=', 'TBL_EMP_ADVANCES.YEAR_ID')
            ->join('TBL_ADVANCES', 'TBL_EMP_ADVANCES.adv_id', '=', 'TBL_ADVANCES.adv_id')
            ->get();
        $test = DB::table('TBL_EMP_ADVANCES ')->orderBy('TBL_EMP_ADVANCES.EA_ID', 'DESC')->get();
        foreach ($test as $tests){
            $amount = $tests->amount;
            $amount_total []=$amount;
        }
        $total = array_sum($amount_total);
        $total_eligible = DB::table('TBL_EMP_ADVANCES ')->where('eligible', '=', 1)->count();
        $non_eligible = DB::table('TBL_EMP_ADVANCES ')->where('eligible', '=', 0)->count();
        $tot_apllicant = DB::table('TBL_EMP_ADVANCES ')->count();
        return view('emp_adv.index', compact('page_title', 'data','total', 'eligible_amount','total_eligible', 'non_eligible', 'tot_apllicant'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Employee Advances';
        $emp = ['' => 'Select Employee'] + Employees::lists('emp_name', 'emp_id')->all();
        $adv = ['' => 'Select Advances'] + Advances::lists('adv_title', 'adv_id')->all();
        $year = ['' => 'Select Year'] + Years::lists('year_title', 'year_id')->all();
        $eligible = [''=> 'Select Eligible or Not','1' => 'yes','0' => 'No'];
        return view('emp_adv.create', compact('page_title','emp', 'adv', 'year', 'eligible'));
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
                'emp'  => 	'required',
                'adv'  => 	'required',
                'year'  => 	'required',
            ]);
        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $record = Emp_adv::orderBy('ea_id', 'desc')->first();
        $book = new Emp_adv();
        $book->ea_id = ($record) ? $record->ea_id + 1 : 1;
        $book->emp_id = $request->input('emp');
        $book->adv_id = $request->input('adv');
        $book->queue_no = $request->input('queue');
        $book->year_id = $request->input('year');
        $book->apply_date = ($request->input('ap_date')) ? date('Y-m-d', strtotime(str_replace('/', '-',$request->input('ap_date')))) : '';
        $book->eligible = $request->input('eligible');
        $book->comments = $request->input('comments');
        $book->amount = $request->input('amount');
     // print_r( $book->gender ); die;
		$id = $book->ea_id;
        if($request->hasFile('edoc')) {
            $file = $request->file('edoc');
            $new_filename = 'edoc_' . $id;
            $path = 'public/employee_advance';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $book->edoc = $completeUrl;
        }
        //echo  "<pre>"; print_r($book); die;
        $book->save();
        Session::flash('success', 'Employee Advances added successfully.');
        return Redirect('emp_advances');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Employee Advances';
        $data = DB::table('TBL_EMP_ADVANCES ')->orderBy('TBL_EMP_ADVANCES.EA_ID', 'DESC')
            ->join('TBL_EMP', 'TBL_EMP_ADVANCES.EMP_ID','=', 'TBL_EMP.EMP_ID')
            ->join('TBL_YEAR', 'TBL_YEAR.YEAR_ID','=', 'TBL_EMP_ADVANCES.YEAR_ID')
            ->join('TBL_ADVANCES', 'TBL_EMP_ADVANCES.adv_id', '=', 'TBL_ADVANCES.adv_id')
            ->where('TBL_EMP_ADVANCES.ea_id', '=', $id)
            ->first();

       // echo "<pre>"; print_r($data); die;
        return view('emp_adv.show', compact('page_title','data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Employee Advances';
        $data = DB::table('TBL_EMP_ADVANCES ')->orderBy('TBL_EMP_ADVANCES.EA_ID', 'DESC')
            ->where('TBL_EMP_ADVANCES.ea_id', '=', $id)
            ->first();
       //echo "<pre>"; print_r($data); die;

        $eligible = [''=> 'Select Eligible or Not','1' => 'yes','0' => 'No'];
        $emp = ['' => 'Select Employee'] + Employees::lists('emp_name', 'emp_id')->all();
        $adv = ['' => 'Select Advances'] + Advances::lists('adv_title', 'adv_id')->all();
        $year = ['' => 'Select Year'] + Years::lists('year_title', 'year_id')->all();
        return view('emp_adv.edit', compact('page_title','emp','adv','year', 'data', 'eligible'));

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
            'emp'  => 	'required',
            'adv'  => 	'required',
            'year'  => 	'required',
        ];

        $validation = Validator::make($request->all(),
            [
                'emp'  => 	'required',
                'adv'  => 	'required',
                'year'  => 	'required',
            ],$messages);


        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

			 $user = Emp_adv::find($id);
        if($request->hasFile('edoc')) {
            $file = $request->file('edoc');
            $new_filename = 'edoc_'. $user->ea_id;
            $path = 'public/employee_advance';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $empImg = $completeUrl;
          }
          else
            {
            $empImg = $user->edoc;
            }
             $user->edoc = $empImg;

        $departmentArray = array(
            'emp_id' => $request->input('emp'),
            'adv_id' => $request->input('adv'),
            'queue_no' => $request->input('queue'),
            'year_id' => $request->input('year'),
            'apply_date' => $request->input('ap_date'),
            'eligible' => $request->input('eligible'),
            'amount' => $request->input('amount'),
            'comments' => $request->input('comments'),
            'edoc'=> $user->edoc,
        );
       // echo "<pre>"; print_r($departmentArray);die;
        DB::table('TBL_EMP_ADVANCES')->where('ea_id', '=', $id)->update($departmentArray);
        Session::flash('success', 'Employee Advances updated successfully.');
        return Redirect('emp_advances');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Emp_adv::find($id);
        DB::table('TBL_EMP_ADVANCES')->where('ea_id', '=', $id)->delete();
        Session::flash('success', 'Employee Advances has been deleted successfully.');
        return Redirect('emp_advances');
    }
}
