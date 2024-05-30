<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\MeetingDocument;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MeetingDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $meeting_doc = MeetingDocument::with('doctsandard')->get();
        return view('meeting_document.create',compact('meeting_doc'));
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
            'md_title' => 'required|string|max:255',
            'md_status' => 'required|string|max:255',
            'md_edoc' => 'mimes:jpeg,bmp,png,jpg,xlsx,pdf,html|max:1000',
            'md_upload_date' => 'required|string|max:255',
            'doc_id' => 'required|integer', // Changed to integer
            'meeting_id' => 'required|integer', // Changed to integer
        ];
        
        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
        
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
     
        $meetingdoc = new MeetingDocument();
        $meetingdoc->md_title = $request->md_title;
        $meetingdoc->md_status = $request->md_status;
        // $meetingdoc->md_edoc = $request->md_edoc;
        $meetingdoc->md_upload_date = $request->md_upload_date;
        $meetingdoc->doc_id = (int)$request->doc_id; 
        $meetingdoc->meeting_id = (int)$request->meeting_id; 
        if ($request->hasFile('md_edoc')) {
            $file = $request->file('md_edoc');
              $destinationPath = 'public/Meeting-Document';
              $filename = time() . '_' . $file->getClientOriginalName();
              $file->move($destinationPath, $filename);
              $meetingdoc->md_edoc = $filename;
          }
        $meetingdoc->save();
        
      
        return redirect('/meeting/show/' . $meetingdoc->meeting_id)->with('success', 'Meeting document created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $doc = MeetingDocument::where('meeting_id', $id)->with('doctsandard')->get();
        return response()->json($doc);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      
    $meetingDoc= MeetingDocument::findOrFail($id);
    
    $alldoc= MeetingDocument::with('doctsandard')->get();
    return view('meeting_document.edit', compact('meetingDoc','alldoc'));
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
            'md_title' => 'required|string|max:255',
            'md_status' => 'required|string|max:255',
            'md_edoc' => 'mimes:jpeg,bmp,png,jpg,xlsx,pdf,html|max:1000',
            'md_upload_date' => 'required|string|max:255',
            'doc_id' => 'required|integer',
            'meeting_id' => 'required|integer',
        ];
    
        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
        
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
    
        $meetingDoc = MeetingDocument::findOrFail($id);
        $meetingDoc->md_title = $request->md_title;
        $meetingDoc->md_status = $request->md_status;
        // $meetingDoc->md_edoc = $request->md_edoc;
        $meetingDoc->md_upload_date = $request->md_upload_date;
        $meetingDoc->doc_id = (int)$request->doc_id;
        $meetingDoc->meeting_id = (int)$request->meeting_id;
        if ($request->hasFile('md_edoc')) {
            $file = $request->file('md_edoc');
              $destinationPath = 'public/Meeting-Document';
              $filename = time() . '_' . $file->getClientOriginalName();
              $file->move($destinationPath, $filename);
              $meetingDoc->md_edoc = $filename;
          }
        
        $meetingDoc->save();
        
        // Redirect to the meeting show page
        return redirect('/meeting/show/' . $meetingDoc->meeting_id)->with('success', 'Meeting Document updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $meetingDoc=MeetingDocument::find($id);
        $meetingDoc->delete();
        return back()->withSuccess('delete successfully');
    }
}
