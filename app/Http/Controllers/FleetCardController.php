<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\FleetCard;
use DB;
use Session;
use Validator;
use Input;

class FleetCardController extends Controller
{
    public function _construct()
    {
        $this->middleware('auth');
    }
    public function __construct()
    {
        $this->middleware('auth'); 
    } 


    // public function index(){
    //     $page_title = 'Vehicle Fleet Card';

    //     $all = DB::table('TBL_VEHICLE_FLEET_CARD')->orderBy('fleet_id', 'desc')
    //     ->join('TBL_VEHICLE', 'TBL_VEHICLE_FLEET_CARD.vehid', '=', 'TBL_VEHICLE.vehid')
    //     ->get();
    //         return view('vehicle_allotment.index', compact('page_title' , 'all'));  
           
    // }  
  

    public function create($id){
        $page_title= 'Add Vehicle Fleet Card';
       
        $vehs = DB::table('TBL_VEHICLE')->where('vehid', $id)->get();
         
        foreach($vehs as $authorty3){
            $veh[$authorty3->vehid] = $authorty3->vehno;
        }
       
        return view('fleet_card.create', compact('page_title', 'veh'));   
    } 
  
    public function store(Request $request)
    {
        
        $messages = array(
           
            'required' => 'Vehicle field is required.',
            'required' => 'Created Date field is required.',
            'required' => 'Expiry date field is required.',

        );

        $validator = Validator::make($request->all(), [

            'vehid' => 'required|integer',
            'fleet_title' => 'required',
            'create_date' => 'required',
            
           

        ], $messages);


        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
            
        // $region_ids = auth()->user()->region_id;
        $vehi = FleetCard::orderBy('fleet_id', 'desc')->first();
        $vehicle = new FleetCard();
        $vehicle->fleet_id = ($vehi) ? $vehi->fleet_id + 1 : 1;
        $vehicle->vehid = $request->input('vehid');
        $vehicle->fleet_title = $request->input('fleet_title');
        $vehicle->create_date = ($request->input('create_date'))? date('Y-m-d',strtotime($request->input('create_date'))) : '';
        $vehicle->expiry_date = ($request->input('expiry_date'))? date('Y-m-d',strtotime($request->input('expiry_date'))) : '';
        $vehicle->status = $request->input('status');   
        $vehicle->save();

      
        // $vehicle->save();
        Session::flash('success', 'Vehicle allotment is created successfully.');
        return redirect('vehicles/'.$request->vehid);
        // }
    }
    public function edit($id){
        $page_title= 'Vehicle Management';
        $vehicle = FleetCard::find($id);

       
        $vehs = DB::table('TBL_VEHICLE')->get();
        $veh = array(null => 'Select Vehicle');
         foreach($vehs as $authorty3){
            $veh[$authorty3->vehid] = $authorty3->vehno;
        } 
        
        return view('fleet_card.edit', compact('page_title','veh', 'vehicle')); 
    }
    public function update(Request $request, $id) 
    {

        $messages = array(
            'required' => 'Enter Vehicle field is required.',  
            'required' => 'Enter Create date field is required.',  
            'required' => 'Enter Expiry date field is required.',  
        );

        $validator = Validator::make($request->all(), [

           
            'veh_id' => 'required|integer',
            'create_date' => 'required',
            'expiry_date' => 'required'

        ], $messages);

               
    
         $vehicle = FleetCard::find($id);
         $vehi = $vehicle->vehid; 
            $VehicleArray = array(
                'fleet_title' => $request->input('fleet_title'),
                        'vehid' => $request->input('vehid'),
                        'create_date' => ($request->input('create_date'))? date('Y-m-d',strtotime($request->input('create_date'))) : '',
                        'expiry_date' => ($request->input('expiry_date'))? date('Y-m-d',strtotime($request->input('expiry_date'))) : '',
                        'status' => $request->input('status'),
                    );
         FleetCard::where('fleet_id', '=', $id)->update($VehicleArray);
        Session::flash('success', 'Vehicle Fleet Card updated successfully.');
        return Redirect('vehicles/'.$vehi);
    }

    public function show($id){ 
        $page_title = 'Show Vehicle Info';
        $vehicle = DB::table('TBL_VEHICLE_FLEET_CARD')->where('fleet_id', $id)
        ->join('TBL_VEHICLE', 'TBL_VEHICLE_FLEET_CARD.vehid', '=', 'TBL_VEHICLE.vehid')
        ->get();
      
        return view('fleet_card.show', compact('vehicle')); 
    }
 
    public function destroy($id) 
    {
         //$vehicle = FleetCard::find($id);
         //$vehi = $vehicle->vehid;
        $fleet = DB::table('TBL_VEHICLE_FLEET_CARD')->where('fleet_id', $id)->delete();
        Session::flash('success', ' Vehicle Fleet Card has been deleted successfully.');
        return redirect('vehicles');
    }
}
