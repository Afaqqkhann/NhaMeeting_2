<?php

namespace App\Http\Controllers;

use App\Models\MeetingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MeetingTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "Meeting Type";
        $meetingType = MeetingType::all();
        // dd($meetingType);
        return view('meeting_type.index', compact('page_title', 'meetingType'));
     }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view ('meeting_type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'mt_title' => 'required|string|max:255',
            'mt_status' => 'required|string|max:255',
        ];
    
        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } 
         // If validation passes, create and save the DocStandard model
    $meeting_type = new MeetingType();
    $meeting_type->mt_title = $request->mt_title;
    $meeting_type->mt_status = $request->mt_status;
    $meeting_type->save();

    Session::flash('success', 'Meeting Type has been added successfully.');
    return redirect('/meeting_types');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $meeting_type = MeetingType::find($id); 
        return view('meeting_type.edit', ['meeting_types' => $meeting_type]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'mt_title' => 'required|string|max:255',
            'mt_status' => 'required|string|max:255',
        ];
    
        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } 
    
        $meeting_types =  MeetingType:: find($id);
        $meeting_types->mt_title = $request->mt_title;
        $meeting_types->mt_status = $request->mt_status;
        $meeting_types->update();

    // Redirect to the index page
    Session::flash('success', 'Meeting Type has been updated  successfully.');
    return redirect('/meeting_types');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $meeting_type=MeetingType::find($id);
        $meeting_type->delete();
        return back()->withSuccess('delete successfully');
    }
}
