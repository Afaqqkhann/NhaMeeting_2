<?php

namespace App\Http\Controllers;

use DB;
use Datatables;
use App\Models\Advertisement\Advertisement;

use App\Http\Controllers\Controller;


class AdvertisementController extends Controller
{
	public function __construct() {
		
		$this->middleware('auth');
	}
	
    public function advertisement_list() {
		$page_title = 'Advertisement List';
		
		$advertisements = DB::select('SELECT adv.adv_id, adv.adv_dated, adv.adv_status, adv.adv_last_dated, adv.e_doc, adv.advertisement_no, adv_detail.no_of_post FROM tbl_advertisement adv INNER JOIN tbl_advertisement_detail adv_detail ON adv.adv_id = adv_detail.adv_id');
		
		//$test = Advertisement::get();
		//dd($test);
		
		return view('advertisement.list', compact('page_title', 'advertisements'));
		
		//$results = DB::select('select * from tbl_emp where emp_id = ?', array(39));
		
		//dd($results);
		
		//$users = DB::table('v_test2')->get();
		//print_r($users);
        //return view('advertisement.list', ['users' => $users, 'page_title' => $page_title]);
	}
	
	public function create() {
		$page_title = 'Create Advertisement';
		return view('advertisement.create', compact('page_title'));
	}
	
	public function view($id) {
		$page_title = 'View Advertisement';
		
		$advertisement = DB::table('tbl_advertisement adv')
						->join('tbl_advertisement_detail adv_detail', 'adv.adv_id', '=', 'adv_detail.adv_id')
						->select('adv.*', 'adv_detail.no_of_post')
						->where('adv.adv_id', $id)
						->first();
		$posts = DB::table('tbl_advertisement adv')
					->join('tbl_advertisement_detail adv_detail', 'adv.adv_id', '=', 'adv_detail.adv_id')
					->join('tbl_sanction san', 'adv_detail.post_id', '=', 'san.post_id')
					->select('adv.*', 'san.strength_name', 'adv_detail.comments', 'adv_detail.post_id')
					->where('adv.adv_id', $id)
					->get();
		
		return view('advertisement.view', compact('page_title', 'advertisement', 'posts'));
	}
	
	public function edit($id) {
		$page_title = 'Edit Advertisement';
		$advertisement = DB::table('tbl_advertisement adv')
				->join('tbl_advertisement_detail adv_detail', 'adv.adv_id', '=', 'adv_detail.adv_id')
				->where('adv.adv_id', $id)
				->first();
		
		return view('advertisement.edit', compact('page_title', 'advertisement'));
	}
	
	public function view_post($id) {
		$page_title = 'View Post';
		$post = DB::table('tbl_advertisement_detail adv_detail')
				->join('tbl_sanction san', 'adv_detail.post_id', '=', 'san.post_id')
				->where('adv_detail.post_id', $id)
				->first();
				
		return view('advertisement.view_post', compact('page_title', 'post'));
	}
	
	public function edit_post() {
		$page_title = 'Edit Post';
		$post = DB::table('tbl_advertisement_detail adv_detail')
				->join('tbl_sanction san')
				->where('adv_detail.post_id', $id)
				->first();
				
		return view('advertisement.edit_post', compact('page_title', 'post'));
	}

	public function getDatatableData(){
		$adv = Advertisement::select(['ADV_ID', 'ADVERTISEMENT_NO', 'E_DOC', 'ADV_DATED', 'ADV_LAST_DATED']);
		return Datatables::of($adv)->make();
	}

	public function getDatatable() {
		$page_title = 'Advertisements';
		return view('advertisement.advertisement_list', compact('page_title'));
	}
}
