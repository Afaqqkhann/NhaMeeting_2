<?php

namespace App\Console\Commands;

use App\Jobs\AddFinMedExpenseJob;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use DB;
use Log;

class UpdateFinMedExp extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:finMedExp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update/sync data of Medical Expences as retrieved from Finance Server.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $arrayOfConnections = config('database.connections'); // Array of connections
        foreach ($arrayOfConnections as $connection) {
            // Setting Config
            \Config::set('database.connections.dynamicConnection.host', $connection->host);
            \Config::set('database.connections.dynamicConnection.password', $connection->connection);
            // More of your config

            // Your Logic Here
            info("test Db Config" . $connection->host);

            // Purge the configuration (It is important!)
            DB::purge('dynamicConnection');
        }

        Log::info('Task Schedular - Finance Data Schedule');
        /* $emp = DB::table('tbl_emp')->take(10)->get();
            dd($emp); */
        $conn = DB::connection('sqlsrv_fin');
        $finance = $conn->select('EXEC dbo.Sp_EmpPayments ?, ?', array(null, 7104));
        //dd($finance);
        foreach (collect($finance)->chunk(100) as $medExpences) {
            $this->dispatch(new AddFinMedExpenseJob($medExpences));
        }
    }
}
