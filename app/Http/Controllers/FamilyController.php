<?php

namespace App\Http\Controllers;

use App\Models\Cadre;
use App\Models\Desig;
use App\Models\Employees\Employees;
use App\Models\Family;
use App\Models\Order;
use App\Models\Post;
use App\Models\Relation;
use App\Models\Section;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Validator;
use Input;

class FamilyController extends Controller
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
        $page_title = 'Family Detail';
        /*$data = DB::table('TBL_FAMILY ')->orderBy('FAMILY_ID', 'DESC')
            ->join('TBL_EMP', 'TBL_FAMILY.emp_id', '=', 'TBL_EMP.emp_id')
            ->join('TBL_RELATION', 'TBL_FAMILY.relationship', '=', 'TBL_RELATION.r_id')
            ->get();*/
        $data = DB::table('V_FAMILY')->get();

        return view('family.index', compact('page_title', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $page_title = 'Add Family';
        $gender = ['' => 'Select Gender', '1' => 'Male', '0' => 'Female'];
        $dependent = ['' => 'Select Dependent', '1' => 'Yes', '0' => 'No'];
        $relation = ['' => 'Select Relation'] + Relation::lists('r_title', 'r_id')->all();
        return view('family.create', compact('page_title', 'relation', 'id', 'gender', 'dependent'));
    }

    public function create_memis($id)
    {
        //echo "test";die;
        $page_title = 'Add Family';
        $gender = ['' => 'Select Gender', '1' => 'Male', '0' => 'Female'];
        $dependent = ['' => 'Select Dependent', '1' => 'Yes', '0' => 'No'];
        $alive = ['' => 'Select Alive', '1' => 'Yes', '0' => 'No'];
        $relation = ['' => 'Select Relation'] + Relation::lists('r_title', 'r_id')->all();
        return view('family.create_memis', compact('page_title', 'relation', 'alive', 'id', 'gender', 'dependent'));
    }

    public function store_memis(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'relation'  =>     'required',
            ]
        );
        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        //echo "test"; die;
        $record = Family::orderBy('family_id', 'desc')->first();
        $book = new Family();
        $book->family_id = ($record) ? $record->family_id + 1 : 1;
        $book->name = $request->input('name');
        $book->emp_id = $request->input('id');
        $book->date_of_birth = ($request->input('dob')) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('dob')))) : '';
        $book->cnic = $request->input('cnic');
        $book->dependent = $request->input('dependent');
        $book->gender = $request->input('gender');
        $book->relationship = $request->input('relation');
        $book->remarks = $request->input('remarks');
        $book->alive = $request->input('alive');
        $book->family_status = 1;
        $id = $book->family_id;

        if ($request->hasFile('emp_img')) {
            $file = $request->file('emp_img');
            $new_filename = 'user_' . $id;
            $path = 'public/family';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $book->picture = $completeUrl;
        }
        //echo "<pre>"; print_r($book);die;

        $book->save();
        Session::flash('success', 'Family added successfully.');
        return Redirect('memis_list');
    }




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'shared'  =>     'required',
            ]
        );
        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $record = Family::orderBy('family_id', 'desc')->first();
        $book = new Family();
        $book->family_id = ($record) ? $record->family_id + 1 : 1;
        $book->name = $request->input('name');
        $book->emp_id = $request->input('id');
        $book->date_of_birth = ($request->input('dob')) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('dob')))) : '';
        $book->contigency = $request->input('contigency');
        $book->cnic = $request->input('cnic');
        $book->dependent = $request->input('dependent');
        $book->gender = $request->input('gender');
        // print_r( $book->gender ); die;
        $book->age = $request->input('age');
        $book->relationship = $request->input('relation');
        $book->service_particulars = $request->input('service');
        $book->pesion = $request->input('pa');
        $book->cpf = $request->input('cpf');
        $book->shared = $request->input('shared');
        $book->nationality = $request->input('nation');
        $book->nomination_type = $request->input('nomination');
        $book->family_status = 1;
        $book->wp_slip_count = 6;
        $id = $book->family_id;

        if ($request->hasFile('emp_img')) {
            $file = $request->file('emp_img');
            $new_filename = 'user_' . $id;
            $path = 'public/family';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);
            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $book->picture = $completeUrl;
        }
        $book->save();
        Session::flash('success', 'Family added successfully.');
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
        $page_title = 'Family';
        $data = Family::find($id);
        // echo "<pre>"; print_r($data); die;
        return view('family.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Family';
        $data = Family::find($id);
        //echo "<pre>"; print_r($data);die;
        $gender = ['' => 'Select Gender', '1' => 'Male', '0' => 'Female'];
        $dependent = ['' => 'Select Dependent', '1' => 'Yes', '0' => 'No'];
        $relation = ['' => 'Select Relation'] + Relation::lists('r_title', 'r_id')->all();
        return view('family.edit', compact('page_title', 'data', 'relation', 'gender', 'dependent'));
    }
    public function edit_memis($id)
    {
        $page_title = 'Family';
        $data = Family::find($id);
        //echo "<pre>"; print_r($data);die;
        $gender = ['' => 'Select Gender', '1' => 'Male', '0' => 'Female'];
        $dependent = ['' => 'Select Dependent', '1' => 'Yes', '0' => 'No'];
        $relation = ['' => 'Select Relation'] + Relation::lists('r_title', 'r_id')->all();
        return view('family.edit_memis', compact('page_title', 'data', 'relation', 'gender', 'dependent'));
    }


    public function update_memis(Request $request, $id)
    {



        $messages = [
            'relation.required' => 'The relation field is required.',



        ];

        $validation = Validator::make(
            $request->all(),
            [
                'relation'  =>     'required',


            ],
            $messages
        );


        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $user = Family::find($id);
        if ($request->hasFile('emp_img')) {
            $file = $request->file('emp_img');
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

        $departmentArray = array(
            'name' => $request->input('name'),
            'emp_id' => $request->input('id'),
            'date_of_birth' => ($request->input('dob')) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('dob')))) : '',

            'cnic' => $request->input('cnic'),
            'dependent' => $request->input('dependent'),
            'gender' => $request->input('gender'),
            'relationship' => $request->input('relation'),
            'alive' => $request->input('alive'),

            'remarks' => $request->input('remarks'),
            'picture' => $empImg,
        );


        // print_r($departmentArray);die;

        DB::table('TBL_FAMILY')->where('FAMILY_ID', '=', $id)->update($departmentArray);

        Session::flash('success', 'Family updated successfully.');

        return Redirect('employee/emp_detail/' . $request->input('id'));
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
        // echo $id;die;


        $messages = [
            'shared.required' => 'The Shared field is required.',



        ];

        $validation = Validator::make(
            $request->all(),
            [
                'shared'  =>     'required',


            ],
            $messages
        );


        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $user = Family::find($id);
        if ($request->hasFile('emp_img')) {
            $file = $request->file('emp_img');
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

        $departmentArray = array(
            'name' => $request->input('name'),
            'emp_id' => $request->input('id'),
            'date_of_birth' => ($request->input('dob')) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('dob')))) : '',

            'contigency' => $request->input('contigency'),
            'cnic' => $request->input('cnic'),
            'dependent' => $request->input('dependent'),
            'gender' => $request->input('gender'),
            'relationship' => $request->input('relation'),
            'age' => $request->input('age'),
            'service_particulars' => $request->input('service'),
            'nationality' => $request->input('nation'),
            'pesion' => $request->input('pa'),
            'cpf' => $request->input('cpf'),
            'shared' => $request->input('shared'),
            'nomination_type' => $request->input('nomination'),
            'remarks' => $request->input('remarks'),
            'picture' => $empImg,
        );


        //print_r($departmentArray);die;

        DB::table('TBL_FAMILY')->where('FAMILY_ID', '=', $id)->update($departmentArray);

        Session::flash('success', 'Family updated successfully.');

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
        $data = Family::find($id);
        DB::table('TBL_FAMILY')->where('FAMILY_ID', '=', $id)->delete();
        Session::flash('success', 'Family has been deleted successfully.');

        return Redirect('employee/emp_detail/' . $data->emp_id);
    }
}
