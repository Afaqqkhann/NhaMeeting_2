<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingType;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "Meeting";
        $meetings = Meeting::with('meetingType')->get();

         return view('Meeting.index', compact('page_title', 'meetings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $meetings = MeetingType::all();
        // dd($meetings); 
        return view('Meeting.create', compact('meetings'));
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
        'meeting_date' => 'required|string|max:255',
        'meeting_no' => 'required|string|max:255',
        'meeting_type' => 'required|string|max:255',
        'meeting_upload_date' => 'required|string|max:255',
        'meeting_edoc' => 'mimes:jpeg,bmp,png,jpg,xlsx,pdf,html|max:1000',
        'meeting_status' => 'required|string|max:255',
    ];

    // Validate the request data
    $validator = Validator::make($request->all(), $rules);

    // Check if validation fails
    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $meeting = new Meeting();
    $meeting->meeting_date = $request->meeting_date;
    $meeting->meeting_no = $request->meeting_no;
    $meeting->meeting_type = $request->meeting_type;
    $meeting->meeting_upload_date = $request->meeting_upload_date;
    $meeting->meeting_status = $request->meeting_status;
    if ($request->hasFile('meeting_edoc')) {
      $file = $request->file('meeting_edoc');
        $destinationPath = 'public/meetings';
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move($destinationPath, $filename);
        $meeting->meeting_edoc = $filename;
    }

    $meeting->save();

    // Redirect to the index page
    Session::flash('success', 'Meeting created successfully');
    return redirect('dashboard/meeting');
}


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = "Meeting Information";
        $meeting = Meeting::find($id);
        // $meetingall = Meeting::find($id);
        // return view('meeting_document.create', compact('meeting'));
        if (!$meeting) {
            abort(404); // Or handle the error appropriately
        }
        
        return view('Meeting.show', compact('page_title','meeting'));

        
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
{
    $meeting = Meeting::with('meetingType')->find($id); 
    $allMeetings = MeetingType::all(); // Assuming you want to show all meeting types in the dropdown
    return view('Meeting.edit', ['meetings' => $meeting, 'allMeetings' => $allMeetings]);
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
            'meeting_no' => 'required|string|max:255',
            'meeting_status' => 'required|string|max:255',
            'meeting_edoc' => 'mimes:jpeg,bmp,png,jpg,xlsx,pdf,html|max:1000',
            'meeting_type' => 'required|string|max:255',
            'meeting_upload_date'=> 'required|string|max:255',
            'meeting_date'=> 'required|string|max:255',
        ];
    
        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } 
       
    
        $meetings =  Meeting::find($id);
        $meetings->meeting_no = $request->meeting_no;
        // $meetings->meeting_edoc = $request->meeting_edoc;
        $meetings->meeting_status = $request->meeting_status;
        $meetings->meeting_upload_date = $request->meeting_upload_date;
        $meetings->meeting_type = $request->meeting_type;
        $meetings->meeting_date = $request->meeting_date;
       
        if ($request->hasFile('meeting_edoc')) {
            $file = $request->file('meeting_edoc');
              $destinationPath = 'public/meetings';
              $filename = time() . '_' . $file->getClientOriginalName();
              $file->move($destinationPath, $filename);
              $meetings->meeting_edoc = $filename;
          }
        $meetings->update();

    // Redirect to the index page
    Session::flash('success', 'Meeting update successfully');
    return redirect('dashboard/meeting');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $meeting=Meeting::find($id);
        $meeting->delete();
        return back()->withSuccess('delete successfully');
    }
}
