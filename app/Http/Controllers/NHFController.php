<?php

namespace App\Http\Controllers;

use App\Models\Emp;
use App\Models\Pension;
use App\Models\NHFVoteCast;
use App\Models\NHFCandidate;
use Illuminate\Http\Request;

use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Auth;
class NHFController extends Controller
{
    public function __construct() {

       // $this->middleware('auth');
        /*if(!Auth::user()->can('pension'))
            abort(403);*/
    }
	
	/** NHF Election Result Page */
	public function getElectionResult(){
		$page_title = "NHF Election 2020 Result";
		
		$cand_zone_votes = [];
		$cand_zone_wise_votes = [];
		$hq_votes = [];
		$pu_votes = [];
		$sn_votes = [];
		$kp_votes = [];
		$bl_votes = [];
			
		$candidates = DB::table('V_VC')->orderBy('TT_REION_VOTES','ASC')->get();
		//echo "<pre>";
		//print_r($candidates);die;
		
		foreach($candidates as $key => $cand){
			
			$hq_votes = DB::select("select VOTE_CANDIDATE_ZONE($cand->candidate_id,2) as hqvotes from dual");
			//print_r($hq_votes);die;
			$pu_votes = DB::select("select VOTE_CANDIDATE_ZONE($cand->candidate_id,4) as puvotes from dual");
			$sn_votes = DB::select("select VOTE_CANDIDATE_ZONE($cand->candidate_id,5) as snvotes from dual");			
			$kp_votes = DB::select("select VOTE_CANDIDATE_ZONE($cand->candidate_id,1) as kpvotes from dual");
			$bl_votes = DB::select("select VOTE_CANDIDATE_ZONE($cand->candidate_id,3) as blvotes from dual");
			$cand_zone_wise_votes[$key] = ["cand_name"=>$cand->candidate_name,"hqvotes" => $hq_votes[0]->hqvotes,
			"puvotes" => $pu_votes[0]->puvotes, "snvotes" => $sn_votes[0]->snvotes,
			"kpvotes" => $kp_votes[0]->kpvotes,"blvotes" => $bl_votes[0]->blvotes,"cand_tot_votes"=>$cand->tt_reion_votes];
		}
		//echo "<pre>";
		//print_r($cand_zone_votes);die;
		
		$resultData = '';
		//$resultData = NHFCandidate::orderBy('candidate_name','ASC')->get();

		return view('nhf_election.result', compact('page_title','cand_zone_wise_votes'));//,'resultData'));		
	}



    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $page_title = 'NHF Election';
        

        return view('nhf_election.index', compact('page_title'));

    }

   
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $page_title = 'E-Polling';
		$voter_stat = Auth::user()->voter_status;
		
		$data = NHFCandidate::orderBy('candidate_id','ASC')->get();	
		
		//echo '<pre>';print_r($data);die;
		
        return view('nhf_election.create', compact('page_title','data','voter_stat'));

    }
	
	//// Save Votes
	 public function saveVotes(Request $request)
    {
      
        $userID = Auth::user()->id;
		$employee = Emp::find($userID);
		
		
		$username = '<span style="color:red;">'.Auth::user()->name.'</span>';
		$ballot_data = $request->all();
		//print_r(json_encode($ballot_data['form_data']));die;
		
		 $box = $request->all();        
		$box_arr=  array();
		parse_str($ballot_data['form_data'], $box_arr);
		//print_r($box_arr['nhfcheckbox']);die;
		
		
		
		if($box_arr['nhfcheckbox']){
			for($j=0; $j<sizeof($box_arr['nhfcheckbox']); $j++) {
				$record = NHFVoteCast::orderBy('cast_id', 'desc')->first();
				
				$votes = new NHFVoteCast();
				$votes->cast_id = ($record) ? $record->cast_id + 1 : 1;
				$votes->voter_id = $userID;
				$votes->vote_cast_status = '1';			// 1 - vote casted
				$votes->CAST_TIMESTAMP = date("d-y-M h:i:s A");  //datetime
				$votes->candidate_id = $box_arr['nhfcheckbox'][$j];
				$votes->region_id = $employee->region_id;
				$votes->section_id = $employee->section_id;
				$votes->zone_id = $employee->zone_id;
				$votes->place_id = $employee->place_id;
				$votes->wing_id = $employee->wing_id;
				$votes->save();
			}
			/// Update User table - voter status (vote casted)
			DB::table('USERS')->where('id', '=',$userID)->update(['voter_status' => '1']);		
			return response()->json(['msg' => 'Congratulations '.$username.'. You have casted your vote successfully.']);
		}
		return response()->json(['msg' => 'Sorry! Try again.']);
        
		
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        /*$validation = Validator::make($request->all(),
            [
                'descision'  => 	'required',
                'descision_edoc'	    =>	'mimes:pdf',

            ]);

        if ($validation->fails())
        {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }*/

        $record = Pension::orderBy('case_proceeding_id', 'desc')->first();

        $pension = new Pension();
        $pension->pension_id = ($record) ? $record->pension_id + 1 : 1;
        $pension->comments = $request->input('comments');
        $pension->pension_status = $request->input('pension_status');
        // upload document
        // upload document
        if($request->hasFile('edoc')) {
            $file = $request->file('edoc');


            /// new file name
            $new_filename = 'Pension_'. $pension->pension_id;

            $path = 'public/NHA-IS/Pension';

            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);

            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $pensionEdoc = $completeUrl;
            //echo $proceedDescEdoc;die;

        }
        else{
            $pensionEdoc = $pension->edoc;
        }

        $pension->edoc = $pensionEdoc;


        $pension->save();


        Session::flash('success', 'Pension updated successfully.');

        return response()->json(['us_option' => $user_options]);

        //return redirect('pension');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        /*$permission = Permission::find($id);

        return view('permission.show', compact('permission'));*/
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
		$data = Permission::where('parent', '=', 1)->get();
		$parent = array('1' => 'Select Parent');
		foreach($data as $row)
			$parent[$row->id] = $row->display_name;
			
        $permission = Permission::find($id);

        return view('permission.edit', compact('permission', 'parent'));
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
        $pension = Permission::find($id);
        $pension->comments = $request->input('comments');
        $pension->pension_status = $request->input('pension_status');

        // upload document
        if($request->hasFile('edoc')) {
            $file = $request->file('edoc');


            /// new file name
            $new_filename = 'Pension_'. $pension->pension_id;

            $path = 'public/NHA-IS/CaseProceed';

            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $new_filename . '.' . $extension);

            $completeUrl = $path . '/' . $new_filename . '.' . $extension;
            $pensionEdoc = $completeUrl;
            //echo $proceedDescEdoc;die;

        }
        else{
            $pensionEdoc = $proceed->descision_edoc;
        }

        $permission->name = $request->input('name');
        $permission->display_name = $request->input('display_name');
        $permission->description = $request->input('description');
		$permission->icon = $request->icon;
		$permission->link = $request->link;
		$permission->parent = isset($request->parent) ? $request->parent : 1;
        $permission->save();

        return Redirect::to('permission');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $permission = Permission::find($id);
        $permission->delete();

        return Redirect::to('permission');
    }
}
