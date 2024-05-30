<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use Auth;
use App\User;
use App\Permission;
use Redirect;
class E_votedashboardController extends Controller
{
	public function __construct() {
		
		$this->middleware('auth');
        
	}
	
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()

    {
        $t_cand = DB::select('SELECT TOTAL_CANDIDATES()  t_cand FROM dual');
        $t_rem_vote = DB::select('SELECT TOTAL_REMAINING_VOTES() t_rem_vote FROM dual');
        $t_voter = DB::select('SELECT TOTAL_VOTERS() t_voter FROM dual');
        $t_vote_cast = DB::select('SELECT TOTAL_VOTE_CASTS() t_vote_cast FROM dual');
        //echo "<pre>"; print_r($t_cand);die;
		return view('eis_dashboard.e_vote', compact('t_cand','t_rem_vote','t_voter','t_vote_cast'));
    }
	
	public function getLiveVotes(){
		$live_votes =  DB::select('SELECT TOTAL_VOTE_CASTS() t_vote_cast FROM dual');
		return response()->json(['live_votes' => $live_votes ]);
	}
	
    public function index1()

    {
        $data = array(
            't_cand' => DB::select('SELECT TOTAL_CANDIDATES()  t_cand FROM dual'),
            't_rem_vote' => DB::select('SELECT TOTAL_REMAINING_VOTES() t_rem_vote FROM dual'),
            't_voter' => DB::select('SELECT TOTAL_VOTERS() t_voter FROM dual'),
            't_vote_cast' => DB::select('SELECT TOTAL_VOTE_CASTS() t_vote_cast FROM dual'),
        );

        return response()->json([$data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
