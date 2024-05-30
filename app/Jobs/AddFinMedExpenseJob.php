<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Finance\MedicalExpense;
use Exception;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class AddFinMedExpenseJob extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    // public $timeout = 1;
    public $tries = 3;
    public $maxExceptions = 2;
    //public $backoff = 1;
    protected $medExpences;
    public function __construct($medExpences)
    {
        $this->medExpences = $medExpences;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // throw new Exception();
        // sleep(5);
        logger("Queue Job");
        if (isset($this->medExpences) && !empty($this->medExpences)) {
            //foreach (collect($this->medExpences)->chunk(100) as $medFinExp) {
            Log::info('Queue Job - Finance Data Schedule');
            foreach ($this->medExpences as $medExp) {
                //dd($medExp->Month);
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
                //dd($medicalExp);
            }
            //}
        }
    }

    public function failed($e)
    {
        info('job failed' . $e->message());
    }
}
