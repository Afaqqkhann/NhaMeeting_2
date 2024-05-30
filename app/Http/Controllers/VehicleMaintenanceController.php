<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleMaintenance;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use DB;
use Session;
use Validator;
use Input; 


class VehicleMaintenanceController extends Controller
{
    // public function _construct()
    // {
    //     $this->middleware('auth');
    // }
    public function __construct()
    {
        $this->middleware('auth'); 
    } 


    public function index(){
        $page_title = 'Vehicle Allotment';

        $maintain = DB::table('TBL_VEHICLE_MAINTEMANCE')->orderBy('maintenance_id', 'desc')
        ->join('TBL_EMP', 'TBL_VEHICLE_MAINTEMANCE.emp_id', '=', 'TBL_EMP.EMP_id')
        ->join('TBL_REGION', 'TBL_VEHICLE_MAINTEMANCE.region_id', '=', 'TBL_REGION.region_id')
        ->join('TBL_VEHICLE_REPAIR_HEADS', 'TBL_VEHICLE_MAINTEMANCE.head_id', '=', 'TBL_VEHICLE_REPAIR_HEADS.ID')
        ->join('TBL_VEHICLE_WORKSHOPS', 'TBL_VEHICLE_MAINTEMANCE.workshop_id', '=', 'TBL_VEHICLE_WORKSHOPS.WSID')
        ->get();
            return view('vehicle_maintenance.index', compact('page_title' , 'maintain'));    
    }  
  

    public function create($id){
        $page_title= 'Add Vehicle Maintenance';
       
        // $veh = DB::table('tbl_vehicle')->where('vehid', $id)
        // ->select('tbl_vehicle.vehid as vehid')
        // ->get();
        $v_no = DB::table('TBL_VEHICLE')->where('vehid', $id)
        ->select('TBL_VEHICLE.vehid as vehicle_id')
        ->first();
      
       

        $head = DB::table('tbl_vehicle_repair_heads')->orderBy('id','ASC')->get();
        $heads = array(null => 'Select Head');
        foreach($head as $hed){
            $heads[$hed->id] = $hed->title;
        }
         
        $wor = DB::table('tbl_vehicle_workshops')->orderBy('wsid','ASC')->get();
        $works = array(null => 'Select Workshop');
        foreach($wor as $work){
            $works[$work->wsid] = $work->wsname;
        }


        $veh = DB::table('tbl_vehicle')->orderBy('vehid','ASC')->get();
        $vehi = array(null => 'Select Vehicle');
        foreach($veh as $vehicle){
            $vehi[$vehicle->vehid] = $vehicle->vehno;
        }

        $authority = DB::table('tbl_app_authority')->orderBy('aa_id','ASC')->get();
        $authi = array(null => 'Select Authority');
        foreach($authority as $v_auth){
            $authi[$v_auth->aa_name] = $v_auth->aa_name;
        }


        $emps = DB::table('TBL_EMP')->get();
        $emp = array(null => 'Select Employee');
        
        foreach($emps as $authorty3){
            $emp[$authorty3->emp_id] = $authorty3->emp_name .' ('. $authorty3->designation.')'; 
        }  

        $regions = DB::table('tbl_region')->orderBy('region_name','ASC')->get();
        $arr_regions = array(null => 'Select Region');
        foreach($regions as $region){
            $arr_regions[$region->region_id] = $region->region_name;
        }

    
        return view('vehicle_maintenance.create', compact('page_title' ,'heads', 'works', 'vehi', 'emp','arr_regions', 'v_no','authi'));   
    } 
   
    public function store(Request $request)
    {      
        
        $messages = array(
            'head_id' => 'Head field is required.',
            'workshop_id' => 'Workshop field is required.',
            'emp_id' => 'Employee Field is required.', 
            'region_id' => 'Region in number field is required.',  
            'maintemenace_title' => 'Maintenance Title field length must be less than 200 characters.',  
        );

        $validator = Validator::make($request->all(), [
            'head_id' => 'required|integer',
            'workshop_id' => 'required|integer',
            'emp_id' => 'required|integer',
            'region_id' => 'required|integer',
            'maintemenace_title' => 'max:200',
            'edoc' => 'mimes:pdf|max:100000'           

        ], $messages);


        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
           
        // $region_ids = auth()->user()->region_id;
        $vehi = VehicleMaintenance::orderBy('maintenance_id', 'desc')->first();
        $vehicle = new VehicleMaintenance();
        $vehicle->maintenance_id = ($vehi) ? $vehi->maintenance_id + 1 : 1;
        $vehicle->head_id = $request->input('head_id');
        $vehicle->maintemenace_title = $request->input('maintemenace_title');
        $vehicle->dated = date('Y-m-d');
        $vehicle->workshop_id = $request->input('workshop_id');
        $vehicle->sanction_no = $request->input('sanction_no');
        $vehicle->saction_memo_date = ($request->input('saction_memo_date'))? date('Y-m-d',strtotime($request->input('saction_memo_date'))) : '';
        $vehicle->total_amount = $request->input('total_amount');
        $vehicle->vehid = $request->input('vehid');
        $vehicle->emp_id = $request->input('emp_id'); 
        $vehicle->meter_no = $request->input('meter_no');
        $vehicle->sanction_issued_by = $request->input('sanction_issued_by');
        $vehicle->maintemance_status = $request->input('status');
        $vehicle->region_id = $request->input('region_id');
       
     
       $aa = Vehicle::find($vehicle->vehid);
      $veh_number = $aa->vehno;
       
        if($request->hasFile('edoc')) {
            $file = $request->file('edoc');
            $new_filename = 'v_maint_'. $veh_number.'_'.$vehicle->maintenance_id;
            $path = 'public/assets/img/vehicles_maintenance';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $vehicle->edoc = $completeUrl;
        }       
            
        $vehicle->save();
        Session::flash('success', 'Vehicle Maintenance is created successfully.');
        return redirect('vehicles/'.$request->vehid);
        // } 
    }
    public function edit($id){
        $page_title= 'Vehicle Maintenance';
       
        $vehicle = VehicleMaintenance::find($id);
        // $vehicle = DB::table('tbl_vehicle_maintemance')->where('maintenance_id',$id)->first();
    
        
        $head = DB::table('tbl_vehicle_repair_heads')->orderBy('id','ASC')->get();
        $heads = array(null => 'Select Head');
        foreach($head as $hed){
            $heads[$hed->id] = $hed->title;
        }
         
        
        $wor = DB::table('tbl_vehicle_workshops')->orderBy('wsid','ASC')->get();
        $works = array(null => 'Select Workshop');
        foreach($wor as $work){
            $works[$work->wsid] = $work->wsname;
        }

        
        $veh = DB::table('tbl_vehicle')->orderBy('vehid','ASC')->get();
        $vehi = array(null => 'Select Vehicle');
        foreach($veh as $v){
            $vehi[$v->vehid] = $v->vehno;
        }

        $emps = DB::table('TBL_EMP')->get();
        $emp = array(null => 'Select Employee');
        
        foreach($emps as $authorty3){
            $emp[$authorty3->emp_id] =  $authorty3->emp_name .' ('. $authorty3->designation.')';
        }  

        $regs = DB::table('TBL_REGION')->get();
        $reg = array(null => 'Select Region');
        foreach ($regs as $authorty3) {
            $reg[$authorty3->region_id] = $authorty3->region_name;
        }

        $authority = DB::table('tbl_app_authority')->orderBy('aa_id','ASC')->get();
        $authi = array(null => 'Select Authority');
        foreach($authority as $v_auth){
            $authi[$v_auth->aa_name] = $v_auth->aa_name;
        }
        
        return view('vehicle_maintenance.edit', compact('vehicle','page_title','heads', 'works', 'vehi', 'emp','reg','authi')); 
    }
    public function update(Request $request, $id) 
    {

       
    
        $messages = array(
            'head_id' => 'Head field is required.',
            'workshop_id' => 'Workshop field is required.',
            'emp_id' => 'Employee field is required.', 
            'region_id' => 'Region field is required.',   
            'maintemenace_title' => 'Maintenance Title field length must be less than 200 characters.',  
        );

        $validator = Validator::make($request->all(), [

            'head_id' => 'required|integer',
            'workshop_id' => 'required|integer',
            'emp_id' => 'required|integer',
            'region_id' => 'required|integer',
            'maintemenace_title' => 'max:200'

        ], $messages);

               

         $vehicle = VehicleMaintenance::find($id);
         $vehi= $vehicle->maintenance_id;
         $vehs = $vehicle->vehid;

         $aa = Vehicle::find($vehicle->vehid);
         $veh_number = $aa->vehno;

         
         if($request->hasFile('edoc')) {
            $file = $request->file('edoc');
            $new_filename = 'v_maint_'. $veh_number.'_'.$vehicle->maintenance_id;
            $new_filename = 'v_maint_'. $veh_number;
            $path = 'public/assets/img/vehicles_maintenance';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $maint_doc = $completeUrl;
           }
        else 
        {
            $maint_doc = $vehicle->edoc;
        }
        $vehicle->edoc = $maint_doc;

        $VehicleArray = array(
            'head_id' => $request->input('head_id'),
            'emp_id' => $request->input('emp_id'),
            'maintemenace_title' => $request->input('maintenance_title'),
            'sanction_no' => $request->input('sanction_no'),
            'total_amount' => $request->input('total_amount'),
            'workshop_id' => $request->input('workshop_id'),
            'meter_no' => $request->input('meter_no'),
            'sanction_issued_by' => $request->input('sanction_issued_by'),
            'maintemance_status' => $request->input('maintemance_status'),
            'region_id' => $request->input('region_id'),
            'saction_memo_date' => ($request->input('saction_memo_date'))? date('Y-m-d',strtotime($request->input('saction_memo_date'))) : '',
            'dated' => date('Y-m-d'),
            'edoc' => $maint_doc,

        );
         VehicleMaintenance::where('maintenance_id', '=', $id)->update($VehicleArray);
        Session::flash('success', 'Vehicle Maintenance updated successfully.');
        return Redirect('vehicles/'.$vehs);
    }

    public function show($id){ 
        //$appoint = DB::table('TBL_APPOINTMENT')->where('EMP_ID', $id)->orderBy('appointment_id', 'DESC  ')->get();
        $page_title = 'Show Vehicle Info';
        $vehicle_no = VehicleMaintenance::find($id);
        $maint = DB::table('TBL_VEHICLE_MAINTEMANCE')->where('maintenance_id', $id)
        ->join('TBL_EMP', 'TBL_VEHICLE_MAINTEMANCE.emp_id', '=', 'TBL_EMP.EMP_id')
        ->join('TBL_VEHICLE', 'TBL_VEHICLE_MAINTEMANCE.vehid', '=', 'TBL_VEHICLE.vehid')
        ->join('TBL_REGION', 'TBL_VEHICLE_MAINTEMANCE.region_id', '=', 'TBL_REGION.region_id')
        ->join('TBL_VEHICLE_REPAIR_HEADS', 'TBL_VEHICLE_MAINTEMANCE.head_id', '=', 'TBL_VEHICLE_REPAIR_HEADS.ID')
        ->join('TBL_VEHICLE_WORKSHOPS', 'TBL_VEHICLE_MAINTEMANCE.workshop_id', '=', 'TBL_VEHICLE_WORKSHOPS.WSID')
        ->get();

    
        return view('vehicle_maintenance.show', compact('maint')); 
    }
      
    public function destroy($id) 
    { 
        $veh = VehicleMaintenance::find($id);
        $num = $veh->vehid;
        $vehicle = DB::table('TBL_VEHICLE_MAINTEMANCE')->where('maintenance_id', $id)->delete();
        Session::flash('success', ' Vehicle Maintenance been deleted successfully.');
        return redirect('vehicles/'.$num);
    }
}
