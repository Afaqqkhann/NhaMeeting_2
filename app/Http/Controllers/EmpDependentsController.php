<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Employees\Employees;
use App\User;
use App\Role;
use App\Permission;

use DB;
use Validator;
use Redirect;
use Datatables;
use URL;
use Session;

use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;



class EmpDependentsController extends Controller {
	
	/**
		* Finalized Dependents Data by Regional User
	**/
	
    public function finalizedDependents($empID){
		/* if(Auth::user()->hasRole('Personnel')){
			$status = 'hq_status';
		}
		else if(Auth::user()->hasRole('memis_list')){
			$status = 'regional_status';
			DB::table('TBL_FAMILY')->where('emp_id','=',$empID)->update([$status => 1]);
		
			return response()->json(['msg' => 'Dependents data has been finalized successfully.'])
		} */		
		$userID = Auth::user()->id;
		DB::table('TBL_FAMILY')->where('emp_id','=',$empID)->update(['regional_status' => 1,'user_id' => $userID]);
	
		return response()->json(['msg' => 'Dependents data has been finalized successfully.']);
		        

    }
	
	/**
		* Verify Dependents Data by DD Personnel Headquarter
	**/
	
    public function verifyDependents($empID){	
		
		
		DB::table('TBL_FAMILY')->where('emp_id','=',$empID)->update(['hq_status' => 1]);
		
		return response()->json(['msg' => 'Dependents data has been verified successfully.']);        

    }
	
	/**
		* Generate Report/Order for Employee's Dependent List
	*/
	public function dependentsListOrder($empID){
		
		$data = DB::table('TBL_FAMILY')
		->leftJoin('TBL_FAMILY_VALIDITY', 'TBL_FAMILY.FV_ID', '=', 'TBL_FAMILY_VALIDITY.FV_ID')
		->where('TBL_FAMILY.emp_id','=',$empID)->orderBy('TBL_FAMILY.date_of_birth','ASC')->get();
		
		$emp = DB::table('TBL_EMP')->where('emp_id','=',$empID)->first();
		
		return view('dependents.dependents_order',compact('data','emp'));
	}
	
	
	
	

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		
	}

	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{

        
		
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        
		
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
       
		
		
		
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		
	}



	
}
