<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use DB;
use Illuminate\Http\Request;
use Input;
use Session;
use Validator;

class RewardController extends Controller
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
        $page_title = 'Add Reward';

        return view('reward.create', compact('page_title', 'id'));
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
//                'e_from'  =>     'required',
                //                'e_to'  =>     'required',
            ]);
        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }
        $record = Reward::orderBy('reward_id', 'desc')->first();
        $book = new Reward();
        $book->reward_id = ($record) ? $record->reward_id + 1 : 1;
        $book->emp_id = $request->input('id');
        $book->letter_date = ($request->input('l_date')) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('l_date')))) : '';
        $book->comments = $request->input('comments');
        $book->kind_of_reward = $request->input('k_reward');
        $book->purpose = $request->input('p_reward');
        $book->app_authority = $request->input('approv');
        $book->reward_status = 1;
        $book->save();
        Session::flash('success', 'Reward added successfully.');
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
        $page_title = 'Reward';
        $data = Reward::find($id);
        //echo "<pre>"; print_r($data); die;
        return view('reward.show', compact('page_title', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Reward';
        $data = Reward::find($id);
//        echo "<pre>"; print_r($data); die;
        return view('reward.edit', compact('page_title', 'data'));
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

//            'e_from.required' => 'The Extension From field is required.',
            //            'e_to.required' => 'The Extension To field is required.',
        ];
        $validation = Validator::make($request->all(),
            [
//                'e_from'  =>     'required',
                //                'e_to'  =>     'required',
            ], $messages);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $letter_date = ($request->input('l_date')) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('l_date')))) : '';
        $departmentArray = array(

            'emp_id' => $request->input('id'),
            'kind_of_reward' => $request->input('k_reward'),
            'purpose' => $request->input('p_reward'),
            'letter_date' => $letter_date,
            'app_authority' => $request->input('approval'),
            'comments' => $request->input('comments'),
            'reward_status' => 1,
        );
        DB::table('TBL_REWARD')->where('REWARD_ID', '=', $id)->update($departmentArray);
        Session::flash('success', 'Reward updated successfully.');
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
        $data = Reward::find($id);
        DB::table('TBL_REWARD')->where('REWARD_ID', '=', $id)->delete();
        Session::flash('success', 'Reward has been deleted successfully.');
        return Redirect('employee/emp_detail/' . $data->emp_id);
    }
}
