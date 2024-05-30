<?php

namespace App\Http\Controllers;


use App\Models\Family;
use App\Models\Lecturer;
use App\Models\Lecturer_nom;
use Illuminate\Http\Request;
use DB;
use Session;
use Validator;
use Input;

class Lec_nomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        ini_set('max_execution_time', 5000);
//        ini_set('memory_limit', '5000M');
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
    public function create($tc_id,$tran_id)
    {
        $page_title = 'Add Lecturer Nomination';
        $lecturer = ['' => 'Select Lecturer'] + Lecturer::lists('lecturer_name', 'lecturer_id')->all();
        return view('lec_nomination.create', compact('page_title','lecturer', 'tran_id', 'tc_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(),
            [
                'lec_name' => 'required',
            ]);
        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $record = Lecturer_nom::orderBy('tl_id', 'desc')->first();
        $book = new Lecturer_nom();
        $book->tl_id = ($record) ? $record->tl_id + 1 : 1;
        $book->lec_id = $request->input('lec_name');
        $book->tc_id = $request->input('tc_id');
        $book->training_id = $request->input('trn_id');
        if ($request->hasFile('edoc')) {
            $file = $request->file('edoc');
            $new_filename = 'lect_' . $book->tl_id;
            $path = 'public/lecturer';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $book->tl_edoc = $completeUrl;
        }
	//echo "<pre>"; print_r($book); die;
        $book->save();
        Session::flash('success', 'Lecturer Nomination added successfully.');
        return Redirect('/training_course/'.$request->input('tc_id'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = 'Lecture Nomination';
        $data = DB::table('tbl_training_nomination_lec')
            ->join('v_training_course', 'tbl_training_nomination_lec.tc_id', '=', 'v_training_course.course_id')
             ->where('tbl_training_nomination_lec.tl_id', '=', $id)
           ->select('tbl_training_nomination_lec.training_id as training_id', 'v_training_course.course as course', 'tbl_training_nomination_lec.tl_edoc as tl_edoc')
            ->first();
        // echo "<pre>"; print_r($data); die;
        return view('lec_nomination.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //echo "test"; die;
        $page_title = 'Lecturer Nomination';
        $data = Lecturer_nom::find($id);
        $lecturer = ['' => 'Select Lecturer'] + Lecturer::lists('lecturer_name', 'lecturer_id')->all();
        return view('lec_nomination.edit', compact('page_title', 'lecturer', 'data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $messages = array(
            'lec_id' => 'The :attribute field is required.',
        );

        $validator = Validator::make($request->all(), [

            'lec_id' => 'required',

        ], $messages);

        if($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
        $order = Lecturer_nom::find($id);
        $lec_id = $request->input('lec_id');
       if($request->hasFile('edoc')) {
            $file = $request->file('edoc');
            /// new file name
            $new_filename = 'lect_'.$id;
            $path = 'public/lecturer';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $orderEdoc = $completeUrl;
        }
        else{
            if($order->tl_edoc)
                $orderEdoc = $order->tl_edoc;
            else
                $orderEdoc = '';
        }
        $updateFields = array(
            'lec_id' => $lec_id,
            'tl_edoc' => $orderEdoc
        );
        DB::table('TBL_TRAINING_NOMINATION_LEC')->where('TL_ID', '=', $id)->update($updateFields);
        Session::flash('success', 'Lecturer Nomination updated successfully.');
        return Redirect('/training_course/'.$request->input('tran_id'));

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Lecturer_nom::find($id);
        DB::table('TBL_TRAINING_NOMINATION_LEC')->where('TL_ID', '=', $id)->delete();
        Session::flash('success', 'Lecturer Nomination has been deleted successfully.');
        return Redirect('/training_course/'.$data->training_id);
    }
}
