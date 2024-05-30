<?php

namespace App\Http\Controllers;
use App\Models\Treatment;
use App\Models\EmpController;
use App\Models\Employees\Employees;
use Illuminate\Http\Request;
use DB;
use Session;
use Validator;
use Input;

class TreatmentController extends Controller
{

    public function _construct()
    {
        $this->middleware('auth');
    }
   

    public function index(){
		$page_title = 'Treatments';
        $treat = DB::table('tbl_treatment')->orderBy('id', 'desc')->get();
        return view('treatment.index', compact('treat','page_title'));
        
    }


    public function create(){ 
        $page_title= 'Treatment';
        $category = ['' => 'Select Category', 'Outdoor' => 'Outdoor', 'Indoor' => 'Indoor'];
        $type = ['' => 'Select Type', 'Emergency' => 'Emergency', 'Major' => 'Major', 'Major' => 'Minor', 'Prolonged Chronic Disease' => 'Prolonged Chronic Disease' ,'Consultation' => 'Consultation', 'Lab Investigation' => 'Lab Investigation', 'Labs' => 'Labs' ];
        // $emp = ['' => 'Select Employee'];
        // $employees =Employees::orderBy('emp_id', 'DESC')->orderBy('emp_name', 'ASC')->get();
        // foreach($employees as $key => $row)
        //     $emp[$row->emp_id] = $row->emp_name .' ( '.$row->cnic . ' )';
        return view('treatment.create', compact('page_title', 'category', 'type'));
    } 

    public function store(Request $request)
    {
        $messages = array(
            'required' => 'The Created Date field is required.',
           
        );

        $validator = Validator::make($request->all(), [

            'created' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());

        $Treatment = Treatment::orderBy('id', 'desc')->first();
        $treatments = new Treatment();
        $treatments->id = ($Treatment) ? $Treatment->id + 1 : 1;
        $treatments->category = $request->input('category');
        $treatments->type = $request->input('type');
        $treatments->disease = $request->input('disease');
        $treatments->modified_by = 0;
        $treatments->admin_del = 1;
        $treatments->created = ($request->input('created'))? date('Y-m-d',strtotime($request->input('created'))) : '';
        $treatments->created_by = 1;
        // echo "<pre>";
        // print_r($treatments);
        // die;

        $treatments->save();
        Session::flash('success', 'Treatment Slip created successfully.');
        return redirect('treatment');
    }
    public function edit($id){
        $page_title= 'Treatment';
        $category = ['' => 'Select Category', 'Outdoor' => 'Outdoor', 'Indoor' => 'Indoor'];
        $type = ['' => 'Select Type', 'Emergency' => 'Emergency', 'Major' => 'Major', 'Major' => 'Minor', 'Prolonged Chronic Disease' => 'Prolonged Chronic Disease' ,'Consultation' => 'Consultation', 'Lab Investigation' => 'Lab Investigation', 'Labs' => 'Labs' ];
        $treat = Treatment::find($id);
        // $emp = ['' => 'Select Employee'];
        // $employees =Employees::orderBy('emp_id', 'DESC')->orderBy('emp_name', 'ASC')->get();
        // foreach($employees as $key => $row)
        //     $emp[$row->emp_id] = $row->emp_name .' ('.$row->cnic . ' )' ;
        return view('treatment.edit', compact('page_title', 'category', 'type', 'treat')); 
    }
    public function update(Request $request, $id)
    {

        $messages = array(
            'required' => 'The Created Date field is required.',
           
        );

        $validator = Validator::make($request->all(), [

            'created' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());


            $TreatmentArray = array(
                        'category' => $request->input('category'),
                        'type' => $request->input('type'),
                        'disease' => $request->input('disease'),
                        'created' => ($request->input('created'))? date('Y-m-d',strtotime($request->input('created'))) : '',
                    );
                    DB::table('TBL_TREATMENT')->where('id', '=', $id)->update($TreatmentArray);
                        Session::flash('success', 'Treatment Info updated successfully.');
                        return Redirect('treatment');
    }

    public function show($id){ 
        $page_title = 'Show Treatment Info ';
        $treat = Treatment::find($id);
        return view('Treatment.show', compact('treat', 'page_title'));
        
       // $medical = MedicalBook::find($id);
        
        
        // echo "<pre>";
        // print_r($emp);
        // die;
        
    }

    public function destroy($id)
    {
        DB::table('TBL_TREATMENT')->where('id', '=', $id)->delete();
        Session::flash('success', ' Treatment Info has been deleted successfully.');
        return redirect('treatment');
    }

}
