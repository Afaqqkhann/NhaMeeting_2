<?php

namespace App\Http\Controllers;
use App\Models\Panel;
use App\Models\Region;
use Illuminate\Http\Request;
use DB;
use Session;
use Validator;
use Input;
use Auth;

class PanelController extends Controller{

    
    /*public function __construct()
    {
        if (!Auth::user()->can('panel')) {
            abort(403);
        }
    }*/

    public function index(){
		$page_title = 'Panels';
        if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('memishq'))
			$panels = Panel::orderBy('panel_id', 'desc')->get();
		else
			$panels = Panel::where('region_id',auth()->user()->region_id)->orderBy('panel_id', 'desc')->get();
        return view('panel.index', compact('panels','page_title'));
    }


    public function create(){
        $page_title= 'Create Panel';
        
        $Category = ['' => 'Select Category ', 'Category A' => 'Category A', 'Category B' => 'Category B', 'Category C' => 'Category C'];
        
		$panel_types = DB::table('tbl_panel_type')->orderBy('pt_title','ASC')->get();
        $arr_panel_types = array(null => 'Select Panel Type');
        foreach($panel_types as $pt){
            $arr_panel_types[$pt->pt_id] = $pt->pt_title;
        }
		
		$regions = DB::table('tbl_region')->orderBy('region_name','ASC')->get();
        $arr_regions = array(null => 'Select Region');
        foreach($regions as $region){
            $arr_regions[$region->region_id] = $region->region_name;
        }
        return view('panel.create', compact('page_title', 'arr_panel_types', 'Category', 'arr_regions'));
    }

    public function store(Request $request)
    {
        $messages = array(
            'panel_title.required' => 'The Panel title field is required.',
            'category.required' => 'The Category field is required.',
            'regID.required' => 'The Region field is required.',
            'panel_type_id.required' => 'The Panel Type field is required.',
        );

        $validator = Validator::make($request->all(), [

            'panel_title' => 'required',
            'category' => 'required',
            'regID' => 'required',
            'panel_type_id' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());

        $Panel = Panel::orderBy('panel_id', 'desc')->first();
        $panels = new Panel();
        $panels->panel_id = ($Panel) ? $Panel->panel_id + 1 : 1;
        $panels->panel_title = $request->input('panel_title');
        $panels->phone = $request->input('phone');
        $panels->ext = $request->input('ext');
        $panels->category = $request->input('category');
        $panels->region_id = $request->input('regID');
        $panels->address = $request->input('address');
        $panels->panel_type_id = $request->input('panel_type_id');
        $panels->save();
        Session::flash('success', 'Panel created successfully.');
        return redirect('panel');
    }
    public function edit($id){
        $page_title= 'Panel Edit';
        $panel = Panel::find($id);
        $panel_type = ['' => 'Select Panel Type', 'Hospital' => 'Hospital', 'Clinic' => 'Clinic', 'Lab' => 'Lab'];
        $Category = ['' => 'Select Category ', 'Category A' => 'Category A', 'Category B' => 'Category B', 'Category C' => 'Category C'];
        
		$panel_types = DB::table('tbl_panel_type')->orderBy('pt_title','ASC')->get();
        $arr_panel_types = array(null => 'Select Panel Type');
        foreach($panel_types as $pt){
            $arr_panel_types[$pt->pt_id] = $pt->pt_title;
        }
		
		$regions = DB::table('tbl_region')->orderBy('region_name','ASC')->get();
        $arr_regions = array(null => 'Select Region');
        foreach($regions as $region){
            $arr_regions[$region->region_id] = $region->region_name;
        }
        //echo "<pre>"; print_r($panel); die;
        return view('panel.edit', compact('page_title', 'arr_panel_types', 'Category', 'arr_regions', 'panel'));
    }

    public function update(Request $request, $id)
    {
        $messages = array(
            'panel_title.required' => 'The Panel title field is required.',
            'category.required' => 'The Category field is required.',
            'regID.required' => 'The Region field is required.',
            'panel_type_id.required' => 'The Panel Type field is required.',
        );

        $validator = Validator::make($request->all(), [

            'panel_title' => 'required',
            'category' => 'required',
            'regID' => 'required',
            'panel_type_id' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
        $PanelsArray = array(
            'panel_title' => $request->input('panel_title'),
            'phone' => $request->input('phone'),
            'ext' => $request->input('ext'),
            'category' => $request->input('category'),
            'region_id' => $request->input('regID'),
            'address' => $request->input('address'),
            'panel_type_id' => $request->input('panel_type_id'),
        );
        DB::table('TBL_PANEL')->where('PANEL_ID', '=', $id)->update($PanelsArray);
        Session::flash('success', 'Panel updated successfully.');
        return Redirect('panel');
    }

    public function show($id){
        $panel = Panel::find($id);
        $page_title = "Panel Show";
        return view('panel.show', compact('panel', 'page_title'));
    }

    public function destroy($id)
    {
        DB::table('TBL_PANEL')->where('panel_id', '=', $id)->delete();
        Session::flash('success', ' Panel has been deleted successfully.');
        return redirect('panel');
    }

}
