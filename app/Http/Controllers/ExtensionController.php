<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Models\Order;
use DB;
use Illuminate\Http\Request;
use Input;
use Session;
use Validator;

class ExtensionController extends Controller
{
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $page_title = 'Add Extension';
        $order = ['' => 'Select Order'] + Order::lists('order_subject', 'order_id')->all();
        return view('extension.create', compact('page_title', 'id', 'order'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [

            'ext_from.required' => 'The Extension From field is required.',
            'ext_to.required' => 'The Extension To field is required.',
        ];
        $validation = Validator::make($request->all(),
            [
                'ext_from' => 'required',
                'ext_to' => 'required',
            ],$messages);
        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $record = Extension::orderBy('ext_id', 'desc')->first();
        $extension = new Extension();
        $extension->ext_id = ($record) ? $record->ext_id + 1 : 1;
        $extension->emp_id = $request->input('id');
        $extension->ext_from = ($request->input('ext_from')) ? date('Y-m-d', strtotime($request->input('ext_from'))) : '';
        $extension->ext_to = ($request->input('ext_to')) ? date('Y-m-d', strtotime( $request->input('ext_to'))) : '';
//$extension->period = $request->input('days');
        $extension->app_authority = $request->input('approval');
        $extension->remarks = $request->input('remarks');
        $extension->order_id = $request->input('order');
     
        $extension->save();
        Session::flash('success', 'Extension added successfully.');
        return Redirect('employee/emp_detail/' . $request->input('id'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Extension';
        $data = DB::table('TBL_EMP_EXTENSION')->where('EXT_ID', '=', $id)
            ->select('ext_id', 'emp_id', 'app_authority', 'ext_from', 'ext_to', (DB::raw("
            trunc(months_between(ext_to, ext_from) / 12) as ext_years
        ,trunc(mod(months_between(ext_to, ext_from), 12)) as ext_months
        ,trunc(ext_to - add_months(ext_from, trunc(months_between(ext_to, ext_from)))) as ext_days
 ")))->first();
        //echo "<pre>";
        //print_r($data);die;
        return view('extension.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Extension';
        $data = Extension::find($id);
//        echo "<pre>"; print_r($data); die;
        $order = ['' => 'Select Order'] + Order::lists('order_subject', 'order_id')->all();
        return view('extension.edit', compact('page_title', 'data', 'order'));
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
        $messages = [

            'ext_from.required' => 'The Extension From field is required.',
            'ext_to.required' => 'The Extension To field is required.',
        ];
        $validation = Validator::make($request->all(),
            [
                'ext_from' => 'required',
                'ext_to' => 'required',
            ], $messages);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $e_from = ($request->input('ext_from')) ? date('Y-m-d', strtotime($request->input('ext_from'))) : '';
        $e_to = ($request->input('ext_to')) ? date('Y-m-d', strtotime($request->input('ext_to'))) : '';

        $departmentArray = array(
            'ext_from' => $e_from,
            'emp_id' => $request->input('id'),
            'ext_to' => $e_to,
            //'period' => $request->input('days'),
            'app_authority' => $request->input('approval'),
            'remarks' => $request->input('remarks'),
            'order_id' => $request->input('order'),
            'ext_status' => 1,
        );
        DB::table('TBL_EMP_EXTENSION')->where('EXT_ID', '=', $id)->update($departmentArray);
        Session::flash('success', 'Extension updated successfully.');
        return Redirect('employee/emp_detail/' . $request->input('id'));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Extension::find($id);
        DB::table('TBL_EMP_EXTENSION')->where('EXT_ID', '=', $id)->delete();
        Session::flash('success', 'Extension has been deleted successfully.');
        return Redirect('employee/emp_detail/' . $data->emp_id);
    }
}
