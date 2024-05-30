<?php

namespace App\Http\Controllers;

use DB;
use Datatables;
use Request;
use Redirect;
use Validator;
use App\Models\ACR;
use App\Models\Strength;

use App\Http\Controllers\Controller;
use Auth;

class StrengthController extends Controller
{
	public function __construct() {
		
		$this->middleware('auth');
		if(!Auth::user()->can('strength_dss_dashboard'))
			abort(403);
	}
	
	/**
	 * @return \BladeView|bool|\Illuminate\View\View
     */
	public function get_strength_charts() {
		$page_title = 'Strength Charts';
		$data = DB::table('V_SANCTION')->get();
		$direct = $contract = $direct_vacant = $contract_vacant = 0;
		
		/*foreach($data as $row) {
			$direct += $row->app_direct;
			$direct_vacant += $row->app_direct - $row->work_direct;
			
			$contract += $row->app_promotion_acting;
			$contract_vacant += $row->tt_work - $row->app_promotion_acting;
		}*/
		
		$data = array(
			'page_title'		=>	'Strength Charts',
			
			'reg_vacant'	=> DB::select('SELECT REG_VACANT() reg_vacant FROM dual'),
			'reg_work'	=> DB::select('SELECT REG_WORK() reg_work FROM dual'),
			'pc1_vacant'=> DB::select('SELECT PC1_VACANT() pc1_vacant FROM dual'),
			'pc1_work'=> DB::select('SELECT PC1_WORK() pc1_work FROM dual'),
			
			'trauma_vacant'=>DB::select('SELECT TRAUMA_VANCANT() trauma_vacant FROM dual'),
			'trauma_work'=>DB::select('SELECT TRAUMA_WORK() trauma_work FROM dual'),
			'total_supernumerary'=>DB::select('SELECT TOTAL_SUPERNUMERARY() total_supernumerary FROM dual'),
		);
				
		return view('strength.view_strength')->with($data);
	}

	/**
	 * @return mixed
     */
	public function strength_data(){
		$strength = DB::table('TBL_SANCTION')->select('SANCTION_ID', 'STRENGTH_NAME', 'APP_DIRECT', 'WORK_DIRECT')
			->join('TBL_POST', 'TBL_POST.POST_ID', '=', 'TBL_SANCTION.POST_ID')
			->orderby('SANCTION_ID', 'ASC');
		return Datatables::of($strength)->make();
	}

	public function strength_list() {
		$page_title = 'Strength';
		return view('strength.strength_list', compact('page_title'));
	}

	/**
	 *
     */
	public function add_strength(){
		$page_title = 'Add Strength';
		return view('strength.add_strength', compact('page_title'));
	}

	/**
	 * @return mixed
     */
	public function set_strength() {
		$input = Request::except('_token');

		// custom validation messages
		$messages = array(
			'required' => 'The :attribute field is required.',
		);

		$validator = Validator::make(Request::all(), [
			'strength_name' => 'required|max:20'
		], $messages);

		if($validator->fails())
			return redirect()->back()->withInput()->withErrors($validator->errors());
		/*else {
			$row = DB::table('TBL_SANCTION')->orderBy('SANCTION_ID', 'DESC')->first();

			$data = array(
				'SANCTION_ID' 	=>	 $row->sanction_id+1,
				'STRENGTH_NAME'	=>	$input['strength_name'],
				'APP_DIRECT'	=>	$input['app_direct'],
				'WORK_DIRECT'	=>	$input['work_direct'],
				'WORK_DEPUTATION'	=>	$input['work_deputation'],
				'WORK_PROMOTION_ACTING'	=>	$input['work_promotion_acting'],
				'WORK_REGULAR_ADJ'	=>	$input['work_regular_adj'],
				'COMMENTS'	=>	$input['comments'],
			);
			// now insert new record
			DB::table('TBL_SANCTION')->insert($data);
		}*/

		return Redirect::to('/strength/strength_list');
	}
}
