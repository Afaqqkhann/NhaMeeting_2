<?php

namespace App\Http\Controllers;

use App\Models\Employees\Employees;
use App\Models\Pension;
use Illuminate\Http\Request;

use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Auth;
class PensionController extends Controller
{
    public function __construct() {

        $this->middleware('auth');
        /*if(!Auth::user()->can('pension'))
            abort(403);*/
    }

    //// Dashboard
    public function pension_dashboard(){

        $page_title = 'Pension Dashboard';

        $user_id = Auth::user()->id;

       $tt_retd_month = DB::select('SELECT PENSION_TT_RETIER_MONTH('.$user_id.') tot_month FROM dual');
       $tt_retd_year = DB::select('SELECT PENSION_TT_RETIER_YEAR('.$user_id.') tot_year FROM dual');
       $tt_active= DB::select('SELECT PENSION_NOC_ACTIVE('.$user_id.') tot_active FROM dual');
       $tt_finalize = DB::select('SELECT PENSION_NOC_FINALIZE('.$user_id.') tot_finalize FROM dual');
       $tt_entered = DB::select('SELECT PENSION_TT_NOC_ENTERED('.$user_id.') tot_entered FROM dual');
       $tt_pending = DB::select('SELECT PENSION_TT_NOC_PENDING('.$user_id.') tot_pending FROM dual');

       $dor = Employees::select('dor')->where('dor', '>=',  date('Y-m-d'))->whereNotNull('dor')->orderBy('dor','asc')->get();

       //echo '<pre>';
       //print_r(($dor));die;
       $ret_year = array();
       foreach($dor as $key => $row){
           $penObject= new \stdClass();
           $ryear = substr($row->dor, 0, 4);
           $penObject->name = $ryear;
           $penObject->y = $key+1;
           $penObject->drilldown = $ryear;
           $ret_year[$key] = response()->json($penObject);
       }
    //echo '<pre>';
      // print_r($ret_year);die;

       /* {
            "name": "2019",
                                "y": 62,
                                "drilldown": "2019"
                            }*/

      // print_r($tt_active[0]->tot_active);

        return view('pension.dashboard', compact('page_title','tt_retd_month','tt_retd_year','tt_active',
           'tt_finalize', 'tt_entered','tt_pending','ret_year'));
    }

    /// Active Pension
    public function activePension(Request $request){
        $empID = $request->input('emp_id');

        DB::table('TBL_EMP')->where('emp_id',$empID)->update(['emp_pension_status'=> 1]);

        return 'Selected Employee pension status activated successfully.';
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $page_title = 'NOC';
        $data = Pension::all();
        //print_r($data);die;

        return view('pension.index', compact('page_title', 'data'));

    }

    ////////
    public function pension_users_info($id){
        $user_id = Auth::user()->id;
        $pension = Pension::select('pension_id','comments','edoc')->where('user_id','=',$user_id)->where('emp_id','=',$id)->first();

        //echo '<pre>';
        //print_r($pension);die;

        return response()->json(['data' => $pension]);

    }

    //// Update Pension

    public function updatePension(Request $request){

        $empID = $request->input('emp_no');
        $comm = $request->input('comments');
        $user_id = Auth::user()->id;
       // echo $comm;
        //echo 'user'.$user_id;die;

        $pension = Pension::where('user_id','=',$user_id)->where('emp_id','=',$empID)->first();



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

        }
        else{
            $pensionEdoc = $pension->edoc;
        }
        ///// timestamp
        date_default_timezone_set("Asia/Karachi");
        $current_date = date('d-m-Y h:i:s a', time());





        $muArray = array(
            'comments' => $request->input('comments'),
            'updated_at' => $current_date,
            'edoc' =>  $pensionEdoc,

        );

        DB::table('TBL_PENSION')->where('pension_id', '=', $pension->pension_id)->update($muArray);

        return 'Pension updated successfully';
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $page_title = 'Add Pension';

        $decisions = ['' => 'Select Decision', 'Pending' => 'Pending', 'Won' => 'Won', 'Lost' => 'Lost'];

        return view('pension.create', compact('page_title', 'case_id','decisions'));

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
        $permission = Permission::find($id);

        return view('permission.show', compact('permission'));
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
