<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\RepairHeads;
use DB;
use Session;
use Validator;
use Input;

class RepairHeadsController extends Controller
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
        $page_title = 'Vehicle Repair Heads Title';
        $rep = DB::table('TBL_VEHICLE_REPAIR_HEADS')->orderBy('ID', 'DESC')
        ->get();
       
            return view('repair_heads.index', compact('page_title' , 'rep'));
        
    } 


    public function create(){
        $page_title= 'Vehicle Repair Heads Title';
       
        return view('repair_heads.create', compact('page_title'));  
    } 

    public function store(Request $request)
    {
        $messages = array(
            'required' => 'Vehicle Repair Head title field is required.',
           
        );

        $validator = Validator::make($request->all(), [

            'title' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
 
        $Rep = RepairHeads::orderBy('id', 'desc')->first();
        $repair = new RepairHeads();
        $repair->id = ($Rep) ? $Rep->id + 1 : 1;
        $repair->title = $request->input('title');
        $repair->save();
        Session::flash('success', 'Vehicle Repair Heads title is created successfully.');
        return redirect('repair_heads');
    }
    public function edit($id){
        $page_title= 'Vehicle Repair Heads Title';
        $rep = RepairHeads::find($id);
        return view('repair_heads.edit', compact('page_title', 'rep'));
    }
    public function update(Request $request, $id)
    {

        $messages = array(
            'required' => 'Vehicle Repair Head Title field is required.',
           
           
        );

        $validator = Validator::make($request->all(), [

            'title' => 'required',
            

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());


            $RepArray = array(
                        'title' => $request->input('title'),
            
                    );
                    DB::table('TBL_VEHICLE_REPAIR_HEADS')->where('id', '=', $id)->update($RepArray);
                        Session::flash('success', 'Vehicle Repair Head Title updated successfully.');
                        return Redirect('repair_heads');
    }

    public function show($id){ 
        $page_title = 'Vehicle Repair Heads Title';
        $rep = DB::table('TBL_VEHICLE_REPAIR_HEADS')->where('id', $id)
        ->get();

        return view('repair_heads.show', compact('page_title', 'rep'));
    }

    public function destroy($id)
    {
        DB::table('TBL_VEHICLE_REPAIR_HEADS')->where('id', '=', $id)->delete();
        Session::flash('success', ' Vehicle Repair Head Title has been deleted successfully.');
        return redirect('vehicle_physical');
    }
}
