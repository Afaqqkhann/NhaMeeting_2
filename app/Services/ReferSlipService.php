<?php
namespace App\Services;

use App\Models\ReferSlip;
use Auth;
use Validator;

class ReferSlipService
{
    protected $slip, $employee;

    //public function __construct(ReferSlip $slip, Emp $employee)
    public function __construct(ReferSlip $slip)
    {
        $this->slip = $slip;
        // $this->employee = $employee;
    }

    /* public function getEmployeeInfo($id)
    {

    return $employee = $this->slip::find($id);
    } */

    public function getReferSlipData($id)
    {
        return $refer_slip = self::find($id);
    }

    public function getAllReferSlips()
    {
        // with() - Eager Loading multiple relations with specific cols
        $refer_slips = $this->slip::with([
            'employee' => function ($q) {
                $q->select('emp_id', 'emp_name');
            },
            'family.relation',
            'panel' => function ($q) {
                $q->select('panel_id', 'panel_title');
            },
            'panelType' => function ($q) {
                $q->select('pt_id', 'pt_title');
            },
            'region' => function ($q) {
                $q->select('region_id', 'region_name');
            },
            'user' => function ($q) {
                $q->select('id', 'name');
            },
        ])
            ->orderBy('refer_slip_id', 'desc')->get();

        return $refer_slips;
    }

    public function getReferSlip($id)
    {
        $refer_slip = self::find($id);
        return $refer_slip;
    }

    public function createReferSlip()
    {

    }

    public function saveReferSlip($data)
    {
        $validator = Validator::make($data,
            [
                'emp_id' => 'required',
                'dependent_id' => 'required',
            ]);
        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        $record = $this->slip::orderBy('refer_slip_id', 'desc')->first();

        $refer_slip = new $this->slip;
        $refer_slip->refer_slip_id = ($record) ? $recrod->refer_slip_id + 1 : 1;
        $refer_slip->emp_id = $data['emp_id'];
        $refer_slip->dated = date('Y-m-d');
        $refer_slip->dependent_id = $data['dependent_id'];
        $refer_slip->referred_to = $data['referred_to'];
        $refer_slip->panel_id = $data['panel_id'];
        $refer_slip->consultation = $data['consultation'];
        $refer_slip->emergency_treatment = $data['emergency_treatment'];
        $refer_slip->lab_investigation = $data['lab_investigation'];
        $refer_slip->revisit = $data['revisit'];
        $refer_slip->admission = $data['admission'];
        $refer_slip->user_id = auth()->user()->id;
        $refer_slip->region_id = auth()->user()->region_id;
        $refer_slip->save();

        return $refer_slip->fresh();
    }

    /**
     * Update Refer Slip
     * @return Refer Slip
     */
    public function updateReferSlip($data, $id)
    {
        $messages = [
            'emp_name.required' => 'The Employee Name field is required.',
            'dependent_id.required' => 'The Patient field is required.',

        ];
        $validation = Validator::make($data,
            [
                'emp_id' => 'required',
                'dependent_id' => 'required',

            ], $messages);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $refer_slip = $this->slip::find($id);
        $refer_slip->emp_id = $data['emp_id'];
        $refer_slip->dated = date('Y-m-d');
        $refer_slip->dependent_id = $data['dependent_id'];
        $refer_slip->referred_to = $data['referred_to'];
        $refer_slip->panel_id = $data['panel_id'];
        $refer_slip->consultation = $data['consultation'];
        $refer_slip->emergency_treatment = $data['emergency_treatment'];
        $refer_slip->lab_investigation = $data['lab_investigation'];
        $refer_slip->revisit = $data['revisit'];
        $refer_slip->admission = $data['admission'];
        $refer_slip->user_id = auth()->user()->id;
        $refer_slip->region_id = auth()->user()->region_id;
        $refer_slip->save();

        return $refer_slip->fresh();
    }

    /**
     * Delete Refer Slip
     * @return Refer Slip
     */
    public function deleteReferSlip($id)
    {
        $this->slip::destroy($id);
    }
}
