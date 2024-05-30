<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\ProgressActivity;
use App\Models\ProgressCoreSystem;
use App\Models\ProgressModule;
use DB;
use Session;
use Validator;
use Input;

class Progress_activityController extends Controller
{
    
    public function index(){
        $page_title = 'Progress Activity';
        $progress = DB::table('TBL_PROGRESS_ACTIVITIES')->orderBy('ACTIVITY_ID', 'DESC')
        ->join('TBL_PROGRESS_MODULES', 'TBL_PROGRESS_ACTIVITIES.module_id', '=', 'TBL_PROGRESS_MODULES.module_id')
        ->join('TBL_PROGRESS_CORE_SYSTEM', 'TBL_PROGRESS_ACTIVITIES.cs_id', '=', 'TBL_PROGRESS_CORE_SYSTEM.cs_id')
        ->select(DB::raw('TBL_PROGRESS_ACTIVITIES.activity_status,TBL_PROGRESS_MODULES.title as mod_title, TBL_PROGRESS_CORE_SYSTEM.title as cs_title, TBL_PROGRESS_ACTIVITIES.activity_id as activity_id, TBL_PROGRESS_ACTIVITIES.title as title'))
        ->get();
            return view('progress_activity.index', compact('page_title' , 'progress')); 
          
    }  
 

    public function create(){
        $page_title= 'Progress Activities';
        $pro = ['' => 'Select Module'];
        $prog = ProgressModule::orderBy('module_id', 'DESC')->orderBy('title', 'ASC')->get();
        foreach($prog as $key => $row)
            $pro[$row->module_id] = $row->title;
        
        $activityTypes = ['' => 'Select Activity Type', 0 => 'Temporary', 1 => 'Weekly',
                            2 => 'Monthly', 3 => 'Yearly'];
        return view('progress_activity.create', compact('page_title', 'activityTypes','pro'));  
    } 
  
    public function store(Request $request)
    {
        $messages = array(
            'module_id.required' => 'Module Name field is required.',
            'activity_type.required' => 'Activity Type field is required.',
            'title.required' => 'Activity Title field is required.',
           
        );

        $validator = Validator::make($request->all(), [

            'module_id' => 'required',
            'activity_type' => 'required',
            'title' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
 
        $Progress = ProgressActivity::orderBy('activity_id', 'desc')->first();
        $progressActivity = new ProgressActivity();
        $progressActivity->activity_id = ($Progress) ? $Progress->activity_id + 1 : 1;
        $progressActivity->module_id = $request->input('module_id');
        $progressActivity->activity_type = $request->input('activity_type');

        $progressModule = ProgressModule::where('module_id', $request->input('module_id'))->firstOrFail();
        $progressActivity->cs_id = $progressModule->cs_id;       
        $progressActivity->title = $request->input('title');
        $progressActivity->activity_status = 1;
        $progressActivity->save();
        Session::flash('success', 'Progress Activity is created successfully.');
        return redirect('progress_activity');
    }
    public function edit($id){
        $page_title= 'Progress Activity';
        $progress = ProgressActivity::find($id);
        $pro = ['' => 'Select Module'];
        $prog = ProgressModule::orderBy('module_id', 'DESC')->orderBy('title', 'ASC')->get();
        foreach($prog as $key => $row)
            $pro[$row->module_id] = $row->title;
        
        $activityTypes = ['' => 'Select Activity Type', 0 => 'Temporary', 1 => 'Weekly',
                2 => 'Monthly', 3 => 'Yearly'];

        return view('progress_activity.edit', compact('page_title', 'activityTypes', 'progress', 'pro'));
    }
    public function update(Request $request, $id)
    {
       
        $messages = array(
            'module_id.required' => 'Module Name field is required.',
            'activity_type.required' => 'Activity Type field is required.',
            'title.required' => 'Activity Title field is required.',
           
        );

        $validator = Validator::make($request->all(), [

            'module_id' => 'required',
            'activity_type' => 'required',
            'title' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());

        
        $progressActivity = ProgressActivity::findOrFail($id);
        
        $progressActivity->module_id = $request->input('module_id');
        
        $progressModule = ProgressModule::where('module_id', $request->input('module_id'))->first();        
        $progressActivity->cs_id = $progressModule->cs_id;
        $progressActivity->activity_type = $request->input('activity_type');
        $progressActivity->title = $request->input('title');
        $progressActivity->activity_status = ($request->input('activity_status')) ? $request->input('activity_status') : '0';        
        $progressActivity->save();
            
        Session::flash('success', 'Progress Activity has been updated successfully.');
        return redirect('progress_activity');
    }

    public function show($id){ 
        $page_title = 'Show Progress Activity';
        $progress = DB::table('TBL_PROGRESS_ACTIVITIES')->where('activity_id', $id)
        ->join('TBL_PROGRESS_MODULES', 'TBL_PROGRESS_ACTIVITIES.module_id', '=', 'TBL_PROGRESS_MODULES.module_id')
        ->join('TBL_PROGRESS_CORE_SYSTEM', 'TBL_PROGRESS_ACTIVITIES.cs_id', '=', 'TBL_PROGRESS_CORE_SYSTEM.cs_id')
        ->select(DB::raw('TBL_PROGRESS_MODULES.title as mod_title, TBL_PROGRESS_CORE_SYSTEM.title as cs_title, TBL_PROGRESS_ACTIVITIES.activity_id as activity_id, TBL_PROGRESS_ACTIVITIES.title as title'))
        ->get();  

        return view('progress_activity.show', compact('page_title', 'progress'));
    }

    public function destroy($id)
    {
        DB::table('TBL_PROGRESS_ACTIVITIES')->where('activity_id', '=', $id)->delete();
        Session::flash('success', ' Progress Activity has been deleted successfully.');
        return redirect('progress_activity');
    }
}
