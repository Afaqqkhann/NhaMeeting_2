<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Tranings;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Validator;
use Input;

class InstituteController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add Institute';
        $training_types = DB::table('tbl_training_head')->where('th_type', '=', 5)->get();
        $training_type = array(null => 'Select Training Category');
        foreach ($training_types as $authorty1) {
            $training_type[$authorty1->th_id] = $authorty1->th_title; //. ' ' . $package->contract_code;
        }
        $organizations = DB::table('tbl_training_head')->where('th_type', '=', 1)->orderBy('th_title', 'asc')->get();
        $organization = array(null => 'Select organization');
        foreach ($organizations as $authorty2) {
            $organization[$authorty2->th_id] = $authorty2->th_title; //. ' ' . $package->contract_code;
        }
        $place_ids = DB::table('tbl_training_head')->where('th_type', '=', 6)->orderBy('th_title', 'asc')->get();
        $place_id = array(null => 'Select Place');
        foreach ($place_ids as $authorty3) {
            $place_id[$authorty3->th_id] = $authorty3->th_title; //. ' ' . $package->contract_code;
        }
        $earned_through =  ['' => 'Select Order Type', 'CB' => 'Capacity Building', 'OO' => 'Official Obligation', 'OV' => 'Official Visit'];
        return view('institute.create', compact('page_title', 'training_type', 'organization', 'place_id', 'earned_through'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = array(
            'required' => 'The :attribute field is required.',
        );

        $validator = Validator::make($request->all(), [

            'organization' => 'required',

        ], $messages);

        if ($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());

        $ord = Tranings::orderBy('training_id', 'desc')->first();

        $order = new Tranings();
        $order->training_id = ($ord) ? $ord->training_id + 1 : 1;
        $order->expenditure = $request->input('expend');
        $order->training_type_id = $request->input('training_type');
        $order->earned_through = $request->input('earned_through');
        $order->organization_id = $request->input('organization');
        $order->place_id = $request->input('place_id');
        $order->comments = $request->input('Comments');
        if ($request->hasFile('e_doc')) {
            $file = $request->file('e_doc');
            $new_filename = 'Trainings' . $order->training_id;
            $path = 'public/NHA-IS/TRAININGS';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $order->training_edoc = $completeUrl;
        }


        $order->save();
        Session::flash('success', 'Training created successfully.');
        return redirect('hrd');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Institute';
        $data = Tranings::find($id);
        $training_course = DB::table('mv_trainings')->where('training_id', '=', $id)->orderBy('training_id', 'DESC')->first();
        //echo "<pre>"; print_r($training_course); die;
        return view('institute.show', compact('page_title', 'data', 'training_course'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //echo "test"; die;
        $page_title = 'Edit Institute';
        $data = Tranings::find($id);
        // echo "<pre>"; print_r($data); die;


        $training_types = DB::table('tbl_training_head')->where('th_type', '=', 5)->get();
        $training_type = array(null => 'Select Training Category');
        foreach ($training_types as $authorty1) {
            $training_type[$authorty1->th_id] = $authorty1->th_title; //. ' ' . $package->contract_code;
        }
        $organizations = DB::table('tbl_training_head')->where('th_type', '=', 1)->get();
        $organization = array(null => 'Select organization');
        foreach ($organizations as $authorty2) {
            $organization[$authorty2->th_id] = $authorty2->th_title; //. ' ' . $package->contract_code;
        }
        // echo "<pre>"; print_r($organization); die;
        $place_ids = DB::table('tbl_training_head')->where('th_type', '=', 6)->get();
        $place_id = array(null => 'Select Place');
        foreach ($place_ids as $authorty3) {
            $place_id[$authorty3->th_id] = $authorty3->th_title; //. ' ' . $package->contract_code;
        }
        $earned_through =  ['' => 'Select Order Type', 'CB' => 'Capacity Building', 'OO' => 'Official Obligation', 'OV' => 'Official Visit'];






        return view('institute.edit', compact('page_title', 'data', 'training_type', 'organization', 'place_id', 'earned_through'));
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
        $messages = array(
            'required' => 'The :attribute field is required.',
        );

        $validator = Validator::make($request->all(), [

            'organization' => 'required',

        ], $messages);

        if ($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
        $order = Tranings::find($id);
        $expend = $request->input('expend');
        $training_type = $request->input('training_type');
        $earned = $request->input('earned');
        $organization = $request->input('organization');
        $place_id = $request->input('place_id');
        $comments = $request->input('comments');
        if ($request->hasFile('e_doc')) {
            $file = $request->file('e_doc');
            /// new file name
            $new_filename = 'Training_' . $id;
            $path = 'public/NHA-IS/TRAININGS';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $orderEdoc = $completeUrl;
        } else {
            if ($order->e_doc)
                $orderEdoc = $order->e_doc;
            else
                $orderEdoc = '';
        }
        $updateFields = array(
            'expenditure' => $expend,
            'training_type_id' => $training_type,
            'earned_through' => $earned,
            'organization_id' => $organization,
            'place_id' => $place_id,
            'comments' => $comments,
            'training_edoc' => $orderEdoc
        );

        DB::table('TBL_TRAININGS')->where('training_id', '=', $id)->update($updateFields);


        //after order save
        /* if(Input::hasFile('e_doc')) {
            $path = 'public/NHA-IS/ORDER';
            $table = 'TBL_ORDER';
            $primary_field = 'ORDER_ID';
            $file = Input::file('e_doc');
            $edoc_field = 'E_DOC ';

            $uploader = new UploadController();
            $uploader->upload_edoc($file, 'ORDER_'.$order->order_id, $path, $table, $primary_field, $order->order_id, $edoc_field);
        }*/
        Session::flash('success', 'Training updated successfully.');
        return redirect('hrd');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        DB::table('TBL_TRAININGS')->where('training_id', '=', $id)->delete();
        Tranings::where('training_id', '=', $id)->delete();
        Session::flash('success', ' Training has been deleted successfully.');

        return redirect('hrd');
    }
}
