<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\Wing;
use Illuminate\Http\Request;
use Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class WingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "Wing";
        $actions = Wing::all();
        return view('Wing.index', compact('actions','page_title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view ('Wing.create');
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
            'action_title' => 'required|string|max:255',
            'action_status' => 'required|string|max:255',
        ];
    
        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } 
         // If validation passes, create and save the DocStandard model
    $actions = new Wing();
    $actions->action_title = $request->action_title;
    $actions->action_status = $request->action_status;
    $actions->save();

    // Redirect to the index page
    Session::flash('success', 'Wing created successfully');
    return redirect('/wing');
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
        $actions = Wing::find($id); // Assuming $id is the ID of the document standard
        return view('Wing.edit', ['action' => $actions]);
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
            'action_title' => 'required|string|max:255',
            'action_status' => 'required|string|max:255',
        ];
    
        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } 
    
        $action =  Wing:: find($id);
        $action->action_title = $request->action_title;
        $action->action_status = $request->action_status;
        $action->update();

    // Redirect to the index page
    return redirect('/wing')->with('success', 'Wing update successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $actions=Wing::find($id);
        $actions->delete();
        return back()->withSuccess('delete successfully');
    }
}
