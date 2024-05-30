<?php

namespace App\Services;

use App\Models\Carrier;

class CarrierService
{
    protected $carrier;

    /* public function __construct(Carrier $carrier)
    {
        $this->carrier = $carrier;
    } */

    public function getEmpCarrier($empId)
    {
        return Carrier::where('charge_id', '>=', 200)->where('charge_id', '<', 400)->where('emp_id', $empId)->orderBy('joining_date', 'DESC')->orderBy('bs', 'DESC')->get()->lists('post_duration', 'carrier_id');
        //->lists('post_duration', 'sanction_id');
    }
}
