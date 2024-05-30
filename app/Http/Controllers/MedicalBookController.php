<?php

namespace App\Http\Controllers;
use App\Models\MedicalBook;
use App\Models\EmpController;
use App\Models\Employees\Employees;
use Illuminate\Http\Request;
use DB;
use Session;
use Validator;
use Input;

class MedicalBookController extends Controller
{

    public function _construct()
    {
        $this->middleware('auth');
    }
    public function __construct()
    {
        $this->middleware('auth'); 
    } 


    public function index(){
        $page_title = 'Medical Book';
        $emp = DB::table('TBL_MEDICAL_BOOK ')->orderBy('MEDICAL_BOOK_ID', 'DESC')
        ->join('TBL_EMP', 'TBL_MEDICAL_BOOK.emp_id', '=', 'TBL_EMP.emp_id')
        ->get();
       
            return view('medical_book.index', compact('page_title' , 'emp'));
        
    }


    public function create(){
        $page_title= 'Medical Book';
        $emp = ['' => 'Select Employee']; 
        $employees =Employees::orderBy('emp_id', 'DESC')->orderBy('emp_name', 'ASC')->get();
        foreach($employees as $key => $row)
            $emp[$row->emp_id] = $row->emp_name .' ( '.$row->cnic . ' )';
        return view('medical_book.create', compact('page_title', 'emp'));
    }

    public function store(Request $request)
    {
        $messages = array(
            'required' => 'The Medical Book no field is required.',
           
        );

        $validator = Validator::make($request->all(), [

            'medical_book_no' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());

        $Medical = MedicalBook::orderBy('medical_book_id', 'desc')->first();
        $medicals = new MedicalBook();
        $medicals->medical_book_id = ($Medical) ? $Medical->medical_book_id + 1 : 1;
        $medicals->medical_book_no = $request->input('medical_book_no');
        $medicals->emp_id = $request->input('emp_id');
        $medicals->book_created = ($request->input('book_created'))? date('Y-m-d',strtotime($request->input('book_created'))) : '';
        $medicals->mb_status = $request->input('mb_status');

        $med = $medicals->medical_book_no;

        if($request->hasFile('mb_edoc')) {
            $file = $request->file('mb_edoc');
            $new_filename = 'edoc_'.'M -'. $med;
            $path = 'public/medial_book';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $medicals->mb_edoc = $completeUrl;
        } 
       
        $medicals->save();
        Session::flash('success', 'Medical Book created successfully.');
        return redirect('medical_book');
    }
    public function edit($id){
        $page_title= 'Medical Book';
        $medical = MedicalBook::find($id);
        $emp = ['' => 'Select Employee'];
        $employees =Employees::orderBy('emp_id', 'DESC')->orderBy('emp_name', 'ASC')->get();
        foreach($employees as $key => $row)
            $emp[$row->emp_id] = $row->emp_name .' ('.$row->cnic . ' )' ;
        return view('medical_book.edit', compact('page_title', 'emp', 'medical'));
    }
    public function update(Request $request, $id)
    {

        $messages = array(
            'required' => 'The Medical Book ID field is required.',
           
        );

        $validator = Validator::make($request->all(), [

            'medical_book_no' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());

            $med = MedicalBook::find($id);
            $medical = $med->medical_book_no;
            
            if($request->hasFile('mb_edoc')) {
                $file = $request->file('mb_edoc');
                $new_filename = 'edoc_'.'M -'. $medical;
                $path = 'public/medial_book';
                $path = str_replace('&', '_', $path);
                $extension = $file->getClientOriginalExtension();
                $file->move($path, $new_filename . '.' . $extension);
                $completeUrl = $path . '/' . $new_filename . '.' . $extension;
                $medical_doc = $completeUrl;
              }
              else 
                {
                $medical_doc = $med->mb_edoc;
                }
                $med->mb_edoc = $medical_doc;


            $MedicalArray = array(
                        'medical_book_no' => $request->input('medical_book_no'),
                        'emp_id' => $request->input('emp_id'),
                        'mb_edoc' => $medical_doc,
                        'mb_status' => $request->input('mb_status'),
                        'book_created' => ($request->input('book_created'))? date('Y-m-d',strtotime($request->input('book_created'))) : '',
                    );
                    DB::table('TBL_MEDICAL_BOOK')->where('medical_book_id', '=', $id)->update($MedicalArray);
                        Session::flash('success', 'Medical Book updated successfully.');
                        return Redirect('medical_book');
    }

    public function show($id){ 
        $page_title = 'Show Medical Book ';
        $emps = DB::table('TBL_MEDICAL_BOOK')->where('medical_book_id', $id)
        ->join('TBL_EMP', 'TBL_MEDICAL_BOOK.emp_id', '=', 'TBL_EMP.emp_id')
        ->get();

        
       // $medical = MedicalBook::find($id);
        
        
        // echo "<pre>";
        // print_r($emp);
        // die;
        return view('medical_book.show', compact('page_title', 'emps'));
    }

    public function destroy($id)
    {
        DB::table('TBL_MEDICAL_BOOK')->where('medical_book_id', '=', $id)->delete();
        Session::flash('success', ' Medical Book has been deleted successfully.');
        return redirect('medical_book');
    }

}
