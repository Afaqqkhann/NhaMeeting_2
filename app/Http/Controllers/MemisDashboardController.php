<?php


namespace App\Http\Controllers;
use App\Models\MedicalBill;
use App\Models\PanelController;
use App\Models\Panel;
use Illuminate\Http\Request;
use DB;
use App\User;
use Session;
use Validator;
use Input;

class MemisDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($reportType,$user)
    {       
		$user = urldecode($user); 
		$user_info = User::where('name','=',$user)->first();
		if($reportType == 'Employees'){					
			$page_title = 'Employees';
			$data = DB::table('V_FAMILY_FINALIZIED_EMP')			
			   ->where('user_id','=',$user_info->id) 
			   ->orderBy('f_emp_id', 'DESC')
			   ->distinct('emp_id')
				->get();
			
		}else if($reportType == 'Dependents'){
			$page_title = 'Dependents';
			$data = DB::table('V_FAMILY_FINALIZIED')
				->join('TBL_RELATION','V_FAMILY_FINALIZIED.relationship','=','TBL_RELATION.r_id')
			   ->where('user_id','=',$user_info->id) 
			   ->orderBy('family_id', 'DESC')
			   ->distinct('emp_id')
				->get();
				
				
			return view('employees.employee_dependents_medical_list',compact('page_title','data'));	
		}
		
       // echo "<pre>";
      // print_r($data); die;


        return view('employees.employees_medical_list', compact('page_title', 'data'));
    }
	
	

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add MedicalBill';
        $panel_title = ['' => 'Select Panel Title'];
        $panel =Panel::orderBy('panel_id', 'DESC')->orderBy('panel_title', 'ASC')->get();
        foreach($panel as $key => $row)
            $panel_title[$row->panel_id] = $row->panel_title;
        return view('medicalbill.create', compact('page_title', 'panel_title'));
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
                //'panel_title'           => 'required',
                'panel_bill_date'  => 	'required',
                'panel_bill_no'  => 	'required',
                'bill_tt_amt' => 'required|integer',


                //'duration_days' =>  'required',
            'bill_edoc'	    =>	'mimes:jpeg,bmp,png,jpg,xlsx,pdf,html|max:1000',
            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $Medicalbill = MedicalBill::orderBy('bill_id', 'desc')->first();
        $medicalsbill = new MedicalBill();
        $medicalsbill->bill_id = ($Medicalbill) ? $Medicalbill->bill_id + 1 : 1;
        $medicalsbill->panel_bill_no = $request->input('panel_bill_no');
        $medicalsbill->panel_id = $request->input('panel_id');
        $medicalsbill->panel_bill_date = ($request->input('panel_bill_date'))? date('Y-m-d',strtotime($request->input('panel_bill_date'))) : '';
        $medicalsbill->bill_tt_amt = $request->input('bill_tt_amt');
        $medicalsbill->bill_edoc = $request->input('bill_edoc');
        $id = $medicalsbill->bill_id;
        if($request->hasFile('bill_edoc')) {
            $file = $request->file('bill_edoc');
            $new_filename = 'bill_edoc_' . $id;
            $path = 'public/medical_bill';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $medicalsbill->bill_edoc = $completeUrl;
        }
        // $medicalsbill->mb_status = 1;
        $medicalsbill->save();
        Session::flash('success', 'Medical Bill created successfully.');
        return redirect('medical');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   
  
    public function show($id){ 
        $medical = MedicalBill::find($id);
        $page_title = "Medical Bill Show";
        $data = DB::table('TBL_Medical_Bill')->orderBy('bill_id', 'DESC')
        ->join('TBL_Panel', 'TBL_Medical_Bill.panel_id', '=', 'TBL_Panel.panel_id')
        ->where('TBL_Medical_Bill.bill_id', '=', $id)
        ->first();
        return view('medicalbill.show', compact('medical', 'page_title','data'));
    }

   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id){
        $page_title= 'Medical Bill';
        $medical = MedicalBill::find($id);
        $panel_title = ['' => 'Select Panel Title'];
        $panel =Panel::orderBy('panel_id', 'DESC')->orderBy('panel_title', 'ASC')->get();
        foreach($panel as $key => $row)
            $panel_title[$row->panel_id] = $row->panel_title;
        return view('medicalbill.edit', compact('page_title', 'panel_title', 'medical'));
    }
   
    public function update(Request $request, $id)
    {

        $messages = array(
            'required' => 'The Medical Bill ID field is required.',
           
        );

        $validator = Validator::make($request->all(), [

            'panel_bill_no' => 'required',

        ], $messages);

        if($validator->fails()){
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }
         
			 $user = MedicalBill::find($id);
             if($request->hasFile('bill_edoc')) {
                 $file = $request->file('bill_edoc');
                 $new_filename = 'bill_edoc'. $user->bill_id;
                 $path = 'public/medical_bill';
                 $path = str_replace('&', '_', $path);
                 $extension = $file->getClientOriginalExtension();
                 $file->move($path, $new_filename . '.' . $extension);
                 $completeUrl = $path . '/' . $new_filename . '.' . $extension;
                 $medImg = $completeUrl;
               }
               else
                 {
                 $medImg = $user->bill_edoc;
                 }
                  $user->bill_edoc = $medImg;


            $MedicalArray = array(
                        'panel_bill_no' => $request->input('panel_bill_no'),
                        'panel_id' => $request->input('panel_id'),
                        'panel_bill_date' => ($request->input('panel_bill_date'))? date('Y-m-d',strtotime($request->input('panel_bill_date'))) : '',
                        'bill_tt_amt' => $request->input('bill_tt_amt'),
                        'bill_edoc'=> $user->bill_edoc,
                    );
                    DB::table('TBL_MEDICAL_Bill')->where('bill_id', '=', $id)->update($MedicalArray);
                        Session::flash('success', 'Medical Bill updated successfully.');
                        return Redirect('medical');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         DB::table('TBL_MEDICAL_BILL')->where('bill_id', '=', $id)->delete();

        Session::flash('success', 'Medical Bill has been deleted successfully.');

        return redirect('medical');
    }
}
