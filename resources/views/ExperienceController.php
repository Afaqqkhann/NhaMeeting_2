<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use DB;
use Illuminate\Http\Request;
use Input;
use Session;
use Validator;

class ExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        ini_set('max_execution_time', 5000);
        ini_set('memory_limit', '5000M');
//        $page_title = 'family';
        //        $data = DB::table('TBL_FAMILY ')->orderBy('FAMILY_ID', 'DESC')
        //            ->join('TBL_EMP', 'TBL_FAMILY.emp_id', '=', 'TBL_EMP.emp_id')
        //            ->join('TBL_RELATION', 'TBL_FAMILY.relationship', '=', 'TBL_RELATION.r_id')
        //            ->get();
        //
        //        return view('family.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $page_title = 'Add Experience';
        $doctype = ['' => 'Select Organization Type', '1' => 'NGO', '2' => 'Private', '3' => 'Semi Government', '4' => 'Government', '5' => 'Autonoums Body'];
        return view('experience.create', compact('page_title', 'id', 'doctype'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(),
            [
                'org_name' => 'required',
            ]);
        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $record = Experience::orderBy('exp_id', 'desc')->first();
        $book = new Experience();
        $book->exp_id = ($record) ? $record->exp_id + 1 : 1;
        $book->emp_id = $request->input('id');
        $book->org_name = $request->input('org_name');
        $book->org_type = $request->input('org_type');
        $book->joining_date = ($request->input('j_date')) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('j_date')))) : '';
        $book->ending_date = ($request->input('e_date')) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('e_date')))) : '';
        $book->designation = $request->input('designation');
        $book->grade_bs = $request->input('bs');
        $book->service_type = $request->input('service_type');
        $book->job_description = $request->input('j_descrip');
        //$book->duration = $request->input('duration');
        $book->reason_leaving = $request->input('reason_leaving');
        $book->exp_status = 1;
        $book->save();
        Session::flash('success', 'Experience added successfully.');
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
        $page_title = 'Education';
        $data = Experience::find($id);
        // echo "<pre>"; print_r($data); die();
        return view('experience.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Experience';
        $data = Experience::find($id);
        $doctype = ['' => 'Select Organization Type', '1' => 'NGO', '2' => 'Private', '3' => 'Semi Government', '4' => 'Government', '5' => 'Autonoums Body'];
        return view('experience.edit', compact('page_title', 'data', 'doctype'));
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
            'org_name.required' => 'The Organization Name field is required.',
        ];
        $validation = Validator::make($request->all(),
            [
                'org_name' => 'required',
            ], $messages);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $joining_date = ($request->input('j_date')) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('j_date')))) : '';
        $endining_date = ($request->input('e_date')) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('e_date')))) : '';
        $departmentArray = array(
            'org_name' => $request->input('org_name'),
            'emp_id' => $request->input('id'),
            'org_type' => $request->input('org_type'),
            'designation' => $request->input('designation'),
            'grade_bs' => $request->input('bs'),
            'joining_date' => $joining_date,
            'ending_date' => $endining_date,
            'service_type' => $request->input('service_type'),
            'job_description' => $request->input('j_description'),
            //'duration' => $request->input('duration'),
            'reason_leaving' => $request->input('reason_leaving'),
            'exp_status' => 1,
        );
        DB::table('TBL_EXPERIENCE')->where('EXP_ID', '=', $id)->update($departmentArray);
        Session::flash('success', 'Experience updated successfully.');
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
        $data = Experience::find($id);
        DB::table('TBL_EXPERIENCE')->where('EXP_ID', '=', $id)->delete();
        Session::flash('success', 'Experience has been deleted successfully.');
        return Redirect('employee/emp_detail/' . $data->emp_id);
    }
}
