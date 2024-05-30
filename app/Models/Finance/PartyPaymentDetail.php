<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class PartyPaymentDetail extends Model
{
    protected $connection = 'sqlsrv_fin_online';
    protected $table = 'view_parties_payment_details';
    public $timestamps = false;
}
