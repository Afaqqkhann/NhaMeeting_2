<?php

namespace App\Services;

use App\Jobs\AddFinMedExpenseJob;
use App\Models\Emp;
use App\Models\Finance\PartyPaymentDetail;
use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Log;

class FinMedExpService
{
    use DispatchesJobs;
    public function __construct()
    {
    }

    public function updateTBLFinMedExp()
    {
        Log::info('Task Schedular - Finance Data Schedule');
        /* $emp = DB::table('tbl_emp')->take(10)->get();
            dd($emp); */
        /* $emp = Emp::take(10)->get();
        dd($emp); */
        $tt = PartyPaymentDetail::take(30)->get();
        dd($tt);
        /* $conn = DB::connection('sqlsrv_fin');
        $finance = $conn->select('EXEC dbo.Sp_EmpPayments ?, ?', array(null, 7104));
        //dd($finance);
        foreach (collect($finance)->chunk(100) as $medExpences) {
            $this->dispatch(new AddFinMedExpenseJob($medExpences));
        } */
    }
}
