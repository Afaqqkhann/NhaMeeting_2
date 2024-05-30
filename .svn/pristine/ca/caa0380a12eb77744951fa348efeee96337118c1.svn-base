<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Advertisement\Advertisement;
use Response;

class HomeController extends Controller
{
	public function __construct() {
		
		$this->middleware('auth');
	}
	
    public function index() {
		$page_title = 'Human Resource Management Information System';
		
		$strength = DB::table('V_SANCTION')->get();
		
		return view('home', compact('page_title', 'strength'));
	}

	/**
	 * @return mixed
     */
	public function ajax_get_strength() {

		$strength = DB::table('V_SANCTION')
            ->where('STRENGTH_TYPE','>', 0)
            ->get();
		$designation = $direct_o = $direct_v = $promot_o = $promot_v = array();
		foreach($strength as $row) {
			array_push($designation, $row->strength_name);
			array_push($direct_o, intval($row->work_direct - $row->work_deputation));
			array_push($direct_v, intval(($row->vacant_post < 0 || $row->vacant_post == NULL) ? 0 : $row->vacant_post));
			array_push($promot_o, intval(($row->work_promotion_acting < 0 || $row->work_promotion_acting == NULL) ? 0 : $row->work_promotion_acting));
			array_push($promot_v, intval(($row->app_promotion_acting < 0 || $row->app_promotion_acting == NULL) ? 0 : $row->app_promotion_acting - $row->work_promotion_acting));
		}

		$data = array(
			'designation'=>array_values($designation),
			'direct_o'=>array_values($direct_o),
			'direct_v'=>array_values($direct_v),
			'promot_o'=>array_values($promot_o),
			'promot_v'=>array_values($promot_v)
		);

		return Response::json(array(
			'success' => true,
			'data'   => $data
		));
	}
}
