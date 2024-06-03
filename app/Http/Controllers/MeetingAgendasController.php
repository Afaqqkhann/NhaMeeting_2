<?php

namespace App\Http\Controllers;

use App\Models\Wing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Meeting;


class MeetingAgendasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "Meetings Agendas";
        $meetingAgendas = Agenda::with('meeting.meetingType', 'wing')->orderBy('ma_title', 'asc')->get();

        return view('meeting_agenda.index', compact('page_title', 'meetingAgendas'));

       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $agendas = Wing::all();
        return view('meeting_agenda.create',compact('agendas'));
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
            'ma_title' => 'required|string|max:255',
            'ma_status' => 'required|string|max:255',
            'ma_edoc' => 'mimes:jpeg,bmp,png,jpg,xlsx,pdf,html|max:1000',
            'ma_upload_date' => 'required|string|max:255',
            'action_id' => 'required|integer', // Changed to integer
            'meeting_id' => 'required|integer', // Changed to integer
        ];
        
        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
        
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // If validation passes, create and save the Agenda model
        $meetingagenda = new Agenda();
        $meetingagenda->ma_title = $request->ma_title;
        $meetingagenda->ma_status = $request->ma_status;
        // $meetingagenda->ma_edoc = $request->ma_edoc;
        $meetingagenda->ma_upload_date = $request->ma_upload_date;
        $meetingagenda->action_id = (int)$request->action_id; 
        $meetingagenda->meeting_id = (int)$request->meeting_id; 
        if ($request->hasFile('ma_edoc')) {
            $file = $request->file('ma_edoc');
              $destinationPath = 'public/agendas';
              $filename = time() . '_' . $file->getClientOriginalName();
              $file->move($destinationPath, $filename);
              $meetingagenda->ma_edoc = $filename;
          }
        $meetingagenda->save();
        
        // Redirect to the index page
        return redirect('/meeting/show/' . $meetingagenda->meeting_id)->with('success', 'Meeting Agenda created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $agendas = Agenda::where('meeting_id', $id)->with('wing')->get();
      
        return response()->json($agendas);
        return view ('meeting_agenda.show',compact('agendas'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
{
    // Find the agenda by ID
    $meetingAgenda = Agenda::findOrFail($id);
    $allagendas = Wing::all();
   
    return view('meeting_agenda.edit', compact('meetingAgenda','allagendas'));
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
        // Validation rules
        $rules = [
            'ma_title' => 'required|string|max:255',
            'ma_status' => 'required|string|max:255',
            'ma_edoc' => 'mimes:jpeg,bmp,png,jpg,xlsx,pdf,html|max:1000',
            'ma_upload_date' => 'required|string|max:255',
            'action_id' => 'required|integer',
            'meeting_id' => 'required|integer',
        ];
    
        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
        
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
    
        $meetingAgenda = Agenda::findOrFail($id);
        $meetingAgenda->ma_title = $request->ma_title;
        $meetingAgenda->ma_status = $request->ma_status;
        // $meetingAgenda->ma_edoc = $request->ma_edoc;
        $meetingAgenda->ma_upload_date = $request->ma_upload_date;
        $meetingAgenda->action_id = (int)$request->action_id;
        $meetingAgenda->meeting_id = (int)$request->meeting_id;
        if ($request->hasFile('ma_edoc')) {
            $file = $request->file('ma_edoc');
              $destinationPath = 'public/agendas';
              $filename = time() . '_' . $file->getClientOriginalName();
              $file->move($destinationPath, $filename);
              $meetingAgenda->ma_edoc = $filename;
          }
        $meetingAgenda->save();
        
        // Redirect to the meeting show page
        return redirect('/meeting/show/' . $meetingAgenda->meeting_id)->with('success', 'Agenda updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $meetingAgenda=Agenda::find($id);
        $meetingAgenda->delete();
        return back()->withSuccess('delete successfully');
    }
}
