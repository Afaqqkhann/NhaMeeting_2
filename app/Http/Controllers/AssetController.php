<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\Asset;
use DB;
use Validator;
use Session;

class AssetController extends Controller
{
	public function __construct() {

		/*$this->middleware('auth');
        if(!Auth::user()->can('acr_assets'))
            abort(403);*/
	}
	
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($emp_id)
    {
        $page_title = 'Add Asset';
		
        $posts = ['' => 'Select Post'];
        $years = ['' => 'Select Year'];
               

        $post = DB::table('TBL_SANCTION')->orderBy('strength_name', 'ASC')->get();
        foreach ($post as $row) {
            $posts[$row->sanction_id] = $row->strength_name . '- (' . $row->sanction_id . ')';
        }
        
        
        $year = DB::table('TBL_YEAR')->where('year_status','=',1)->orderBy('year_title', 'DESC')->get();
        foreach ($year as $row) {
            $years[$row->year_id] = $row->year_title; 	//. '- (' . $row->year_id . ')';
        }              

        return view('asset.create', compact('page_title','emp_id', 'years', 'posts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $messages = [
            'year_id.required' => "The Year field is required.",
        ];
        $validation = Validator::make($request->all(),
            [
                'year_id' => 'required',

            ], $messages);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
		
		$last_asset = Asset::orderBy('assets_id', 'desc')->first();
        $asset = new Asset();
        $asset->assets_id = ($last_asset) ? $last_asset->assets_id + 1 : 1;
        $asset->emp_id = $request->input('emp_id');
        $asset->year_id = $request->input('year_id');             
		

        if (!empty($request->input('date_of_receipt')) && $request->input('date_of_receipt') !== null
		&& $request->input('date_of_receipt') !== 'dd-mm-yyyy') {

            $asset->date_of_receipt = date('Y-m-d', strtotime($request->input('date_of_receipt')));
        }
		
		$asset->assets_status = ($request->input('assets_status'))?$request->input('assets_status'):'0';

		/// EDOC
		if($request->hasFile('assets_edoc')) {
            $file = $request->file('assets_edoc');  
            /// new file name 
            $new_filename = 'asset_'.$asset->assets_id;

            $path = 'public\NHA-IS\Assets';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);

            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $asset->assets_edoc = $completeUrl;

        }

		$asset->save();		
		
		Session::flash('success', 'Record added successfully.');	
       
        
        return Redirect('employee/emp_detail/' . $request->input('emp_id'));
		
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $page_title = 'Edit Asset';
		
        $posts = ['' => 'Select Post'];
        $years = ['' => 'Select Year'];
		
		$data = Asset::find($id);
               

        $post = DB::table('TBL_SANCTION')->orderBy('strength_name', 'ASC')->get();
        foreach ($post as $row) {
            $posts[$row->sanction_id] = $row->strength_name . '- (' . $row->sanction_id . ')';
        }        
        
        $year = DB::table('TBL_YEAR')->where('year_status','=',1)->orderBy('year_title', 'DESC')->get();
        foreach ($year as $row) {
            $years[$row->year_id] = $row->year_title; 	//. '- (' . $row->year_id . ')';
        }              

        return view('asset.edit', compact('page_title','data','emp_id', 'years', 'posts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $messages = [
            'year_id.required' => "The Year field is required.",
        ];
        $validation = Validator::make($request->all(),
            [
                'year_id' => 'required',

            ], $messages);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
		
		$asset = Asset::find($id);       
        
        $asset->year_id = $request->input('year_id');            
		

        if (!empty($request->input('date_of_receipt')) && $request->input('date_of_receipt') !== null
		&& $request->input('date_of_receipt') !== 'dd-mm-yyyy') {

            $asset->date_of_receipt = date('Y-m-d', strtotime($request->input('date_of_receipt')));
        }
		
		$asset->assets_status = ($request->input('assets_status'))?$request->input('assets_status'):'0';
		/// EDOC
		if($request->hasFile('assets_edoc')) {
            $file = $request->file('assets_edoc');  
            /// new file name 
            $new_filename = 'asset_'.$asset->assets_id;

            $path = 'public\NHA-IS\Assets';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);

            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $asset->assets_edoc = $completeUrl;

        } else{
            if($asset->assets_edoc)
                $asset->assets_edoc = $asset->assets_edoc;
            else
                $asset->assets_edoc = '';

        }	

		$asset->save();		
		
		Session::flash('success', 'Record updated successfully.');	
       
        
        return Redirect('employee/emp_detail/' . $asset->emp_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $data = Asset::find($id);
        DB::table('TBL_ASSETS')->where('ASSETS_ID', '=', $id)->delete();
        Session::flash('success', 'Record has been deleted successfully.');
		
        return Redirect('employee/emp_detail/' . $data->emp_id);
    }
}
