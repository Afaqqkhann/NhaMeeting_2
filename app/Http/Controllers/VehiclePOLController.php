<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\VehicleAllotment;
use DB;
use Session;
use Validator;
use Input;

class VehiclePOLController extends Controller
{
    public function _construct()
    {
        $this->middleware('auth');
    }    

    public function create($id){
        $page_title= 'Add Vehicle POL';
       
        
        $emps = DB::table('TBL_EMP')->get();
        $emp = array(null => 'Select Employee');
        
        foreach($emps as $authorty3){
            $emp[$authorty3->emp_id] = $authorty3->emp_name.' ('. $authorty3->designation.')'; 
        }  

        $vehs = DB::table('TBL_VEHICLE')->where('vehid', $id)->get();
         
        foreach($vehs as $authorty3){
            $veh[$authorty3->vehid] = $authorty3->vehno;
        }
        
        return view('vehiclePOL.create', compact('page_title', 'emp', 'veh', 'id'));   
    } 
  
    public function store(Request $request)
    {
        
        $messages = array(
            'required' => 'Employee Name is required.',
            'required' => 'Allotment Date field is required.',
            
            
           
            
        );

        $validator = Validator::make($request->all(), [

            'emp_id' => 'required|integer',
        
            'allotmentdate' => 'required|date',
            
           

        ], $messages);

        // $vehicles = DB::table('TBL_VEHICLE')-where('vehid', $request->vehid)->get();

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());

            

        // $region_ids = auth()->user()->region_id;
        
        $vehi = VehicleAllotment::orderBy('id', 'desc')->first();
        $vehicle = new VehicleAllotment();
        $vehicle->id = ($vehi) ? $vehi->id + 1 : 1;
        $vehicle->emp_id = $request->input('emp_id');
        $vehicle->vehid = $request->input('vehid');
        $vehicle->remarks = $request->input('remarks');
        $vehicle->allotmentdate = ($request->input('allotmentdate'))? date('Y-m-d',strtotime($request->input('allotmentdate'))) : '';
        $vehicle->status = $request->input('status');


     $vehi = $vehicle->vehid;
     $allot = $vehicle->id;
      if($request->hasFile('edoc')) {
            $file = $request->file('edoc');
            $new_filename = 'edoc_'.'V -'. $vehi.'A-'. $allot;
            $path = 'public/vehicle_allotment';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $vehicle->edoc = $completeUrl;
        } 
         
        $vehicle->save();

      
        // $vehicle->save();
        Session::flash('success', 'Vehicle allotment is created successfully.');
        return redirect('vehicles/'.$request->vehid);
        // }
    }
    public function edit($id){
        
        $page_title= 'Vehicle Management';
        $vehicle = VehicleAllotment::find($id);

        $emps = DB::table('TBL_EMP')->get();
        $emp = array(null => 'Select Employee');
        
        foreach($emps as $authorty3){
            $emp[$authorty3->emp_id] = $authorty3->emp_name; 
        }

        $vehs = DB::table('TBL_VEHICLE')->get();
        $veh = array(null => 'Select Vehicle');
         foreach($vehs as $authorty3){
            $veh[$authorty3->vehid] = $authorty3->vehno;
        }
        
        return view('vehicle_allotment.edit', compact('page_title','vehicle', 'emp', 'veh')); 
    }
    public function update(Request $request, $id) 
    {

        $messages = array(
            'required' => 'Enter Employee field is required.',
            'required' => 'Enter Vehicle field is required.',  
            'required' => 'Enter Vehicle allotment date field is required.',  
        );

        $validator = Validator::make($request->all(), [

            'emp_id' => 'required|integer',
            'vehid' => 'required|integer',
            'allotmentdate' => 'required'

        ], $messages);

         $vehicle = VehicleAllotment::find($id);
         $allot = $vehicle->id;
         $vehi = $vehicle->vehid; 
         if($request->hasFile('edoc')) {
            $file = $request->file('edoc');
            $new_filename = 'edoc_'.'V -'. $vehi.'A-'. $allot;
            $path = 'public/vehicle_allotment';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $allot_doc = $completeUrl;
          }
          else
            {
            $allot_doc = $vehicle->edoc;
            }
            $vehicle->edoc = $allot_doc;
       
            $VehicleArray = array(
                        'emp_id' => $request->input('emp_id'),
                        'vehid' => $request->input('vehid'),
                        'allotmentdate' => ($request->input('allotmentdate'))? date('Y-m-d',strtotime($request->input('allotmentdate'))) : '',
                        'edoc' => $allot_doc,
                        'remarks' => $request->input('remarks'),
                        'status' => $request->input('status'),
                    ); 
                   
        // VehicleAllotment::where('id', '=', $id)->update($VehicleArray);
        DB::table('TBL_VEHICLE_ALLOTMENT')->where('id', '=', $id)->update($VehicleArray);
        // $all = DB::table('TBL_VEHICLE_ALLOTMENT')->where('id',)
        // ->join('TBL_EMP', 'TBL_VEHICLE_ALLOTMENT.emp_id', '=', 'TBL_EMP.EMP_id')
        // ->join('TBL_VEHICLE', 'TBL_VEHICLE_ALLOTMENT.vehid', '=', 'TBL_VEHICLE.vehid')
        // ->get();
        Session::flash('success', 'Vehicle Allotment updated successfully.');
        return Redirect('vehicles/'.$vehi);
    }

    public function show($id){ 
        $page_title = 'Show Vehicle Info';
        $vehicle = DB::table('TBL_VEHICLE_ALLOTMENT')->where('id', $id)
        ->join('TBL_EMP', 'TBL_VEHICLE_ALLOTMENT.emp_id', '=', 'TBL_EMP.EMP_id')
        ->join('TBL_VEHICLE', 'TBL_VEHICLE_ALLOTMENT.vehid', '=', 'TBL_VEHICLE.vehid')
        ->select('TBL_VEHICLE_ALLOTMENT.id as id', 'TBL_VEHICLE.vehid as vehid', 'TBL_VEHICLE_ALLOTMENT.remarks as allot_remarks', 'TBL_EMP.emp_name as employee', 'TBL_VEHICLE_ALLOTMENT.allotmentdate as dated', 'TBL_VEHICLE_ALLOTMENT.status as veh_status', 'TBL_VEHICLE_ALLOTMENT.edoc as e_document', 'TBL_VEHICLE.vehno as vehicle_no')
        ->get();
       
        return view('vehicle_allotment.show', compact('vehicle')); 
    }
 
    public function destroy($id) 
    {
        $veh = VehicleAllotment::find($id);
        $vehicle = DB::table('TBL_VEHICLE_ALLOTMENT')->where('id', $id)->delete();
        Session::flash('success', ' Vehicle Delete has been deleted successfully.');
        return redirect('vehicles/'.$veh->vehid);
    }
}
