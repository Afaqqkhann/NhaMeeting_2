<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\VehiclePhysical;
use DB;
use Session;
use Validator;
use Input;


class Vehicle_PhysicalController extends Controller
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
        $page_title = 'Vehicle Physical Title';
        $phy = DB::table('TBL_VEHICLE_PHYSICAL')->orderBy('PHYSICAL_ID', 'DESC')
        ->get();
       
            return view('vehicle_physical.index', compact('page_title' , 'phy'));
        
    } 


    public function create(){
        $page_title= 'Vehicle Physical Title';
       
        return view('vehicle_physical.create', compact('page_title'));  
    } 

    public function store(Request $request)
    {
        $messages = array(
            'required' => 'Vehicle Physical title field is required.',
           
        );

        $validator = Validator::make($request->all(), [

            'phy_title' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
 
        $Phys = VehiclePhysical::orderBy('physical_id', 'desc')->first();
        $title = new VehiclePhysical();
        $title->physical_id = ($Phys) ? $Phys->physical_id + 1 : 1;
        $title->phy_title = $request->input('phy_title');
        $title->status = 1;
        $title->save();
        Session::flash('success', 'Vehicle Physical title is created successfully.');
        return redirect('vehicle_physical');
    }
    public function edit($id){
        $page_title= 'Vehicle Physical Title';
        $phy = VehiclePhysical::find($id);
        return view('vehicle_physical.edit', compact('page_title', 'phy'));
    }
    public function update(Request $request, $id)
    {

        $messages = array(
            'required' => 'Vehicle Physical Title field is required.',
           
           
        );

        $validator = Validator::make($request->all(), [

            'phy_title' => 'required',
            

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());


            $PhyArray = array(
                        'phy_title' => $request->input('phy_title'),
            
                    );
                    DB::table('TBL_VEHICLE_PHYSICAL')->where('physical_id', '=', $id)->update($PhyArray);
                        Session::flash('success', 'Vehicle Physical Title updated successfully.');
                        return Redirect('vehicle_physical');
    }

    public function show($id){ 
        $page_title = 'Vehicle Physical Title';
        $phy = DB::table('TBL_VEHICLE_PHYSICAL')->where('physical_id', $id)
        ->get();

        return view('vehicle_physical.show', compact('page_title', 'phy'));
    }

    public function destroy($id)
    {
        DB::table('TBL_VEHICLE_PHYSICAL')->where('physical_id', '=', $id)->delete();
        Session::flash('success', ' Vehicle Physical Title has been deleted successfully.');
        return redirect('vehicle_physical');
    }
}
