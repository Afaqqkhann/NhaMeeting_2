<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Education;
use DB;
use Illuminate\Http\Request;
use Input;
use Session;

class EducationController extends Controller
{
       

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $page_title = 'Add Education';
        $doctype = array('' => 'Select Document Type');

        $emp_documents = DB::table('TBL_EDUCTION_TYPE')->orderBy('edu_type_id', 'ASC')->get();
        foreach ($emp_documents as $row) {
            $doctype[$row->edu_type_id] = $row->title;
        }
        return view('education.create', compact('page_title', 'id', 'doctype'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        $validation = Validator::make($request->all(),
        //            [
        //                'shared'  =>     'required',
        //            ]);
        //        if ($validation->fails())
        //        {
        //            return redirect()->back()->withInput()->withErrors($validation->errors());
        //        }
        $record = Education::orderBy('education_id', 'desc')->first();
        $book = new Education();
        $book->education_id = ($record) ? $record->education_id + 1 : 1;
        $book->institute_name = $request->input('inst_name');
        $book->emp_id = $request->input('id');
        $book->sessions = ($request->input('session')) ? date('Y-m-d', strtotime($request->input('session'))) : '';
        $book->document_name = $request->input('doc_name');
        $book->document_type_id = $request->input('doc_type');
        $book->total_marks = $request->input('total_marks');
        $book->obtained_marks = $request->input('obt_marks');
        $book->comments = $request->input('comments');
        $book->education_status = 1;
        $book->verify = 0;
        $book->save();
        Session::flash('success', 'Education added successfully.');
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
        $data = Education::find($id);
        // echo "<pre>"; print_r($data); die;
        return view('education.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Education';

        $data = Education::find($id);

        $doctype = array('' => 'Select Document Type');

        $emp_documents = DB::table('TBL_EDUCTION_TYPE')->orderBy('edu_type_id', 'ASC')->get();
        foreach ($emp_documents as $row) {
            $doctype[$row->edu_type_id] = $row->title;
        }

        return view('education.edit', compact('page_title', 'data', 'doctype'));
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
		$session = ($request->input('session')) ? date('Y-m-d', strtotime($request->input('session'))) : '';
        $education_status = ($request->input('education_status')) ? $request->input('education_status') : '0';
        $departmentArray = array(
            'institute_name' => $request->input('inst_name'),
            'emp_id' => $request->input('id'),
            'document_name' => $request->input('doc_name'),
            'document_type_id' => $request->input('doc_type'),
            'sessions' => $session,
            'total_marks' => $request->input('total_marks'),
            'obtained_marks' => $request->input('obt_marks'),
            'comments' => $request->input('comment'),
            'education_status' => $education_status,
            'verify' => 0,

        );

        //print_r($departmentArray);die;

        DB::table('TBL_EDUCATION')->where('EDUCATION_ID', '=', $id)->update($departmentArray);

        Session::flash('success', 'Education updated successfully.');

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
        $data = Education::find($id);
        DB::table('TBL_EDUCATION')->where('EDUCATION_ID', '=', $id)->delete();
        Session::flash('success', 'Education has been deleted successfully.');

        return Redirect('employee/emp_detail/' . $data->emp_id);
    }
}
