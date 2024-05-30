<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Family;
use App\Models\Relation;
use DB;
use Illuminate\Http\Request;
use Input;
use Session;
use Validator;
use Auth;

class Family_memisController extends Controller
{
    public function __construct()
    {
       /* if (!Auth::user()->can('family_memis')) {
            abort(403);
        }*/
    }
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        ini_set('max_execution_time', 5000);
        ini_set('memory_limit', '5000M');
        $page_title = 'family';
        $data = DB::table('TBL_FAMILY ')->orderBy('FAMILY_ID', 'DESC')
            ->join('TBL_EMP', 'TBL_FAMILY.emp_id', '=', 'TBL_EMP.emp_id')
            ->join('TBL_RELATION', 'TBL_FAMILY.relationship', '=', 'TBL_RELATION.r_id')
            ->get();

        return view('family.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        //echo "test";die;
        $page_title = 'Add Family';
        //$gender = ['' => 'Select Gender', '1' => 'Male', '0' => 'Female'];
        //$dependent = ['' => 'Select Dependent', '1' => 'Yes', '0' => 'No'];
        //$alive = [''=> 'Select Alive','1' => 'Yes','0' => 'No'];
        $relations = ['' => 'Select Relation'] + Relation::lists('r_title', 'r_id')->all();

        $validities = ['' => 'Select Validity'];
        $valid = DB::table('TBL_FAMILY_VALIDITY')->orderBy('fv_title', 'ASC')->get();

        foreach ($valid as $v) {
            $validities[$v->fv_id] = $v->fv_title;
        }

        $relation = array_diff_key($relations, array_flip(["3", "4", "9"]));

        return view('family_memis.create', compact('page_title', 'validities', 'relation', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // echo "store1"; die;
        $messages = [
            'name.required' => 'The Name field is required.',
            'dob.required' => 'The DOB field is required.',
            'relation.required' => 'The Relationship field is required.',
            //'dependent.required' => 'The Dependent field is required.',
            'cnic.required' => 'The CNIC field is required.',
            'picture.required' => 'The Picture is required.',
            'fv_id.required' => 'The Validity field is required.',
            'remarks.max' => 'The Remarks length must be within 500 characters.',

        ];

        $validation = Validator::make($request->all(),
            [
                'name' => 'required',
                'dob' => 'required',
                'cnic' => 'required',
                //'dependent' => 'required',
                'relation' => 'required',
                'remarks' => 'max:500',
                'picture' => 'required',
                'fv_id' => 'required',
				'affidavit' =>'mimes:pdf',

            ], $messages);
        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $record = Family::orderBy('family_id', 'desc')->first();
        $fam = new Family();
        $fam->family_id = ($record) ? $record->family_id + 1 : 1;
        $fam->name = $request->input('name');
        $fam->emp_id = $request->input('id');
        $fam->date_of_birth = ($request->input('dob')) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('dob')))) : '';
        $fam->cnic = $request->input('cnic');
        $fam->gender = $request->input('gender');
        $fam->relationship = $request->input('relation');
        $fam->remarks = $request->input('remarks');
        $fam->fv_id = $request->input('fv_id');
        $fam->alive = 1; // 1- Alive Yes
        $fam->dependent = 1; // 1- Yes
        $fam->family_status = 1;
        $id = $fam->family_id;

        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $new_filename = 'user_' . $id;
            $path = 'public/family';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $fam->picture = $completeUrl;
        }
		
		if ($request->hasFile('affidavit')) {
            $file = $request->file('affidavit');
            $new_filename = 'affidavit_' . $id;
            $path = 'public/family/';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $fam->affidavit = $completeUrl;
			
        }
        // echo "<pre>"; print_r($fam);die;

        $fam->save();
        Session::flash('success', 'Family added successfully.');
        return Redirect('memis_listing/' . $request->input('id'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //echo "show";die;
        $page_title = 'Family';
        $data = Family::find($id);
        // echo "<pre>"; print_r($data); die;
        return view('family_memis.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //echo  "edit";die;
        $page_title = 'Family';
        $data = Family::find($id);
        // $gender = ['' => 'Select Gender', '1' => 'Male', '0' => 'Female'];
        //$dependent = ['' => 'Select Dependent', '1' => 'Yes', '0' => 'No'];

        $relations = ['' => 'Select Relation'] + Relation::lists('r_title', 'r_id')->all();

        $validities = ['' => 'Select Validity'];
        $valid = DB::table('TBL_FAMILY_VALIDITY')->orderBy('fv_title', 'ASC')->get();

        foreach ($valid as $v) {
            $validities[$v->fv_id] = $v->fv_title;
        }

        $relation = array_diff_key($relations, array_flip(["3", "4", "9"]));
        return view('family_memis.edit', compact('page_title', 'data', 'relation', 'validities'));

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
        $messages = [
            'name.required' => 'The Name field is required.',
            'dob.required' => 'The DOB field is required.',
            'relation.required' => 'The Relationship field is required.',

            'cnic.required' => 'The CNIC field is required.',
            'fv_id.required' => 'The Validity field is required.',
            'remarks.max' => 'The Remarks length must be within 500 characters.',

        ];

        $validation = Validator::make($request->all(),
            [
                'name' => 'required',
                'dob' => 'required',
                'cnic' => 'required',

                'relation' => 'required',
                'remarks' => 'max:500',
                'fv_id' => 'required',
				'affidavit' =>'mimes:pdf',

            ], $messages);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $user = Family::find($id);
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $new_filename = 'user_' . $user->family_id;
            $path = 'public/family';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $empImg = $completeUrl;
        } else {
            $empImg = $user->picture;
        }
		$famStatus = $user->family_status;
		
		if ($request->hasFile('affidavit')) {
            $file = $request->file('affidavit');
            $new_filename = 'affidavit_' . $user->family_id;
            $path = 'public/family';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $famAffidavit = $completeUrl;
			$famStatus = 1;
        } else {
            $famAffidavit = $user->affidavit;
        }

        $familyArray = array(
            'name' => $request->input('name'),
            'emp_id' => $request->input('id'),
            'date_of_birth' => ($request->input('dob')) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('dob')))) : '',

            'cnic' => $request->input('cnic'),
            'fv_id' => $request->input('fv_id'),
            //'dependent' => $request->input('dependent'),
            'gender' => $request->input('gender'),
            'relationship' => $request->input('relation'),
            //'alive' => $request->input('dependent'),
            'remarks' => $request->input('remarks'),
            'picture' => $empImg,
            'affidavit' => $famAffidavit,
			'family_status' => $famStatus,
        );

        //echo "<pre>";print_r($familyArray);die;

        DB::table('TBL_FAMILY')->where('FAMILY_ID', '=', $id)->update($familyArray);

        Session::flash('success', 'Family updated successfully.');

        return redirect('memis_listing/' . $request->input('id'));

    }
    public function destroy($id)
    {
        $emp_data = DB::table('TBL_FAMILY')->where('FAMILY_ID', '=', $id)->first();
        //echo "<pre>"; print_r($emp_data);die;
        DB::table('TBL_FAMILY')->where('FAMILY_ID', '=', $id)->delete();
        return redirect('memis_listing/' . $emp_data->emp_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */

}
