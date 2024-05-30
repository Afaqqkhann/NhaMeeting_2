<?php

namespace App\Http\Controllers;

use App\Models\Intrnee;
use App\Models\Discipline;

use App\Models\Intrnee_edu;
use App\Models\Order;
use App\Models\Tranings;
use App\Models\Region;
use App\Models\VPlace;
use App\Models\District;

use Illuminate\Http\Request;
use Naeem\Helpers\Helper;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\UnPaidInterneeRequest;
use DB;
use Session;
use Validator;
use Datatables;
use Input;

class Interneee_eduController extends Controller
{
    public function index2()
    {
        return view('internee_deu.index2', compact('page_title', 'internee_detail', 'total_internee', 'paid_intrne', 'short_list'));
    }
    public function getInternees(Request $request)
    {
        $query =  DB::table('TBL_INTERNEE')
            ->select(
                [
                    'TBL_INTERNEE.internee_id', 'TBL_INTERNEE.name', 'TBL_INTERNEE.fname',
                    'TBL_INTERNEE.cnic_no', 'TBL_INTERNEE_EDUCATION.discipline'
                ]
            )
            /* ->select('TBL_INTERNEE.*',        
        'TBL_INTERNEE_EDUCATION.degrees','TBL_INTERNEE_EDUCATION.discipline',
        'TBL_INTERNEE_EDUCATION.institute','TBL_INTERNEE_EDUCATION.session_paid',
        'TBL_INTERNEE_EDUCATION.completion_date_paid',
        'TBL_INTERNEE_EDUCATION.total_marks_paid','TBL_INTERNEE_EDUCATION.obtain_marks_paid',
        'TBL_INTERNEE_EDUCATION.grade_paid','TBL_INTERNEE_EDUCATION.cgpa_paid',
        'TBL_INTERNEE_EDUCATION.cnic_edoc',        
        'TBL_PLACE.PLACE_TITLE','TBL_INTERNEE_REASONS.title as reason') */
            ->leftJoin('TBL_INTERNEE_EDUCATION', 'TBL_INTERNEE.INTERNEE_ID', '=', 'TBL_INTERNEE_EDUCATION.INTERNEE_ID')
            ->leftJoin('TBL_PLACE', 'TBL_INTERNEE.PLACE_ID', '=', 'TBL_PLACE.PLACE_ID')
            ->leftJoin('TBL_INTERNEE_REASONS', 'TBL_INTERNEE.REASON_ID', '=', 'TBL_INTERNEE_REASONS.REASON_ID')
            ->where('TBL_INTERNEE.INTERNEE_ID', '>', 24608)
            ->where('TBL_INTERNEE.program_type', '=', 'Paid');
        return Datatables::of($query)
            ->addColumn('action', function ($query) {
                return '<a href="internee_edu/' . $query->internee_id . '/edit" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
            })
            ->make(true);
    }

    public function getInterneesCustom(Request $request)
    {

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = Intrnee::select('count(*) as allcount')->count();
        $totalRecordswithFilter = Intrnee::select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();

        // Fetch records
        $records = Intrnee::orderBy('internee_id', $columnSortOrder)
            ->where('tbl_internee.name', 'like', '%' . $searchValue . '%')
            ->select('tbl_internee.*')
            ->skip($start)
            ->take($rowperpage)
            ->get();
        //dd($records);
        // echo 'kk';die;

        $data_arr = array();
        $sno = $start + 1;
        foreach ($records as $record) {
            $id = $record->internee_id;
            $fname = $record->fname;
            $name = $record->name;
            $cnic = $record->cnic_no;

            $data_arr[] = array(
                "id" => $id,
                "fname" => $fname,
                "name" => $name,
                "cnic" => $cnic
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Paid Internees';
        $date = date("d-m-Y");
        $total_internee = DB::selectOne("SELECT PAID_INTERNEE_TOTAL($date) AS avg_exp FROM DUAL");
        $paid_intrne = DB::selectOne("SELECT PAID_INTERNEE_SL_JOINNED($date) AS sl_join FROM DUAL");
        $short_list = DB::selectOne("SELECT PAID_INTERNEE_SHORT_LISTED($date) AS short_list FROM DUAL");

        /* $query =  DB::table('TBL_INTERNEE')
            ->select(['TBL_INTERNEE.internee_id','TBL_INTERNEE.name','TBL_INTERNEE.fname',
            'TBL_INTERNEE.cnic_no','TBL_INTERNEE_EDUCATION.discipline']
            ) */
        $internee_detail = DB::table('TBL_INTERNEE')
            ->select(
                'TBL_INTERNEE.*',
                'TBL_INTERNEE_EDUCATION.degrees',
                'TBL_INTERNEE_EDUCATION.discipline',
                'TBL_INTERNEE_EDUCATION.institute',
                'TBL_INTERNEE_EDUCATION.session_paid',
                'TBL_INTERNEE_EDUCATION.completion_date_paid',
                'TBL_INTERNEE_EDUCATION.total_marks_paid',
                'TBL_INTERNEE_EDUCATION.obtain_marks_paid',
                'TBL_INTERNEE_EDUCATION.grade_paid',
                'TBL_INTERNEE_EDUCATION.cgpa_paid',
                'TBL_PLACE.PLACE_TITLE',
                'TBL_INTERNEE_EDUCATION.cnic_edoc',
                'TBL_INTERNEE_REASONS.title as reason'
            )
            ->leftJoin('TBL_INTERNEE_EDUCATION', 'TBL_INTERNEE.INTERNEE_ID', '=', 'TBL_INTERNEE_EDUCATION.INTERNEE_ID')
            ->leftJoin('TBL_PLACE', 'TBL_INTERNEE.PLACE_ID', '=', 'TBL_PLACE.PLACE_ID')
            ->leftJoin('TBL_INTERNEE_REASONS', 'TBL_INTERNEE.REASON_ID', '=', 'TBL_INTERNEE_REASONS.REASON_ID')
            ->orderBy('TBL_INTERNEE.internee_id', 'asc')->where('TBL_INTERNEE.INTERNEE_ID', '>', 13000)
            ->where('TBL_INTERNEE.program_type', '=', 'Paid');    //->paginate(5000)  
        if (auth()->user()->username == 'adhrd') {
            $internee_detail = $internee_detail->where('TBL_INTERNEE_EDUCATION.discipline', '=', 'Lab Technology');
        } else if (auth()->user()->username == 'dirhrd') {
            $internee_detail = $internee_detail->where('TBL_INTERNEE_EDUCATION.discipline', '=', 'DAE (Civil / Quantity Survey)');
        } else
            $internee_detail = $internee_detail->take(4000)->get();

        return view('internee_deu.index', compact('page_title', 'internee_detail', 'total_internee', 'paid_intrne', 'short_list'));
    }

    public function index_unpaid()
    {

        $page_title = 'UnPaid Internees';
        //$date = 0 ;
        $unpaid_tt = DB::select('SELECT UNPAID_TT() unpaid_tt FROM dual');

        $unpaid_joined = DB::selectOne("SELECT UNPAID_JOINED() AS unpaid_joined FROM DUAL");

        $unpaid_selected = DB::selectOne("SELECT UNPAID_SELECTED() AS unpaid_selected FROM DUAL");
        //  echo "<pre>"; print_r($unpaid_joined);die;



        $internee_detail = DB::table('TBL_INTERNEE')
            ->select(
                'TBL_INTERNEE.*',
                'TBL_INTERNEE_EDUCATION.degrees',
                'TBL_INTERNEE_EDUCATION.discipline',
                'TBL_INTERNEE_EDUCATION.institute',
                'TBL_INTERNEE_EDUCATION.session_paid',
                'TBL_PLACE.PLACE_TITLE',
                'TBL_INTERNEE_EDUCATION.cnic_edoc',
                'TBL_INTERNEE_REASONS.title as reason'
            )
            ->leftJoin('TBL_INTERNEE_EDUCATION', 'TBL_INTERNEE.INTERNEE_ID', '=', 'TBL_INTERNEE_EDUCATION.INTERNEE_ID')
            ->leftJoin('TBL_PLACE', 'TBL_INTERNEE.PLACE_ID', '=', 'TBL_PLACE.PLACE_ID')
            ->leftJoin('TBL_INTERNEE_REASONS', 'TBL_INTERNEE.REASON_ID', '=', 'TBL_INTERNEE_REASONS.REASON_ID')
            ->orderBy('TBL_INTERNEE.internee_id', 'desc')->where('TBL_INTERNEE.INTERNEE_ID', '>=', 21846)
            ->where('TBL_INTERNEE.program_type', '=', 'Unpaid');    //->paginate(5000)  
        if (auth()->user()->username == 'adhrd') {
            $internee_detail = $internee_detail->where('TBL_INTERNEE_EDUCATION.discipline', '=', 'Lab Technology')
                ->get();
        }
        /*else if(auth()->user()->username == 'dirhrd'){
            $internee_detail = $internee_detail->where('TBL_INTERNEE_EDUCATION.discipline','=','DAE (Civil / Quantity Survey)')
                                    ->get();
        } */ else
            $internee_detail = $internee_detail->get();
        // echo"<pre>"; print_r($internee_detail); die;

        return view('internee_deu.index_unpaid', compact('page_title', 'internee_detail', 'unpaid_tt', 'unpaid_joined', 'unpaid_selected'));
    }

    public function updateAppReceiving($internee_id)
    {
        //echo $internee_id;die;
        //header('Access-Control-Allow-Origin: *');  
        //header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

        $internee = Intrnee::find($internee_id);
        $internee->application_recive = 1;
        $internee->joining_status = 1;
        $internee->save();
        return response()->json(['data' => 'updated']);
    }

    public function updateAppShortList($internee_id)
    {
        //echo $internee_id;die;
        //header('Access-Control-Allow-Origin: *');  
        //header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

        $internee = Intrnee::find($internee_id);
        $internee->internee_status = 1;
        $internee->application_recive = 1;
        $internee->save();
        return response()->json(['data' => 'updated']);
    }
    /** Irrelevant Degree Reson update */
    public function updateIrrelevantDegree(Request $request)
    {
        //print_r($request);die;
        //header('Access-Control-Allow-Origin: *');  
        //header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

        $internee = Intrnee::find($request->internee);
        $internee->reason_id = 6;
        $internee->save();
        return response()->json(['data' => 'Irrelevant Degree updated']);
    }



    /** Create Form **/
    public function create()
    {
        $grade = ['' => 'Select Grade', 'A+' => 'A+', 'A' => 'A',  'B' => 'B',  'C' => 'C', 'D' => 'D'];
        $semester = ['' => 'Select Semester', '1' => 'Ist', '2' => '2nd', '3' => '3rd', '4' => '4th', '5' => '5th', '6' => '6th', '7' => '7th', '8' => '8th'];
        $year = ['' => 'Select Year', '1' => 'Ist', '2' => '2nd', '3' => '3rd', '4' => '4th'];
        $gender = ['' => 'Select Gender', 'Male' => 'Male', 'Female' => 'Female'];
        $proposed_months = [
            '' => 'Select Month', 'Jul-2022' => 'Jul-2022', 'Aug-2022' => 'Aug-2022', 'Sep-2022' => 'Sep-2022',
            'Oct-2022' => 'Oct-2022', 'Nov-2022' => 'Nov-2022', 'Dec-2022' => 'Dec-2022',
            'Jan-2023' => 'Jan-2023', 'Feb-2023' => 'Feb-2023', 'Mar-2023' => 'Mar-2023', 'Apr-2023' => 'Apr-2023',
            'May-2023' => 'May-2023', 'Jun-2023' => 'Jun-2023'
        ];
        $discipline = ['' => 'Select Discipline'] + Discipline::lists('discipline_title', 'discipline_id')->all();
        $placeArr = ['' => 'Select Place'] + VPlace::orderBy('region_name', 'asc')
            ->lists('place_title', 'place_id')->all();
        $places = DB::table('V_PLACE')->where('place_type_id', '<>', 4)->orderBy('place_title', 'ASC')->get();
        $placeArr = array('' => 'Select Place');
        foreach ($places as $place) {
            $placeArr[$place->place_id] = $place->place_title . ' (' . $place->place_type . ')';
        }

        $dists = DB::table('TBL_DISTRICT')->get();
        $districts = array('' => 'Select District');
        foreach ($dists as $dist) {
            $districts[$dist->district_name] = $dist->district_name;
        }
        $districts['others'] = 'Others';

        return view('internee_deu.create', compact('semester', 'proposed_months', 'districts', 'year', 'placeArr', 'discipline', 'gender', 'grade'));
    }
    ////
    public function index1($id)
    {

        $data = DB::table('tbl_internee')->join('tbl_internee_education', 'tbl_internee.internee_id', '=', 'tbl_internee_education.internee_id')
            ->leftJoin('v_place', 'tbl_internee.place_id', '=', 'v_place.place_id')
            ->where('tbl_internee.internee_id', '=', $id)
            ->select(
                'tbl_internee.internee_id as internee_id',
                'tbl_internee.name as name',
                'tbl_internee.news_paper_name as news_paper_name',
                'tbl_internee.pubication_date as pubiction_date',
                'tbl_internee.fname as fname',
                'tbl_internee.cnic_no as cnic_no',
                'tbl_internee.gender as gender',
                'tbl_internee.domicle as domicle',
                'tbl_internee.email as email',
                'tbl_internee.mobile_no as mobile',
                'tbl_internee.telephone_no as phone_no',
                'tbl_internee.present_address as present_address',
                'tbl_internee.eductional_recom_letter_edoc',
                'tbl_internee.cnic_front',
                'tbl_internee.cnic_back',
                'tbl_internee.final_transac',
                'tbl_internee.present_district as present_district',
                'tbl_internee.present_tehsil as present_tehsil',
                'tbl_internee.domicle as domicle_dist',
                'tbl_internee.permanent_address as permanent_address',
                'tbl_internee.permanent_district as permanent_district',
                'tbl_internee.permanent_tehsil as permanent_tehsil',
                'tbl_internee.proposed_month as proposed_month',
                'tbl_internee_education.internee_edu_id as internee_edu_id',
                'tbl_internee_education.discipline as discipline',
                'tbl_internee_education.degrees as degrees',
                'tbl_internee_education.institute as institute',
                'tbl_internee_education.session_paid as session_paid',
                'tbl_internee_education.completion_date_paid as completion_date_paid',
                'tbl_internee_education.enrollment_no as enrollment_no',
                'tbl_internee_education.addmission_date as addmission_date',
                'tbl_internee_education.current_semester as current_semester',
                'tbl_internee_education.yeared as yeared',
                'tbl_internee_education.cnic_edoc as cnic_edoc',
                'tbl_internee_education.domicle_edoc as domicle_edoc',
                'tbl_internee_education.transcript_edoc as ts_edoc',
                'v_place.place_type',
                'v_place.place_title'
            )
            ->first();
        // echo "<pre>"; print_r($data);
        return view('internee_deu.detail', compact('data'));
    }
    public function store(UnPaidInterneeRequest $request)
    {

        try {
            return DB::transaction(function () use ($request) {
                $ord = Intrnee::orderBy('internee_id', 'desc')->first();
                $intrenee = new Intrnee();
                $int_id = $intrenee->internee_id = ($ord) ? $ord->internee_id + 1 : 1;
                $intrenee->program_type = 'Unpaid';
                $intrenee->name = $request->input('name');
                $intrenee->news_paper_name = $request->input('name_newspaper');
                $intrenee->pubication_date = ($request->input('date_publication')) ? date('Y-m-d', strtotime($request->input('date_publication'))) : null;
                if (substr_count($request->input('cnic_no'), '-') <= 0) {
                    $intrenee->cnic_no = substr($request->input('cnic_no'), 0, 5) . '-' .
                        substr($request->input('cnic_no'), 5, 7) . '-' .
                        substr($request->input('cnic_no'), 12);
                } else {
                    $intrenee->cnic_no = $request->input('cnic_no');
                }

                $intrenee->domicle = $request->input('domicile_dist');
                $intrenee->fname = $request->input('father_name');
                $intrenee->gender = $request->input('gender');
                $intrenee->email = $request->input('email');
                $intrenee->mobile_no = $request->input('mobile');
                $intrenee->telephone_no = $request->input('phone');
                $intrenee->present_address = $request->input('pre_address');
                $intrenee->present_tehsil = $request->input('pre_tehsil');
                $intrenee->proposed_month = $request->input('proposed_month');
                /*** District */
                $intrenee->present_district = ($request->input('pre_district') != 'Others') ?
                    $request->input('pre_district') : $request->input('other_pre_district');
                $intrenee->permanent_address = $request->input('prm_address');
                $intrenee->permanent_tehsil = $request->input('prm_tehsil');
                $intrenee->permanent_district = ($request->input('prm_district') != 'Others') ?
                    $request->input('prm_district') : $request->input('other_prm_district');

                $intrenee->place_id = $request->input('place_id');
                /** Document Attachement */
                $intrenee->cnic_front = ($request->hasFile('cnic_front')) ?
                    Helper::uploadDocument($request->file('cnic_front'), 'public/NHA-IS/HRD/UnpaidInternship', 'cnic_front_' . $intrenee->internee_id) : '';
                $intrenee->cnic_back = ($request->hasFile('cnic_back')) ?
                    Helper::uploadDocument($request->file('cnic_back'), 'public/NHA-IS/HRD/UnpaidInternship', 'cnic_back_' . $intrenee->internee_id) : '';
                $intrenee->eductional_recom_letter_edoc = ($request->hasFile('eductional_recom_letter_edoc')) ?
                    Helper::uploadDocument($request->file('eductional_recom_letter_edoc'), 'public/NHA-IS/HRD/UnpaidInternship', 'education_doc_' . $intrenee->internee_id) : '';


                $intrenee->save();


                $int_edu = Intrnee_edu::orderBy('internee_edu_id', 'desc')->first();
                $intrenee_edu = new Intrnee_edu();
                $intrenee_edu->internee_edu_id = ($int_edu) ? $int_edu->internee_edu_id + 1 : 1;
                $intrenee_edu->internee_id = $int_id;
                $intrenee_edu->degrees = $request->input('degree');

                $intrenee_edu->displine_id = $request->input('discipline');
                if ($request->input('discipline') == 1)
                    $DIS = 'Auto CAD';
                elseif ($request->input('discipline') == 2)
                    $DIS = 'DAE (Civil / Quantity Survey)';
                elseif ($request->input('discipline') == 3)
                    $DIS = 'Environmental Sciences';
                elseif ($request->input('discipline') == 4)
                    $DIS = 'Geo-Technical / Material Science';
                elseif ($request->input('discipline') == 5)
                    $DIS = 'Lab Technology';
                elseif ($request->input('discipline') == 6)
                    $DIS = 'Civil Engineering';
                $intrenee_edu->discipline = $DIS;

                $intrenee_edu->institute = $request->input('uni_name');
                $intrenee_edu->session_paid = $request->input('session');
                // $intrenee_edu->completion_date_paid = ($request->input('date_comp')) ? date('Y-m-d', strtotime($request->input('date_comp'))) : null;
                // $intrenee_edu->total_marks_paid = $request->input('total_marks' );
                //$intrenee_edu->obtain_marks_paid = $request->input('obtained_marks');
                // $intrenee_edu->grade_paid = $request->input('grade');
                // $intrenee_edu->cgpa_paid = $request->input('cgpa');

                //echo "<pre>"; print_r($intrenee_edu); die;
                $intrenee_edu->save();
                Session::flash('success', 'You have submitted application form successfully.');
                return redirect('nha_unpaid_detail/' . $int_id);
            });
        } catch (\Exception $e) {
            echo 'Something went wrong. Please try again.';
            //\Illuminate\Database\QueryException $e
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cnic_check($id, $prog_type)
    {
        $data = DB::table('tbl_internee')->where('cnic_no', '=', $id)
            ->where('program_type', '=', $prog_type)->first();
        return json_encode($data);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $semester = ['' => 'Select Semester', '1' => 'Ist', '2' => '2nd', '3' => '3rd', '4' => '4th', '5' => '5th', '6' => '6th', '7' => '7th', '8' => '8th'];
        $year = ['' => 'Select Year', '1' => 'Ist', '2' => '2nd', '3' => '3rd', '4' => '4th'];

        $internee_status = ['' => 'Select Internee Status', '0' => 'Long Listed', '1' => 'Short Listed'];
        $joining_status = ['' => 'Select Joining Status', '0' => 'Not Joined', '1' => 'Joined'];

        $reasons = array(null => 'Select Reason');
        $rsn = DB::table('TBL_INTERNEE_REASONS')->orderBy('title', 'ASC')->get();

        foreach ($rsn as $row) {
            $reasons[$row->reason_id] = $row->title;
        }

        $places = DB::table('V_PLACE')->where('place_type_id', '<>', 4)->orderBy('place_title', 'ASC')->get();
        $placeArr = array('' => 'Select Place');
        foreach ($places as $place) {
            $placeArr[$place->place_id] = $place->place_title . ' (' . $place->place_type . ')';
        }

        $data = DB::table('TBL_INTERNEE')
            ->select(
                'TBL_INTERNEE.*',
                'TBL_INTERNEE_EDUCATION.degrees',
                'TBL_INTERNEE_EDUCATION.discipline',
                'TBL_INTERNEE_EDUCATION.institute',
                'TBL_INTERNEE_EDUCATION.session_paid',
                'TBL_INTERNEE_EDUCATION.completion_date_paid',
                'TBL_INTERNEE_EDUCATION.total_marks_paid',
                'TBL_INTERNEE_EDUCATION.obtain_marks_paid',
                'TBL_INTERNEE_EDUCATION.grade_paid',
                'TBL_INTERNEE_EDUCATION.cgpa_paid',
                'TBL_INTERNEE_EDUCATION.cnic_edoc',
                'TBL_INTERNEE_EDUCATION.domicle_edoc',
                'TBL_INTERNEE_EDUCATION.transcript_edoc'
            )
            ->leftJoin('TBL_INTERNEE_EDUCATION', 'TBL_INTERNEE.INTERNEE_ID', '=', 'TBL_INTERNEE_EDUCATION.INTERNEE_ID')
            ->where('TBL_INTERNEE.internee_id', '=', $id)->first();
        /* $cnic_no = (substr_count($data->cnic_no,'-') <= 0)?
                        $this->cnic_format($data->cnic_no) : $data->cnic_no; */
        $cnic_no = $data->cnic_no;

        return view('internee_deu.edit', compact('semester', 'cnic_no', 'placeArr', 'reasons', 'year', 'data', 'internee_status', 'joining_status'));
    }
    public function edit_unpaid($id)
    {

        $internee_status = ['' => 'Select Internee Status', '0' => 'Long Listed', '1' => 'Short Listed'];
        $joining_status = ['' => 'Select Joining Status', '0' => 'Not Joined', '1' => 'Joined'];

        $reasons = array(null => 'Select Reason');
        $rsn = DB::table('TBL_INTERNEE_REASONS')->orderBy('title', 'ASC')->get();

        foreach ($rsn as $row) {
            $reasons[$row->reason_id] = $row->title;
        }

        $places = DB::table('V_PLACE')->where('place_type_id', '<>', 4)->orderBy('place_title', 'ASC')->get();
        $placeArr = array('' => 'Select Place');
        foreach ($places as $place) {
            $placeArr[$place->place_id] = $place->place_title . ' (' . $place->place_type . ')';
        }

        $proposed_months = [
            '' => 'Select Month', 'Jul-2022' => 'Jul-2022', 'Aug-2022' => 'Aug-2022', 'Sep-2022' => 'Sep-2022',
            'Oct-2022' => 'Oct-2022', 'Nov-2022' => 'Nov-2022', 'Dec-2022' => 'Dec-2022',
            'Jan-2023' => 'Jan-2023', 'Feb-2023' => 'Feb-2023', 'Mar-2023' => 'Mar-2023', 'Apr-2023' => 'Apr-2023',
            'May-2023' => 'May-2023', 'Jun-2023' => 'Jun-2023'
        ];


        $data = DB::table('TBL_INTERNEE')
            ->select(
                'TBL_INTERNEE.*',
                'TBL_INTERNEE_EDUCATION.degrees',
                'TBL_INTERNEE_EDUCATION.discipline',
                'TBL_INTERNEE_EDUCATION.institute',
                'TBL_INTERNEE_EDUCATION.session_paid',
                'TBL_INTERNEE_EDUCATION.cnic_edoc',
                'TBL_INTERNEE_EDUCATION.domicle_edoc'
            )
            ->leftJoin('TBL_INTERNEE_EDUCATION', 'TBL_INTERNEE.INTERNEE_ID', '=', 'TBL_INTERNEE_EDUCATION.INTERNEE_ID')
            ->where('TBL_INTERNEE.internee_id', '=', $id)->first();

        $cnic_no = $data->cnic_no;

        return view('internee_deu.edit_unpaid', compact('cnic_no', 'proposed_months', 'placeArr', 'reasons', 'data', 'internee_status', 'joining_status'));
    }

    public function update_unpaid(Request $request, $id)
    {
        $messages = array(
            'cnic_no' => 'The CNIC field is required.',
            'name' => 'The Name field is required.',
        );
        //
        $validator = Validator::make($request->all(), [

            'cnic_no' => 'required',
            'name' => 'required',

        ], $messages);

        if ($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());

        try {
            return DB::transaction(function () use ($request, $id) {
                $intrenee = Intrnee::findOrFail($id);

                $intrenee->name = $request->input('name');
                $intrenee->news_paper_name = $request->input('name_newspaper');
                $intrenee->pubication_date = ($request->input('date_publication')) ? date('Y-m-d', strtotime($request->input('date_publication'))) : null;

                $intrenee->domicle = $request->input('domicile_dist');
                $intrenee->fname = $request->input('father_name');
                $intrenee->gender = $request->input('gender');
                $intrenee->email = $request->input('email');
                $intrenee->mobile_no = $request->input('mobile');
                $intrenee->telephone_no = $request->input('phone');
                $intrenee->present_address = $request->input('pre_address');
                $intrenee->present_tehsil = $request->input('pre_tehsil');
                $intrenee->proposed_month = $request->input('proposed_month');
                /*** District */
                $intrenee->present_district = ($request->input('pre_district') != 'Others') ?
                    $request->input('pre_district') : $request->input('other_pre_district');
                $intrenee->permanent_address = $request->input('prm_address');
                $intrenee->permanent_tehsil = $request->input('prm_tehsil');
                $intrenee->permanent_district = ($request->input('prm_district') != 'Others') ?
                    $request->input('prm_district') : $request->input('other_prm_district');
                $intrenee->internee_status = $request->input('internee_status');
                $intrenee->joining_status = $request->input('joining_status');
                $intrenee->reason_id = $request->input('reason_id');
                $intrenee->place_id = $request->input('place_id');
                /** Document Attachement */
                $intrenee->cnic_front = ($request->hasFile('cnic_front')) ?
                    Helper::uploadDocument($request->file('cnic_front'), 'public/NHA-IS/HRD/UnpaidInternship', 'cnic_front_' . $intrenee->internee_id) : $intrenee->cnic_front;
                $intrenee->cnic_back = ($request->hasFile('cnic_back')) ?
                    Helper::uploadDocument($request->file('cnic_back'), 'public/NHA-IS/HRD/UnpaidInternship', 'cnic_back_' . $intrenee->internee_id) : $intrenee->cnic_back;
                $intrenee->eductional_recom_letter_edoc = ($request->hasFile('eductional_recom_letter_edoc')) ?
                    Helper::uploadDocument($request->file('eductional_recom_letter_edoc'), 'public/NHA-IS/HRD/UnpaidInternship', 'education_doc_' . $intrenee->internee_id) : $intrenee->eductional_recom_letter_edoc;

                $intrenee->proposed_office_work = $request->input('proposed_office_work');
                $intrenee->accounts = $request->input('accounts');
                $intrenee->proposed_office_address = $request->input('proposed_office_address');

                $intrenee->save();

                $intrenee_edu = Intrnee_edu::where('internee_id', '=', $id)->first();
                $intrenee_edu->degrees = $request->input('degree');
                $intrenee_edu->discipline = $request->input('discipline');
                $intrenee_edu->institute = $request->input('uni_name');
                $intrenee_edu->session_paid = $request->input('session');

                $intrenee_edu->save();
                Session::flash('success', 'Record has been updated successfully.');
                return redirect('unpaid_edu/list');
            });
        } catch (\Exception $e) {
            echo "Sorry! Record couldn't update successfully. Please try again.";
            //\Illuminate\Database\QueryException $e
        }
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
        $messages = array(
            'cnic_no' => 'The CNIC field is required.',
            'name' => 'The Name field is required.',
        );
        //
        $validator = Validator::make($request->all(), [

            'cnic_no' => 'required',
            'name' => 'required',

        ], $messages);

        if ($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());


        $intrenee = Intrnee::find($id);
        $intrenee->name = $request->input('name');
        $intrenee->news_paper_name = $request->input('name_newspaper');
        $intrenee->pubication_date = ($request->input('date_publication')) ? date('Y-m-d', strtotime($request->input('date_publication'))) : null;
        $intrenee->cnic_no = $request->input('cnic_no');
        $intrenee->domicle = $request->input('domicile_dist');
        $intrenee->fname = $request->input('father_name');
        $intrenee->gender = $request->input('gender');
        $intrenee->email = $request->input('email');
        $intrenee->mobile_no = $request->input('mobile');
        $intrenee->telephone_no = $request->input('phone');
        $intrenee->present_address = $request->input('pre_address');
        $intrenee->present_tehsil = $request->input('pre_tehsil');
        $intrenee->present_district = $request->input('pre_district');
        $intrenee->permanent_address = $request->input('prm_address');
        $intrenee->permanent_tehsil = $request->input('prm_tehsil');
        $intrenee->permanent_district = $request->input('prm_district');
        $intrenee->internee_status = $request->input('internee_status');
        $intrenee->joining_status = $request->input('joining_status');
        $intrenee->reason_id = $request->input('reason_id');
        /** Document Attachement */
        $intrenee->cnic_front = ($request->hasFile('cnic_front')) ?
            Helper::uploadDocument($request->file('cnic_front'), 'public/NHA-IS/HRD/paidInternship', 'cnic_front_' . $intrenee->internee_id) : $intrenee->cnic_front;
        $intrenee->cnic_back = ($request->hasFile('cnic_back')) ?
            Helper::uploadDocument($request->file('cnic_back'), 'public/NHA-IS/HRD/paidInternship', 'cnic_back_' . $intrenee->internee_id) : $intrenee->cnic_back;
        $intrenee->final_transac = ($request->hasFile('final_transac')) ?
            Helper::uploadDocument($request->file('final_transac'), 'public/NHA-IS/HRD/paidInternship', 'transcript_' . $intrenee->internee_id) : $intrenee->final_transac;

        $intrenee->proposed_office_work = $request->input('proposed_office_work');
        $intrenee->accounts = $request->input('accounts');
        $intrenee->proposed_office_address = $request->input('proposed_office_address');
        //  echo "<pre>"; print_r($intrenee); die;
        // echo 'dd'.$request->input('name');die;
        $intrenee->save();

        $intrenee_edu = Intrnee_edu::where('internee_id', '=', $id)->first();
        if ($intrenee_edu != null) {
            $intrenee_edu->degrees = $request->input('degree');
            $intrenee_edu->discipline = $request->input('discipline');
            $intrenee_edu->institute = $request->input('uni_name');
            $intrenee_edu->session_paid = $request->input('session');
            $intrenee_edu->completion_date_paid = ($request->input('date_comp')) ? date('Y-m-d', strtotime($request->input('date_comp'))) : null;
            $intrenee_edu->total_marks_paid = $request->input('total_marks');
            $intrenee_edu->obtain_marks_paid = $request->input('obtained_marks');
            $intrenee_edu->grade_paid = $request->input('grade');
            $intrenee_edu->cgpa_paid = $request->input('cgpa');

            /*if(is_string(request()->get('cnic'))){
				$intrenee_edu->cnic_edoc = 1;
			}
			else {
				$intrenee_edu->cnic_edoc = 0;
			}
			if(is_string(request()->get('domicile'))){
				$intrenee_edu->domicle_edoc = 1;
			}
			else {
				$intrenee_edu->domicle_edoc = 0;
			}
			if(is_string(request()->get('transcript'))){
				$intrenee_edu->transcript_edoc = 1;
			}
			else {
				$intrenee_edu->transcript_edoc = 0;
			} */
            // echo "<pre>"; print_r($intrenee_edu); die;
            $intrenee_edu->save();
        }
        Session::flash('success', 'Data updated successfully.');
        // echo "<script>window.close();</script>";
        return redirect('internee_edu/list');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        DB::table('TBL_INTERNEE_EDUCATION')->where('internee_id', '=', $id)->delete();
        DB::table('TBL_INTERNEE')->where('internee_id', '=', $id)->delete();

        Session::flash('success', ' Internee has been deleted successfully.');

        return redirect('internee_edu/list');
    }
    public function destroy_unpaid($id)
    {

        DB::table('TBL_INTERNEE_EDUCATION')->where('internee_id', '=', $id)->delete();
        DB::table('TBL_INTERNEE')->where('internee_id', '=', $id)->delete();

        Session::flash('success', ' Internee has been deleted successfully.');

        return redirect('unpaid_edu/list');
    }

    public function cnic_format($cnic)
    {
        return substr($cnic, 0, 5) . '-' .
            substr($cnic, 5, 7) . '-' .
            substr($cnic, -1);
    }
}
