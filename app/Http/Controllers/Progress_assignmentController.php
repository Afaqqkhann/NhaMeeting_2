<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProgressActivity;
use App\Models\ProgressAssignment;
use App\Models\ProgressModule;
use App\User;
use DB;
use Illuminate\Http\Request;
use Input;
use Session;
use Validator;

class Progress_assignmentController extends Controller
{

    public function index($activityId = 0)
    {
        $page_title = 'Progress Assignment';

        $assign = DB::table('TBL_PROGRESS_ASSIGNMENT')->orderBy('ASSIGNMENT_ID', 'DESC')
            ->join('TBL_PROGRESS_MODULES', 'TBL_PROGRESS_ASSIGNMENT.module_id', '=', 'TBL_PROGRESS_MODULES.module_id')
            ->join('TBL_PROGRESS_CORE_SYSTEM', 'TBL_PROGRESS_ASSIGNMENT.cs_id', '=', 'TBL_PROGRESS_CORE_SYSTEM.cs_id')
            ->join('TBL_PROGRESS_ACTIVITIES', 'TBL_PROGRESS_ASSIGNMENT.activity_id', '=', 'TBL_PROGRESS_ACTIVITIES.activity_id')
            ->join('USERS', 'TBL_PROGRESS_ASSIGNMENT.user_id', '=', 'USERS.id')
            ->select(DB::raw('USERS.name as user_full_name, TBL_PROGRESS_MODULES.title as mod_title, TBL_PROGRESS_CORE_SYSTEM.title as cs_title, TBL_PROGRESS_ACTIVITIES.title as activity_title, TBL_PROGRESS_ASSIGNMENT.days_diff, TBL_PROGRESS_ASSIGNMENT.assignment_id as assignment_id, TBL_PROGRESS_ASSIGNMENT.activity_id, TBL_PROGRESS_ASSIGNMENT.assign_date as assign_date, TBL_PROGRESS_ASSIGNMENT.complete_date as complete_date, TBL_PROGRESS_ASSIGNMENT.remarks as remarks'));
        $assign = ($activityId > 0) ? $assign->where("activity_id", $activityId)->get() :
        $assign->get();
        //dd($assign);
        return view('progress_assignment.index', compact('page_title', 'assign'));

    }

    public function create()
    {
        $page_title = 'Add Progress Assignment';
        $act = ['' => 'Select Activity'];
        $activity = ProgressActivity::orderBy('activity_id', 'DESC')->orderBy('title', 'ASC')->get();
        foreach ($activity as $key => $row) {
            $act[$row->activity_id] = $row->title;
        }

        /* $pro = ['' => 'Select Module'];
        $prog = ProgressModule::orderBy('module_id', 'DESC')->orderBy('title', 'ASC')->get();
        foreach($prog as $key => $row)
        $pro[$row->module_id] = $row->title;
        $c_sys = ['' => 'Select Core System'];
        $core_sys = ProgressCoreSystem::orderBy('cs_id', 'DESC')->orderBy('title', 'ASC')->get();
        foreach($core_sys as $key => $row)
        $c_sys[$row->cs_id] = $row->title; */
        $users = ['' => 'Select User'];
        $user_records = User::orderBy('name', 'ASC')->get();
        foreach ($user_records as $key => $row) {
            $users[$row->id] = $row->name;
        }

        return view('progress_assignment.create', compact('page_title', 'users', 'act'));
    }

    public function store(Request $request)
    {

        $messages = array(
            'activity_id.required' => 'Activity field is required.',
            // 'module_id.required' => 'Module field is required.',
            // 'cs_id.required' => 'Core System field is required.',
            // 'assign_date.required' => 'Assignment Date is required.',
            // 'complete_date.required' => 'Complete Date field is required.',
            'remarks.required' => 'Remarks field is required.',
            'user_id.required' => 'User field is required.',

        );

        $validator = Validator::make($request->all(), [

            'activity_id' => 'required',
            // 'module_id' => 'required',
            // 'cs_id' => 'required',
            //'assign_date' => 'required',
           // 'complete_date' => 'required',
            'remarks' => 'required',
            'user_id' => 'required',

        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        $p_assign = ProgressAssignment::orderBy('assignment_id', 'desc')->first();
        $p_assignment = new ProgressAssignment();
        $p_assignment->assignment_id = ($p_assign) ? $p_assign->assignment_id + 1 : 1;
        $p_assignment->activity_id = $request->input('activity_id');
        $progressActivity = ProgressActivity::where('activity_id', $request->input('activity_id'))->first();
        $p_assignment->module_id = $progressActivity->module_id;
        $progressModule = ProgressModule::where('module_id', $progressActivity->module_id)->first();
        $p_assignment->cs_id = $progressModule->cs_id;
        $assignDate = \Naeem\Helpers\Helper::convert_date($request->input('assign_date'));
        $completeDate = \Naeem\Helpers\Helper::convert_date($request->input('complete_date'));
       
        $p_assignment->assign_date = $assignDate;
        $p_assignment->complete_date = $completeDate;
       
        /* Days Difference Calculation */
        $daysDiff = 0;
        if ($assignDate != null && $completeDate != null) {
            $completion_date = new \DateTime($request->input('complete_date'));
            $assign_date = new \DateTime($request->input('assign_date'));
            $interval = $completion_date->diff($assign_date);
            $daysDiff = $interval->format('%a') + 1;
        }

        $p_assignment->days_diff = $daysDiff;
        $p_assignment->remarks = $request->input('remarks');
        $p_assignment->user_id = $request->input('user_id');
        $p_assignment->assign_status = 1;
        $p_assignment->save();
        Session::flash('success', 'Progress Assignment is created successfully.');
        return redirect('progress_assignment');
    }
    public function edit($id)
    {
        $page_title = 'Edit Progress Assignment';
        $assignment = ProgressAssignment::find($id);
        $act = ['' => 'Select Activity'];
        $activity = ProgressActivity::orderBy('activity_id', 'DESC')->orderBy('title', 'ASC')->get();
        foreach ($activity as $key => $row) {
            $act[$row->activity_id] = $row->title;
        }

        /* $pro = ['' => 'Select Module'];
        $prog = ProgressModule::orderBy('module_id', 'DESC')->orderBy('title', 'ASC')->get();
        foreach($prog as $key => $row)
        $pro[$row->module_id] = $row->title;
        $c_sys = ['' => 'Select Core System'];
        $core_sys = ProgressCoreSystem::orderBy('cs_id', 'DESC')->orderBy('title', 'ASC')->get();
        foreach($core_sys as $key => $row)
        $c_sys[$row->cs_id] = $row->title; */

        $users = ['' => 'Select User'];
        $user_records = User::orderBy('name', 'ASC')->get();
        foreach ($user_records as $key => $row) {
            $users[$row->id] = $row->name;
        }

        return view('progress_assignment.edit', compact('page_title', 'users', 'assignment', 'act'));
    }
    public function update(Request $request, $id)
    {

        $messages = array(
            'activity_id.required' => 'Activity field is required.',
            //'module_id.required' => 'Module field is required.',
            //'cs_id.required' => 'Core System field is required.',
           // 'assign_date.required' => 'Assignment Date is required.',
            //'complete_date.required' => 'Complete Date field is required.',
            'remarks.required' => 'Remarks field is required.',
            'user_id.required' => 'User field is required.',
        );

        $validator = Validator::make($request->all(), [
            'activity_id' => 'required',
            // 'module_id' => 'required',
            //'cs_id' => 'required',
            // 'assign_date' => 'required',
            // 'complete_date' => 'required',
            'remarks' => 'required',
            'user_id' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        $p_assignment = ProgressAssignment::find($id);
        $p_assignment->activity_id = $request->input('activity_id');
        $progressActivity = ProgressActivity::where('activity_id', $request->input('activity_id'))->first();
        $p_assignment->module_id = $progressActivity->module_id;

        $progressModule = ProgressModule::where('module_id', $progressActivity->module_id)->first();
        $p_assignment->cs_id = $progressModule->cs_id;

        $assignDate = \Naeem\Helpers\Helper::convert_date($request->input('assign_date'));
        $completeDate = \Naeem\Helpers\Helper::convert_date($request->input('complete_date'));
       
        $p_assignment->assign_date = $assignDate;
        $p_assignment->complete_date = $completeDate;
       
        /* Days Difference Calculation */
        $daysDiff = 0;
        if ($assignDate != null && $completeDate != null) {
            $completion_date = new \DateTime($request->input('complete_date'));
            $assign_date = new \DateTime($request->input('assign_date'));
            $interval = $completion_date->diff($assign_date);
            $daysDiff = $interval->format('%a') + 1;
        }

        $p_assignment->days_diff = $daysDiff;       
        $p_assignment->remarks = $request->input('remarks');
        $p_assignment->user_id = $request->input('user_id');
        $p_assignment->assign_status = ($request->input('assign_status')) ? $request->input('assign_status') : '0';
        $p_assignment->save();

        Session::flash('success', 'Progress Assignment has been updated successfully.');
        return Redirect('progress_assignment');
    }

    public function show($id)
    {
        $page_title = 'Show Progress Assignment';
        $assignment = DB::table('TBL_PROGRESS_ASSIGNMENT')->where('assignment_id', $id)
            ->join('TBL_PROGRESS_MODULES', 'TBL_PROGRESS_ASSIGNMENT.module_id', '=', 'TBL_PROGRESS_MODULES.module_id')
            ->join('TBL_PROGRESS_CORE_SYSTEM', 'TBL_PROGRESS_ASSIGNMENT.cs_id', '=', 'TBL_PROGRESS_CORE_SYSTEM.cs_id')
            ->join('TBL_PROGRESS_ACTIVITIES', 'TBL_PROGRESS_ASSIGNMENT.activity_id', '=', 'TBL_PROGRESS_ACTIVITIES.activity_id')
            ->select(DB::raw('TBL_PROGRESS_MODULES.title as mod_title, TBL_PROGRESS_CORE_SYSTEM.title as cs_title, TBL_PROGRESS_ACTIVITIES.title as activity_title, TBL_PROGRESS_ASSIGNMENT.assignment_id as assignment_id, TBL_PROGRESS_ASSIGNMENT.assign_date as assign_date, TBL_PROGRESS_ASSIGNMENT.complete_date as complete_date, TBL_PROGRESS_ASSIGNMENT.remarks as remarks'))
            ->get();
        return view('progress_assignment.show', compact('page_title', 'assignment'));
    }

    public function destroy($id)
    {
        DB::table('TBL_PROGRESS_ASSIGNMENT')->where('assignment_id', '=', $id)->delete();
        Session::flash('success', ' Progress Assignment has been deleted successfully.');
        return redirect('progress_assignment');
    }
}
