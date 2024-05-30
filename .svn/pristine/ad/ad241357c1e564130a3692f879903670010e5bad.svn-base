<?php

namespace App\Http\Controllers;

use App\Models\CommunityMsg;
use App\Models\MessageType;
use App\Models\Relation;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use URL;
use Datatables;
use Redirect;
use Validator;
use Session;
use Response;
use Input;

use App\Models\Employees\Employees;
use File;
class CommunityMsgController extends Controller
{

    public function __construct()
    {

        $this->middleware('auth');
        if (!Auth::user()->can('manage_community_msg'))
            abort(403);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Community Message';
        $data = DB::table('V_COMMUNITY_MSG')->where('MESSAGE_STATUS', '=', 1)->get();

        return view('community_msg.index', compact('data', 'page_title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'New Community Message';
        $message_type = MessageType::lists('title', 'msg_type_id');
        $relation = Relation::lists('r_title', 'r_id');

        return view('community_msg.create', compact('page_title', 'message_type', 'relation'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // custom validation messages
        $messages = array(
            'required' => 'The :attribute field is required.',
        );

        $validator = Validator::make($request->all(), [
            'message_type_id' => 'required',
            'relation_id' => 'required',
            'message' => 'required',
        ], $messages);

        if ($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
        else {

            if ($request->input('community_id') == 0) {
                $row = DB::table('TBL_COMMUNITY_MSG')->orderBy('COMMUNITY_ID', 'DESC')->first();

                $data = [
                    'COMMUNITY_ID' => ($row) ? $row->community_id + 1 : 1,
                    'MESSAGE_TYPE_ID' => $request->input('message_type_id'),
                    'RELATION_ID' => $request->input('relation_id'),
                    'EMP_ID' => Auth::user()->emp_id,
                    'ENTRY_DATE' => date('Y-m-d'),
                    'MESSAGE' => $request->input('message'),
                    'COORDINATES' => $request->input('lat') . ',' . $request->input('lng')
                ];
                // now insert new record and get last id
                $community_id = DB::table('TBL_COMMUNITY_MSG')->insertGetId($data, 'COMMUNITY_ID');
            } else {
                $data = [
                    'MESSAGE_TYPE_ID' => $request->input('message_type_id'),
                    'RELATION_ID' => $request->input('relation_id'),
                    'EMP_ID' => Auth::user()->emp_id,
                    'ENTRY_DATE' => date('Y-m-d'),
                    'MESSAGE' => $request->input('message'),
                    'MESSAGE_STATUS' => 1,
                    'COORDINATES' => $request->input('lat') . ',' . $request->input('lng')
                ];
                // now update record and get last id
                DB::table('TBL_COMMUNITY_MSG')->where('COMMUNITY_ID', '=', $request->input('community_id'))->update($data);
            }
        }

        Session::flash('success', 'Community Message has been created successfully.');
        return Redirect::to('/community_msg');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = DB::table('TBL_COMMUNITY_MSG CM')
            ->join('TBL_MESSAGE_TYPE MT', 'MT.MSG_TYPE_ID', '=', 'CM.MESSAGE_TYPE_ID')
            ->join('TBL_RELATION R', 'R.R_ID', '=', 'CM.RELATION_ID')
            ->where('CM.COMMUNITY_ID', '=', $id)
            ->first();

        return view('community_msg.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Edit Community Message';
        $message_type = MessageType::lists('title', 'msg_type_id');
        $relation = Relation::lists('r_title', 'r_id');

        $pictures = DB::table('TBL_COMMUNITY_PICTURE')->where('COMMUNITY_ID', '=', $id)->get();

        $data = DB::table('TBL_COMMUNITY_MSG CM')
            ->join('TBL_MESSAGE_TYPE MT', 'MT.MSG_TYPE_ID', '=', 'CM.MESSAGE_TYPE_ID')
            ->join('TBL_RELATION R', 'R.R_ID', '=', 'CM.RELATION_ID')
            ->where('CM.COMMUNITY_ID', '=', $id)
            ->first();

        return view('community_msg.edit', compact('page_title', 'message_type', 'relation', 'data', 'pictures'));
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
        // custom validation messages
        $messages = array(
            'required' => 'The :attribute field is required.',
        );

        $validator = Validator::make($request->all(), [
            'message_type_id' => 'required',
            'relation_id' => 'required',
            'message' => 'required',
        ], $messages);

        if ($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());
        else {

            $data = [
                'MESSAGE_TYPE_ID' => $request->input('message_type_id'),
                'RELATION_ID' => $request->input('relation_id'),
                'EMP_ID' => Auth::user()->emp_id,
                'ENTRY_DATE' => date('Y-m-d'),
                'MESSAGE' => $request->input('message'),
                'COORDINATES' => $request->input('lat') . ',' . $request->input('lng')
            ];

            DB::table('TBL_COMMUNITY_MSG')->where('COMMUNITY_ID', '=', $id)->update($data);
        }

        Session::flash('success', 'Community Message has been updated successfully.');
        return Redirect::to('/community_msg');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('TBL_COMMUNITY_PICTURE')->where('COMMUNITY_ID', '=', $id)->delete();
        DB::table('TBL_COMMUNITY_MSG')->where('COMMUNITY_ID', '=', $id)->delete();

        Session::flash('success', 'Community Message has been deleted successfully.');

        return Redirect::to('community_msg');
    }

    public function upload_image()
    {

        $input = Input::except('community_id', '_token');
        $community_id = Input::get('community_id');

        $rules = array(
            'file' => 'image|mimes:jpg,jpeg,png'
        );

        $validation = Validator::make($input, $rules);

        if ($validation->fails()) {
            return Response::json($validation->errors->first(), 400);
        }

        $file = Input::file('file');

        $extension = $file->getClientOriginalExtension();
        $directory = 'public/NHA-IS/community_msg';
        $filename = sha1(time()) . ".{$extension}";

        if ($file->move($directory, $filename)) {

            if ($community_id == 0) {
                $community = DB::table('TBL_COMMUNITY_MSG')->orderBy('COMMUNITY_ID', 'DESC')->first();
                $data = array(
                    'COMMUNITY_ID' => ($community) ? $community->community_id + 1 : 1,
                    'EMP_ID' => Auth::user()->emp_id,
                    'MESSAGE_STATUS' => 0
                );
                $community_id = DB::table('TBL_COMMUNITY_MSG')->insertGetId($data, 'COMMUNITY_ID');
            }

            // now insert community picture
            $cp_data = DB::table('TBL_COMMUNITY_PICTURE')->orderBy('PIC_ID', 'DESC')->first();

            $data = array(
                'PIC_ID' => ($cp_data) ? $cp_data->pic_id + 1 : 1,
                'COMMUNITY_ID' => $community_id,
                'COMMUNITY_EDOC' => $directory . '/' . $filename,
            );
            // insert record
            DB::table('TBL_COMMUNITY_PICTURE')->insert($data);

            return response()->json(['success' => $community_id]);

        } else {
            return response()->json(['success' => false]);
        }
    }

    public function delete_image($id)
    {
        $cp_data = DB::table('TBL_COMMUNITY_PICTURE')->where('PIC_ID', '=', $id)->first();

        if ($cp_data) {
            if (file_exists($cp_data->community_edoc))
                File::delete($cp_data->community_edoc);

            DB::delete('delete from TBL_COMMUNITY_PICTURE where PIC_ID = ?', [$id]);
            return Response::json('success', 200);
        } else
            return Response::json(false);

    }

    public function ajax_update()
    {
        $result = CommunityMsg::where('MESSAGE_STATUS', '=', 1)->get();
        $total = 0;
        $data = array();

        foreach ($result as $key => $row) {
            $data[$key]['community_id'] = $row->community_id;
            $data[$key]['message_title'] = $row->message_type->title;
            //$data[$key]['message_picture'] = $row->message_pictures;
            $data[$key]['message'] = str_limit($row->message, 60, '...');

            $total++;
        }

        return response()->json(array('success' => true, 'result' => $data, 'total_messages'=>$total));
    }
}