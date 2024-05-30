<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Jobs\AddFinMedExpenseJob;
use App\Models\Finance\AdvAccounts;
use App\Models\Finance\EmpAdvances;
use App\Models\Finance\MedicalExpense;
use App\Models\Finance\PartyPaymentDetail;
use DB;
use Auth;
use Log;
use PhpParser\Node\Expr\Cast\Double;

class DashboardController extends Controller
{
    protected function expend($medExpences)
    {
        // dd($medExpences);
        if (isset($medExpences) && !empty($medExpences)) {
            foreach ($medExpences as $medExp) {
                //DD($medExp);
                $record = MedicalExpense::orderBy('med_exp_id', 'desc')->first();
                $medicalExp = new MedicalExpense();
                $medicalExp->med_exp_id = ($record) ? $record->med_exp_id + 1 : 1;
                $medicalExp->month = (int)$medExp->Month;
                $medicalExp->year = $medExp->Year;
                $medicalExp->title = $medExp->Title;
                $medicalExp->emp_name = $medExp->EmpName;
                $medicalExp->designation = $medExp->Designation;
                $medicalExp->account_no = $medExp->AccountNo;
                $medicalExp->coa = $medExp->COA;
                $medicalExp->amount = (float)$medExp->Amount;
                $medicalExp->allowance = (float)$medExp->Allow;
                $medicalExp->cnic = str_replace(' ', '', $medExp->CNIC);
                $medicalExp->region = $medExp->region;
                $medicalExp->fin_year = str_replace(' ', '', $medExp->finYear);
                $medicalExp->cheque_date = \Naeem\Helpers\Helper::convert_date($medExp->ChequeDate);
                $medicalExp->account_code = $medExp->AccountCode;
                $medicalExp->save();
                dd($medicalExp);
            }
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function medicalFYExpense($emp_id = null)
    {
        ini_set('max_execution_time', 5000);
        ini_set('memory_limit', '5000M');

        /* if (!Auth::user()->can('employee_salary_info')) {
            abort(403);
        } */

        $page_title = "Medical Expense Dashboard";
        $this->dispatch(new AddFinMedExpenseJob('test'));
        die;
        /*  if ($emp_id) {
            // $empID = ($emp_id) ? $emp_id : Auth::user()->emp_id;
            $empID = $emp_id;
            $emp = DB::table('TBL_EMP')->where('EMP_ID', $empID)->first();
        } */
        // Get overall records from Finance
        /* $conn = DB::connection('sqlsrv_fin');
        $finance = $conn->select('EXEC dbo.Sp_EmpPayments ?, ?', array(null, 7104));
        //dd($finance);
        foreach (collect($finance)->chunk(100) as $medExpences) {
            $this->dispatch(new AddFinMedExpenseJob($medExpences));
        } */

        $collection = MedicalExpense::all()->groupBy('account_code'); //->get();
        //dd($collection);
        //$collection = collect($finance)->groupBy('AccountCode');
        /* $collection = $collection->groupBy('account_code');
        dd($collection); */
        $headsTotal = $collection->map(function ($head, $key) {
            $hosLabObj = new \stdclass();
            $hosLabObj->id = $key;
            $hosLabObj->name = $head[0]->coa;
            $hosLabObj->y = $head->sum('amount');
            $hosLabObj->drilldown = true;
            //$hosLabObj->drilldown = $head[0]->COA;
            return $medExpArr[] = $hosLabObj;
        });
        $medHeads = array_values($headsTotal->all());
        $accountCodes = AdvAccounts::get();
        /* echo '<pre>';
        print_r($accountCodes);
        die; */
        // Employee Advances Detail
        /* $emp_advances = EmpAdvances::where('cnic', $emp->cnic)->orderBy('refmonth', 'desc')->orderBy('accountcode', 'asc')->get(); */

        /* echo '<pre>';
        print_r($emp_advances);
        die; */


        //$headsArr = ['Hospital & Labs', 'Medicines', 'Medical Allowance', 'Reimbursement'];

        //echo '<pre>';print_r($medHeads);die;
        ///Get Medical Expenses Financial Year Wise
        $medExpFY = MedicalExpense::orderBy('fin_year', 'ASC')->get()
            ->groupBy('fin_year')
            ->map(function ($head, $key) {
                //$medExpFY = $medExpFY->map(function ($head, $key) {
                $hosLabObj = new \stdclass();
                $hosLabObj->id = $key;
                $hosLabObj->name = $head[0]->fin_year;
                $hosLabObj->y = $head->sum('amount');
                $hosLabObj->drilldown = true;
                //$hosLabObj->drilldown = $head[0]->COA;
                return $medExpYearArr[] = $hosLabObj;
            });
        $medExpFY = array_values($medExpFY->all());

        //dd($medHeads);

        return view('dashboard.medical_fy_expense', compact('page_title', 'medHeads',  'emp_advances', 'finance', 'medExpFY')); //'emp',
    }


    public function medFyExpenseDetail($accountCode, $finYear)
    {

        $page_title = 'Medical Expense Detail';
        $paymentsDetail = PartyPaymentDetail::where('accountcode', $accountCode)->take(30)->get();
        //where('fin_year',$finYear)->where('accountcode', 'like', '7104%')
        echo '<pre>';
        print_r($paymentsDetail);
        die;

        // return response()->json(['paymentsDetail' => $paymentsDetail]);
        return view('dashboard.medical_expense_detail', compact('page_title', 'paymentsDetail'));
    }

    /// Employee Year Wise Medical Expense
    public function medExpenseHead($group, $col, $val)
    {
        $collection = MedicalExpense::where($col, $val)->orderBy($group, 'ASC')->get()->groupBy($group);
        $medExpHead = $collection->map(function ($head, $key) use ($group) {
            $hosLabObj = new \stdclass();
            $hosLabObj->id = $key;
            $hosLabObj->name = ($group === 'account_code') ? $head[0]->coa : $head[0]->$group;
            $hosLabObj->y = $head->sum('amount');
            //$hosLabObj->drilldown = true;
            //$hosLabObj->drilldown = $head[0]->COA;
            return $medExpYearArr[] = $hosLabObj;
        });
        $medExpHead = array_values($medExpHead->all());

        return response()->json(['data' => $medExpHead]);

        /*if(strlen($code)==4 && $code!=7104){
			echo 'jjj';die;
		}*/
        //echo 'code -  '.$code;die;
        /*  $conn = DB::connection('sqlsrv_fin');
        $medExp = $conn->select('EXEC dbo.Sp_EmpPayments ?, ?', array($cnic = null, $code));
        //dd($medExp);
        //$medExpColl = collect($medExp)->groupBy('Year');
        $medExpColl = collect($medExp)->filter(function ($item) use ($code) {
            return ($item->AccountCode === $code) ? $item : '';
        })->groupBy('finYear'); //->groupBy('Year');
        //dd($medDrill);
        $medExpYear = $medExpColl->map(function ($head, $key) {

            $hosLabObj = new \stdclass();
            $hosLabObj->id = $key;
            $hosLabObj->name = trim($head[0]->finYear);
            $hosLabObj->y = $head->sum('Amount');
            //$hosLabObj->drilldown = true;
            //$hosLabObj->drilldown = $head[0]->COA;
            return $medExpYearArr[] = $hosLabObj;
        });
        $medExpYear = array_values($medExpYear->all());
        //dd($medExpYear);
        return response()->json(['data' => $medExpYear]); */
    }
}
