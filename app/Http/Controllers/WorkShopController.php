<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\WorkShop;
use DB;
use Session;
use Validator;
use Input;


class WorkShopController extends Controller
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
        $page_title = 'WorkShops Management';
        $wor = DB::table('TBL_VEHICLE_WORKSHOPS')->orderBy('wsid', 'desc')
        ->join('TBL_REGION', 'TBL_VEHICLE_WORKSHOPS.region_id', '=', 'TBL_REGION.region_id')
        ->get();
        
            return view('work_shops.index', compact('page_title', 'wor'));  
           
    }  
  

    public function create(){
        $page_title= 'Add Workshop';
        $reg = DB::table('TBL_Region')->get();
        $region = array(null => 'Select Region');
        
        foreach($reg as $authorty3){
            $region[$authorty3->region_id] = $authorty3->region_name; 
        } 
     
        return view('work_shops.create', compact('page_title', 'region'));   
    } 
  
    public function store(Request $request)
    {
        
        $messages = array(
            'required' => 'WorkShop Name field is required.',
            
        );

        $validator = Validator::make($request->all(), [

            'wsname' => 'required',
            

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());

          
        
        $wor = WorkShop::orderBy('wsid', 'desc')->first();
        $shops = new WorkShop();
        $shops->wsid = ($wor) ? $wor->wsid + 1 : 1;
        $shops->wsname = $request->input('wsname');
        $shops->wslocation = $request->input('wslocation');
        $shops->wsgst_num = $request->input('wsgst_num');
        $shops->wsntn_num = $request->input('wsntn_num');
        $shops->description = $request->input('description');
        $shops->region_id = $request->input('regID');
        $shops->status = 1;
        $shops->save();
        Session::flash('success', 'Workshop is created successfully.');
        return redirect('work_shops');
        
    }

    
    public function edit($id){
        $page_title= 'Edit Workshop';
        $wor = WorkShop::find($id);

        $emp = ['' => 'Select Region'];
        $employees =DB::table('TBL_Region')->get();
        foreach($employees as $key => $row)
            $emp[$row->region_id] = $row->region_name;
        return view('work_shops.edit', compact('page_title', 'wor', 'emp'));  
    }

    public function update(Request $request, $id) 
    {

           $messages = array(
           
            
           
        );

        $validator = Validator::make($request->all(), [

            
           

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());


            $workshopArray = array(
                        'wsname' => $request->input('wsname'),
                        'wslocation' => $request->input('wslocation'),
                        'wsgst_num' => $request->input('wsgst_num'),
                        'wsntn_num' => $request->input('wsntn_num'),
                        'description' => $request->input('description'),
                        'region_id' => $request->input('regID'),
                    );
        WorkShop::where('wsid', '=', $id)->update($workshopArray);
        Session::flash('success', 'Workshop updated successfully.');
        return Redirect('work_shops');
    }

    public function show($id){ 
        $page_title = 'Show WorkShop';

        $work = DB::table('TBL_VEHICLE_WORKSHOPS')->where('wsid', $id)
        ->join('TBL_REGION', 'TBL_VEHICLE_WORKSHOPS.region_id', '=', 'TBL_REGION.region_id')
        ->get();
        // $work[] = WorkShop::find($id);
        return view('work_shops.show', compact('work')); 
    }
 
    public function destroy($id) 
    {
        WorkShop::destroy($id);
        Session::flash('success', ' Workshop has been deleted successfully.');
        return redirect('work_shops');
    }
}
