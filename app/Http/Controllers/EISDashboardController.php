<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EISDashboardController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');

        /*if(!Auth::user()->can('eis_dashboard')) {
    $permissions = Permission::all();
    foreach($permissions as $permission) {
    if(Auth::user()->can($permission->name)) {
    echo $permission->name;die;
    if($permission->link != '#')
    return Redirect::to($permission->link)->send();
    }
    }
    abort(403);
    }*/
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $date = Carbon::now()->format('Y-m-d');
        $userId = Auth::user()->id;

        $data = array(
            'page_title' => 'Executive Information System Dashboard',

            'appoint_current_year' => DB::select('SELECT APPOINT_CURRENT_YEAR() appoint_current_year FROM dual'),
            'total_post' => DB::select('SELECT TOTAL_POST() total_post FROM dual'),
            'total_occupied' => DB::select('SELECT TOTAL_OCCUPIED() total_occupied FROM dual'),
            'total_working' => DB::select('SELECT TOTAL_WORKING() total_working FROM dual'),
            'total_postings' => DB::select('SELECT TOTAL_POSTINGS() total_postings FROM dual'),
            'total_postings_3' => DB::select('SELECT TOTAL_POSTINGS_3() total_postings_3 FROM dual'),
            'total_postings_5' => DB::select('SELECT TOTAL_POSTINGS_5() total_postings_5 FROM dual'),

            /*  'total_acr_remaining' => DB::select('SELECT TOTAL_ACR_REMAINING() total_acr_remaining FROM dual'), */
            'total_assets_remaining' => DB::select('SELECT TOTAL_ASSETS_REMAINING() total_assets_remaining FROM dual'),
            'training_local_current' => DB::select('SELECT TRAINING_LOCAL_CURRENT() training_local_current FROM dual'),
            'training_foreign_current' => DB::select('SELECT TRAINING_FOREIGN_CURRENT() training_foreign_current FROM dual'),

            'total_inquiries' => DB::select('SELECT TOTAL_INQUIRIES() total_inquiries FROM dual'),
            'total_penalties' => DB::select('SELECT TOTAL_PENALTIES() total_penalties FROM dual'),

            'reg_vacant' => DB::select('SELECT REG_VACANT() reg_vacant FROM dual'),
            'reg_work' => DB::select('SELECT REG_WORK() reg_work FROM dual'),
            'pc1_vacant' => DB::select('SELECT PC1_VACANT() pc1_vacant FROM dual'),
            'pc1_work' => DB::select('SELECT PC1_WORK() pc1_work FROM dual'),
            'transport_current_amount' => DB::select('SELECT TRANSPORT_CURRENT_AMOUNT() transport_current_amount FROM dual'),

            'total_medicals' => DB::select('SELECT MEDICAL_CURRENT_AMOUNT() medical_current_amount FROM dual'),
            'tenure_posting' => DB::select('SELECT TENURE_POSTING_DUE() T_P_DUE FROM dual'),
            'tenure_transfer_due' => DB::select('SELECT TENURE_TRANSFER_DUE() T_T_DUE FROM dual'),
        );
        //echo "<pre>"; print_r($data); die;
        return view('eis_dashboard.index')->with($data);
    }

    public function index_memis()
    {
        $page_title = 'MEMIS Dashboard';
        $userID = Auth::user()->id;
        /*** Employees */
        $tot_employees = DB::select('SELECT TOTAL_EMP(2) total_employees FROM dual');
        $tot_emp_working = DB::select('SELECT TOTAL_EMP(1) work_employees FROM dual');
        $tot_emp_rtd = DB::select('SELECT TOTAL_EMP(0) rtd_employees FROM dual');
        $user_employees = DB::select('SELECT TOTAL_EMP(' . $userID . ') user_employees FROM dual');
        $tot_emp_final = DB::select('SELECT EMP_FINALIZED(0) total_finalized FROM dual');
        $user_emp_final = DB::select('SELECT EMP_FINALIZED(' . $userID . ') user_finalized FROM dual');
        $tot_emp_verify = DB::select('SELECT EMP_VERIFIED(0) total_verified FROM dual');
        $user_emp_verify = DB::select('SELECT EMP_VERIFIED(' . $userID . ') user_verified FROM dual');
        $tot_emp_gender = DB::select('SELECT EMP_GENDER(1) total_male FROM dual');
        $user_emp_gender = DB::select('SELECT EMP_GENDER(0) total_female FROM dual');

        /*** Dependents */
        $tot_dependents = DB::select('SELECT TOTAL_DEPENDANT(0) total_dependents FROM dual');
        $user_dependents = DB::select('SELECT TOTAL_DEPENDANT(' . $userID . ') user_dependents FROM dual');
        $tot_dep_final = DB::select('SELECT DEPENDANT_FINALIZED(0) total_finalized FROM dual');
        $user_dep_final = DB::select('SELECT DEPENDANT_FINALIZED(' . $userID . ') user_finalized FROM dual');
        $tot_dep_verify = DB::select('SELECT DEPENDANT_VERIFIED(0) total_verified FROM dual');
        $user_dep_verify = DB::select('SELECT DEPENDANT_VERIFIED(' . $userID . ') user_verified FROM dual');
        $tot_dep_gender = DB::select('SELECT FAMILY_GENDER(1) total_male FROM dual');
        $user_dep_gender = DB::select('SELECT FAMILY_GENDER(0) total_female FROM dual');

        $reg_users = DB::table('V_DB_MEDICAL')
            ->select('USERS.USERNAME', 'USERS.NAME', 'V_DB_MEDICAL.*')
            ->leftJoin('USERS', 'V_DB_MEDICAL.USER_ID', '=', 'USERS.ID')
            ->orderBy('USERS.USERNAME', 'DESC')
            ->get();



        $regional_users = array();
        $regional_dependents = array();
        $regional_employees = array();

        foreach ($reg_users as $key => $val) {

            $regional_users[$key] = $val->name; //$val->username;
            $regional_dependents[$key] = (int) $val->dependents;
            $regional_employees[$key] = (int) $val->emps;
        }
        //echo "<pre>";print_r($regional_employees);die;


        return view(
            'eis_dashboard.memis_dash',
            compact(
                'page_title',
                'tot_emp_rtd',
                'tot_emp_working',
                'regional_employees',
                'regional_dependents',
                'tot_employees',
                'user_employees',
                'tot_emp_final',
                'user_emp_final',
                'tot_emp_verify',
                'user_emp_verify',
                'tot_emp_gender',
                'user_emp_gender',
                'tot_dependents',
                'user_dependents',
                'tot_dep_final',
                'user_dep_final',
                'tot_dep_verify',
                'user_dep_verify',
                'tot_dep_gender',
                'user_dep_gender',
                'regional_users'
            )
        );
    }
    public function memis_list(Request $request)
    {
        //echo "test";die;
        $cnic = trim($request->input('cnic'));

        $emp = DB::table('TBL_EMP')->where('cnic', '=', $cnic)->first();
        if (!empty($emp)) {
            $family = DB::table('TBL_FAMILY')
                ->leftJoin('TBL_FAMILY_VALIDITY', 'TBL_FAMILY.FV_ID', '=', 'TBL_FAMILY_VALIDITY.FV_ID')
                ->where('TBL_FAMILY.EMP_ID', $emp->emp_id)->orderBy('TBL_FAMILY.FAMILY_ID', 'DESC')->get();

            foreach ($family as $key => $row) {
                $sum = $row->cpf;
                $tot_sum[] = $sum;
                $total_cpf = array_sum($tot_sum);
            }

            $total_family = count($family);

            $unfinalized = DB::table('TBL_FAMILY')->where('EMP_ID', '=', $emp->emp_id)
                ->where('REGIONAL_STATUS', '=', 0)->count();

            $hq_unfinalized = DB::table('TBL_FAMILY')->where('EMP_ID', '=', $emp->emp_id)
                ->where('HQ_STATUS', '=', 0)->count();

            return view('eis_dashboard.memis_lis', compact(
                'emp',
                'total_family',
                'unfinalized',
                'hq_unfinalized',
                'family',
                'total_cpf'
            ));
        } else {
            return view('eis_dashboard.memis_not_found');
        }
    }

    public function memis_listing($id)
    {
        // echo  "$id";die;

        $emp1 = DB::table('TBL_EMP')->where('emp_id', '=', $id)->get();
        if (!empty($emp1)) {

            $emp = DB::table('TBL_EMP')->where('emp_id', '=', $id)->first();

            $family = DB::table('TBL_FAMILY')
                ->leftJoin('TBL_FAMILY_VALIDITY', 'TBL_FAMILY.FV_ID', '=', 'TBL_FAMILY_VALIDITY.FV_ID')
                ->where('TBL_FAMILY.EMP_ID', $emp->emp_id)->orderBy('TBL_FAMILY.FAMILY_ID', 'DESC')->get();

            $pa = DB::table('TBL_FAMILY')->where('EMP_ID', $emp->emp_id)->orderBy('FAMILY_ID', 'DESC')->get();
            foreach ($family as $key => $row) {
                $sum = $row->cpf;
                $tot_sum[] = $sum;
                $total_cpf = array_sum($tot_sum);
            }
            foreach ($pa as $key => $row) {
                $sum = $row->pesion;
                $tot_sum[] = $sum;
                $total_pa = array_sum($tot_sum);
            }

            $total_family = DB::table('TBL_FAMILY')->where('EMP_ID', '=', $emp->emp_id)->count();

            $unfinalized = DB::table('TBL_FAMILY')->where('EMP_ID', '=', $emp->emp_id)
                ->where('REGIONAL_STATUS', '=', 0)->count();

            $hq_unfinalized = DB::table('TBL_FAMILY')->where('EMP_ID', '=', $emp->emp_id)
                ->where('HQ_STATUS', '=', 0)->count();

            $per_unfinalized = DB::table('TBL_FAMILY')->where('EMP_ID', '=', $emp->emp_id)
                ->where('HQ_STATUS', '=', 0)->count();

            $family_notnull = DB::table('TBL_FAMILY')->where('EMP_ID', '=', $emp->emp_id)->whereNotNull('picture')->WhereNotNull('relationship')->WhereNotNull('name')->WhereNotNull('date_of_birth')
                ->WhereNotNull('cnic')->WhereNotNull('gender')->WhereNotNull('remarks')->count();
            //echo"<pre>";print_r( $family_notnull);die;
            return view('eis_dashboard.memis_lis', compact(
                'emp',
                'total_family',
                'family_notnull',
                'family',
                'unfinalized',
                'hq_unfinalized',
                'per_unfinalized',
                'total_cpf',
                'total_pa'
            ));
        } else {
            return view('eis_dashboard.memis_not_found');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function final_report($id)
    {
        $family = DB::table('TBL_FAMILY')->where('EMP_ID', '=', $id)->orderBy('date_of_birth', 'DESC')->get();
        // echo  "<pre>"; print_r($family);die;
        $emp = DB::table('TBL_EMP')->where('emp_id', '=', $id)->first();
        //echo"<pre>";print_r( $emp);die;
        return view('eis_dashboard.final_report', compact('emp', 'family'));
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
