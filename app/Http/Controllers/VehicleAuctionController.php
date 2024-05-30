<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\VehicleAuctionPlace;
use App\Models\VehicleFYAuction;
use DB;
use Session;
use Validator;
use Input;

class VehicleAuctionController extends Controller
{
       

    public function create($id){
        $page_title= 'Add Vehicle Auction';       
        
        $places = DB::table('TBL_PLACE')->orderBy('place_title', 'asc')->get();
        $placesArr = array(null => 'Select Place');
        
        foreach($places as $place){
            $placesArr[$place->place_id] = $place->place_title; 
        }   
        
        $actFys = VehicleFYAuction::orderBy('auction_title','asc')->get();
        $auctionFYs = array(null => 'Select Auction Title');
        foreach($actFys as $act){
            $auctionFYs[$act->auction_id] = $act->auction_title; 
        } 
        return view('vehicleAuction.create', compact('page_title', 'auctionFYs', 'placesArr', 'id'));   
    } 
  
    public function store(Request $request)
    {
        
        $messages = array(
            'place_id.required' => 'Place filed must be selected.',
            'auction_id.required' => 'Auction Title field must be selected.',
            
        );

        $validator = Validator::make($request->all(), [

            'place_id' => 'required',        
            'auction_id' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
                
        $auctionRecord = VehicleAuctionPlace::orderBy('veh_auction_id', 'desc')->first();
        $auction = new VehicleAuctionPlace();
        $auction->veh_auction_id = ($auctionRecord) ? $auctionRecord->veh_auction_id + 1 : 1;
        $auction->veh_id = $request->input('veh_id');
        $auction->place_id = $request->input('place_id');
        $auction->auction_id = $request->input('auction_id');
        $auction->auction_proposed_amount = $request->input('auction_proposed_amount');
        $auction->auction_amount = $request->input('auction_amount');
        $auction->highiest_bidder = $request->input('highiest_bidder');
        $auction->comments = $request->input('comments');
        $vehicleFYAuc = VehicleFYAuction::where('auction_id',$request->input('auction_id'))->first();
        $auction->auction_title = $vehicleFYAuc->auction_title;
         
        $auction->save();

        Session::flash('success', 'Vehicle Auction is added successfully.');
        return redirect('vehicles/'.$request->input('veh_id'));
        // }
    }
    public function edit($id){
        
        $page_title= 'Add Vehicle Auction';       
        
        $places = DB::table('TBL_PLACE')->orderBy('place_title', 'asc')->get();
        $placesArr = array(null => 'Select Place');
        
        foreach($places as $place){
            $placesArr[$place->place_id] = $place->place_title; 
        }   
        
        $actFys = VehicleFYAuction::orderBy('auction_title','asc')->get();
        $auctionFYs = array(null => 'Select Auction Title');
        foreach($actFys as $act){
            $auctionFYs[$act->auction_id] = $act->auction_title; 
        } 
        $data = VehicleAuctionPlace::findOrFail($id);
        return view('vehicleAuction.edit', compact('page_title', 'data','auctionFYs', 'placesArr', 'id'));   
    }
    public function update(Request $request, $id) 
    {

        $messages = array(
            'place_id.required' => 'Place filed must be selected.',
            'auction_id.required' => 'Auction Title field must be selected.',
            
        );

        $validator = Validator::make($request->all(), [

            'place_id' => 'required',        
            'auction_id' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
                
        
        $auction = VehicleAuctionPlace::find($id);      
       
        $auction->place_id = $request->input('place_id');
        $auction->auction_proposed_amount = $request->input('auction_proposed_amount');
        $auction->auction_amount = $request->input('auction_amount');
        $auction->highiest_bidder = $request->input('highiest_bidder');
        $auction->comments = $request->input('comments');
        $auction->auction_id = $request->input('auction_id');
        $vehicleFYAuc = VehicleFYAuction::where('auction_id',$request->input('auction_id'))->first();
        $auction->auction_title = $vehicleFYAuc->auction_title;
         
        $auction->save();
        Session::flash('success', 'Vehicle Auction updated successfully.');
        return Redirect('vehicles/'.$auction->veh_id);
    }

    public function show($id){ 
        $page_title = 'Vehicle Auction Info';
        $vehicle = VehicleAuctionPlace::findOrFail($id);
       
        return view('vehicleAuction.show', compact('vehicle')); 
    }
 
    public function destroy($id) 
    {
        $veh = VehicleAuctionPlace::find($id);
        $vehicle = VehicleAuctionPlace::where('veh_auction_id', $id)->delete();
        Session::flash('success', ' Vehicle Auction has been deleted successfully.');
        return redirect('vehicles/'.$veh->veh_id);
    }
}
