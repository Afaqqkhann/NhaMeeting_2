<?php

namespace App\Http\Controllers;
use App\Models\Penalty;
use App\Models\EmpController;
use App\Models\Employees\Employees;
use Illuminate\Http\Request;
use DB;
use Session;
use Validator;
use Input;

class PenaltyController extends Controller
{       

 
    public function index(){
        $page_title = 'Employee Penalties';
        $pen = DB::table('TBL_PENALTIE')->orderBy('PENALITIE_ID', 'DESC')
            ->join('TBL_EMP', 'TBL_PENALTIE.emp_id', '=', 'TBL_EMP.emp_id')
            ->get();

        return view('penalty.index', compact('page_title' , 'pen'));
        
    }
  
    public function create($id){ 
        $page_title= 'Employee Penalty'; 
        $pen = Penalty::all();
        $emp = ['' => 'Select Employee']; 
        $status = (isset($_POST['status']) == '1' ? '1' : '0');
        $employees =Employees::orderBy('emp_id', 'DESC')->orderBy('emp_name', 'ASC')->get();
        foreach($employees as $key => $row)
            $emp[$row->emp_id] = $row->emp_name .' ( '.$row->cnic . ' )';
        return view('penalty.create', compact('page_title', 'emp', 'status', 'id'));
    }

    public function store(Request $request)
    {      
       
        $messages = array(
            'emp_id' => 'Employee Name is required.',
            
        );
        $validator = Validator::make($request->all(), [
            'emp_id' => 'required',
        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());

        $Penalty = Penalty::orderBy('penalitie_id', 'desc')->first();
        $penalities = new Penalty();
        $penalities->penalitie_id = ($Penalty) ? $Penalty->penalitie_id + 1 : 1;
        $penalities->emp_id = $request->input('emp_id');
        $penalities->allegation = $request->input('allegation');
        $penalities->nature_of_penalty = $request->input('nature_of_penalty');
        $penalities->off_order_no = $request->input('off_order_no');
        $penalities->app_auth = $request->input('app_auth');
        $penalities->remarks = $request->input('remarks');
        $penalities->penalitie_status = $request->input('status');
       
        $penalities->off_order_date = ($request->input('off_order_date'))? date('Y-m-d',strtotime($request->input('off_order_date'))) : '';
        //echo 'order date'.$penalities->off_order_date;die;
        $penalities->save();
        Session::flash('success', 'Employee Penalty created successfully.');
        return redirect('employee/emp_detail'.'/'.$penalities->emp_id);
    }
    public function edit($id){
        $page_title= 'Employee Penalty';
        $pen = Penalty::find($id);
        $emp = ['' => 'Select Employee'];
        $employees =Employees::orderBy('emp_id', 'DESC')->orderBy('emp_name', 'ASC')->get();
        foreach($employees as $key => $row)
            $emp[$row->emp_id] = $row->emp_name .' ('.$row->cnic . ' )' ;
        return view('penalty.edit', compact('page_title', 'emp', 'pen'));
    }
    public function update(Request $request, $id)
    {

        $messages = array(
            'emp_id' => 'Employee Name field is required.',
           
        );

        $validator = Validator::make($request->all(), [

            'emp_id' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());


            $PenaltyArray = array(
                        'emp_id' => $request->input('emp_id'),
                        'allegation' => $request->input('allegation'),
                        'nature_of_penalty' => $request->input('nature_of_penalty'),
                        'off_order_no' => $request->input('off_order_no'),
                        'app_auth' => $request->input('app_auth'),
                        'remarks' => $request->input('remarks'),
                        'penalitie_status' => $request->input('status'),
                        'off_order_date' => ($request->input('off_order_date'))? date('Y-m-d',strtotime($request->input('off_order_date'))) : '',
                    );
                    DB::table('TBL_PENALTIE')->where('penalitie_id', '=', $id)->update($PenaltyArray);
                        Session::flash('success', 'Employee Penalty updated successfully.');
                        return Redirect('employee/emp_detail'.'/'.$request->emp_id);
    }

    public function show($id){ 
        $page_title = 'Employee Penalty';
        $pen = DB::table('TBL_PENALTIE')->where('penalitie_id', $id)
        ->join('TBL_EMP', 'TBL_PENALTIE.emp_id', '=', 'TBL_EMP.emp_id')
        ->get();

        return view('penalty.show', compact('page_title', 'pen'));
    }

    public function destroy($id)
    {
        $pen = Penalty::find($id);
        $aa = DB::table('TBL_PENALTIE')->where('penalitie_id', '=', $id)->delete();
        Session::flash('success', ' Employee Penalty has deleted successfully.');
        return redirect('employee/emp_detail'.'/'.$pen->emp_id);
    }

}
