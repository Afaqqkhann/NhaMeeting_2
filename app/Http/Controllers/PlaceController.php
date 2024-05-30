<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\Region;
use App\Models\PlaceType;
use App\Models\Employees\Employees;
use DB;
use Session;
use Validator;
use Input;

class PlaceController extends Controller
{
    


    public function index(){
        $page_title = 'Place';
        $place = DB::table('V_PLACE')->orderBy('PLACE_ID', 'DESC')
        /* ->join('TBL_ZONE', 'TBL_PLACE.zone_id', '=', 'TBL_ZONE.zone_id')
        ->join('TBL_REGION', 'TBL_PLACE.region_id', '=', 'TBL_REGION.region_id')
        ->join('TBL_PLACE_TYPE', 'TBL_PLACE.place_type_id', '=', 'TBL_PLACE_TYPE.place_type_id') */
        ->get();
       
            return view('place.index', compact('page_title' , 'place'));
        
    } 


    public function create(){
        $page_title= 'Create Place';
        $reg = ['' => 'Select Region'];
        $region = DB::table('TBL_REGION')
            ->join('TBL_ZONE','TBL_REGION.ZONE_ID','=','TBL_ZONE.ZONE_ID')
            ->orderBy('TBL_REGION.REGION_ID', 'DESC')
            ->orderBy('TBL_REGION.REGION_NAME', 'ASC')->get();
        foreach($region as $key => $row)
            $reg[$row->region_id] = $row->region_name .' - '. $row->zone_title;

    
        $plc = ['' => 'Select Place Type'];
        $place = PlaceType::orderBy('place_type_id', 'DESC')->orderBy('place_type', 'ASC')->get();
            foreach($place as $key => $row)
                $plc[$row->place_type_id] = $row->place_type;
        return view('place.create', compact('page_title', 'reg', 'plc'));  
    } 

    public function store(Request $request)
    {
        $messages = array(
            'required' => 'The Region field is required.',
            'required' => 'The Place Type field is required.',
           
        );

        $validator = Validator::make($request->all(), [

            'region_id' => 'required',
            'place_type_id' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
 
        $Place = Place::orderBy('place_id', 'desc')->first();
        $places = new Place();
        $places->place_id = ($Place) ? $Place->place_id + 1 : 1;
        $places->region_id = $request->input('region_id');
        $places->place_title = $request->input('place_title');
        $places->place_type_id = $request->input('place_type_id');
        $places->place_status = 1;
        $places->coordinates = $request->input('coordinates');
        $places->length_km = $request->input('length_km');
        $places->chainage_from = $request->input('chainage_from'); 
        $places->chainage_to = $request->input('chainage_to');
        $places->seq_no = $request->input('seq_no');
        $places->address = $request->input('address');
        $places->save();
        Session::flash('success', 'Place is created successfully.');
        return redirect('place');
    }
    public function edit($id){
        $page_title= 'Edit Place';
        $place = Place::find($id);
        $reg = ['' => 'Select Region'];
       
        $region = DB::table('TBL_REGION')
            ->join('TBL_ZONE','TBL_REGION.ZONE_ID','=','TBL_ZONE.ZONE_ID')
            ->orderBy('TBL_REGION.REGION_ID', 'DESC')
            ->orderBy('TBL_REGION.REGION_NAME', 'ASC')->get();
        foreach($region as $key => $row)
            $reg[$row->region_id] = $row->region_name .' - '. $row->zone_title;

        
        $plc = ['' => 'Select Place Type'];
        $places = PlaceType::orderBy('place_type_id', 'DESC')->orderBy('place_type', 'ASC')->get();
            foreach($places as $key => $row)
                $plc[$row->place_type_id] = $row->place_type;
        return view('place.edit', compact('page_title', 'place', 'reg', 'plc'));
    }
    public function update(Request $request, $id)
    {

        $messages = array(
            'required' => 'The Region field is required.',
            'required' => 'The Place Type field is required.',
           
        );

        $validator = Validator::make($request->all(), [

            'region_id' => 'required',
            'place_type_id' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());


            $PlaceArray = array(
                        'region_id' => $request->input('region_id'),
                        'place_title' => $request->input('place_title'),
                        'place_type_id' => $request->input('place_type_id'),
                        'coordinates' => $request->input('coordinates'),
                        'length_km' => $request->input('length_km'),
                        'chainage_from' => $request->input('chainage_from'),
                        'chainage_to' => $request->input('chainage_to'),
                        'seq_no' => $request->input('seq_no'),
                        'address' => $request->input('address'),
            
                    );
                    DB::table('TBL_PLACE')->where('place_id', '=', $id)->update($PlaceArray);
                        Session::flash('success', 'Place updated successfully.');
                        return Redirect('place');
    }

    public function show($id){ 
        $page_title = 'Show Place';
        $place = DB::table('TBL_PLACE')->where('place_id', $id)
        ->join('TBL_REGION', 'TBL_PLACE.region_id', '=', 'TBL_REGION.region_id')
        ->join('TBL_PLACE_TYPE', 'TBL_PLACE.place_type_id', '=', 'TBL_PLACE_TYPE.place_type_id')
        ->get();     
   
        
       
        return view('place.show', compact('page_title', 'place'));
    }

    public function destroy($id)
    {
        DB::table('TBL_PLACE')->where('place_id', '=', $id)->delete();
        Session::flash('success', ' Place has been deleted successfully.');
        return redirect('place');
    }
}
