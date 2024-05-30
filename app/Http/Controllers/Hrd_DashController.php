<?php

namespace App\Http\Controllers;
use DB;
use Session;
use Validator;
use Input;

class Hrd_DashController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "Discipline wise Dashboard";
        $date = date("d-m-Y");
        $total_internee = DB::selectOne("SELECT PAID_INTERNEE_TOTAL($date) AS avg_exp FROM DUAL");
        $paid_intrne= DB::selectOne("SELECT PAID_INTERNEE_SL_JOINNED($date) AS sl_join FROM DUAL");
        $short_list= DB::selectOne("SELECT PAID_INTERNEE_SHORT_LISTED($date) AS short_list FROM DUAL");
        $internee_detail = DB::table('TBL_INTERNEE')
            ->select('TBL_INTERNEE.*','TBL_INTERNEE_EDUCATION.degrees','TBL_INTERNEE_EDUCATION.discipline',
                'TBL_INTERNEE_EDUCATION.institute','TBL_INTERNEE_EDUCATION.session_paid','TBL_INTERNEE_EDUCATION.completion_date_paid',
                'TBL_INTERNEE_EDUCATION.total_marks_paid','TBL_INTERNEE_EDUCATION.obtain_marks_paid','TBL_INTERNEE_EDUCATION.grade_paid',
                'TBL_INTERNEE_EDUCATION.cgpa_paid','TBL_INTERNEE_EDUCATION.cnic_edoc')
            ->leftJoin('TBL_INTERNEE_EDUCATION','TBL_INTERNEE.INTERNEE_ID','=','TBL_INTERNEE_EDUCATION.INTERNEE_ID');
 $disp_data = DB::table('V_DISCIPLINE')->get();
         $Yearlable = array();

        foreach($disp_data as $yer) {
            $object1 = $yer->discipline_abb;
            array_push($Yearlable,$object1);
        }
         $labels =  json_encode($Yearlable);


        $disp_data = DB::table('V_DISCIPLINE')->get();

        return view('hrd_dash.dashboard',compact('page_title','internee_detail', 'total_internee', 'paid_intrne', 'short_list', 'labels','disp_data'));
    }

}
