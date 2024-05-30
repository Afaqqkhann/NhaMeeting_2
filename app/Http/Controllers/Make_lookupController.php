<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\MakeLookup;
use DB;
use Session;
use Validator;
use Input;

class Make_lookupController extends Controller
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
        $page_title = 'Vehicle Make lookup';
        $lookup = DB::table('TBL_VEHICLE_MAKE_LOOKUP')->orderBy('ID', 'DESC')
        ->get();
       
            return view('make_lookup.index', compact('page_title' , 'lookup'));
        
    } 


    public function create(){
        $page_title= 'Vehicle Make lookup';
       
        return view('make_lookup.create', compact('page_title'));  
    } 

    public function store(Request $request)
    {
        $messages = array(
            'required' => 'Vehicle Makers field is required.',
           
        );

        $validator = Validator::make($request->all(), [

            'vehmake' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
 
        $Lookup = MakeLookup::orderBy('id', 'desc')->first();
        $makers = new MakeLookup();
        $makers->id = ($Lookup) ? $Lookup->id + 1 : 1;
        $makers->vehmake = $request->input('vehmake');
        $makers->save();
        Session::flash('success', 'Vehicle maker name is created successfully.');
        return redirect('make_lookup');
    }
    public function edit($id){
        $page_title= 'Vehicle Make lookup';
        $make = MakeLookup::find($id);
        return view('make_lookup.edit', compact('page_title', 'make'));
    }
    public function update(Request $request, $id)
    {

        $messages = array(
            'required' => 'Vehicle Makers field is required.',
           
           
        );

        $validator = Validator::make($request->all(), [

            'vehmake' => 'required',
            

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());


            $MakeArray = array(
                        'vehmake' => $request->input('vehmake'),
            
                    );
                    DB::table('TBL_VEHICLE_MAKE_LOOKUP')->where('id', '=', $id)->update($MakeArray);
                        Session::flash('success', 'Vehicle Makers updated successfully.');
                        return Redirect('make_lookup');
    }

    public function show($id){ 
        $page_title = 'Show Vehicle Makers Name';
        $make = DB::table('TBL_VEHICLE_MAKE_LOOKUP')->where('id', $id)
        ->get();

        return view('make_lookup.show', compact('page_title', 'make'));
    }

    public function destroy($id)
    {
        DB::table('TBL_VEHICLE_MAKE_LOOKUP')->where('id', '=', $id)->delete();
        Session::flash('success', ' Vehicle Maker has been deleted successfully.');
        return redirect('make_lookup');
    } 
}
