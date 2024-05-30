<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReferSlipTreatmentRequest;
use App\Models\Emp;
use App\Models\Family;
use App\Models\MedEntitlement;
use App\Models\Panel;
use App\Models\ReferTreatment;
use App\Models\Treatment;
use App\Models\ReferSlip;
use App\Models\Region;
use Auth;
use Illuminate\Http\Request;
use Session;
use Validator;

class ReferTreatmentController extends Controller
{
    //protected $slip_service;

    /*public function __construct(ReferSlipService $slip_service)
    {
    $this->slip_service = $slip_service;
    }*/
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Refer Slips';
        $referslips = ReferSlip::with([
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
        //echo '<pre>';
        //dd($referslips);
        //$referslips = $this->slip_service->getAllReferSlips();

        return view('referslip.index', compact('page_title', 'referslips', 'employees', 'dependents', 'panels', 'regions'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($referSlip)
    {
        $page_title = 'Add Refer Slip Treatment';

        $results = ['1' => 'Negative', '2' => 'Positive'];
        $treatments = ['' => 'Select Treatment'];
        $treatments_arr = Treatment::select('id', 'disease')->orderBy('disease', 'asc')->get();
        
        foreach ($treatments_arr as $key => $treat) {
            $treatments[$treat->id] = $treat->disease;
        }
        
        return view('referslip_treatment.create', compact('page_title', 'results', 'treatments', 'referSlip'));
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReferSlipTreatmentRequest $request)
    {       

        $record = ReferTreatment::orderBy('rt_id', 'desc')->first();      

        $refer_treatment = new ReferTreatment();
        $refer_treatment->rt_id = ($record) ? $record->rt_id + 1 : 1;
        $refer_treatment->result = request('result');
        $refer_treatment->remarks = request('remarks');
        $refer_treatment->treatment_id = request('treatment_id');
        $refer_treatment->refer_id = request('refer_id');
        $refer_treatment->emp_id = request('emp_id');
        $refer_treatment->family_id = request('family_id');
        $refer_treatment->region_id = auth()->user()->region_id;
       
        if ($request->hasFile('edoc_status')) {
            $name = 'treatment_'.$refer_treatment->refer_id.'_'.$refer_treatment->rt_id;
            $file = $request->file('edoc_status');
            $path = 'public/referslip/treatments';
           // $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $name . '.' . $extension);
            $completeUrl = $path . '/' . $name . '.' . $extension;
            $refer_treatment->edoc_status = $completeUrl;
        } 
               
        $refer_treatment->save(); 

        Session::flash('success', 'Refer Slip Treatment has been created successfully.');
        return redirect('referslip/' . $refer_treatment->refer_id);

    }

    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $page_title = "Refer Slip";

        $referSlip = ReferSlip::findOrFail($id);

        return view('referslip.show', compact('referSlip'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Edit Refer Slip Treatment';

        $referTreatment = ReferTreatment::findOrFail($id);

        $referSlip = ReferSlip::where('refer_slip_id',$referTreatment->refer_id)->first();
        
        $results = ['1' => 'Negative', '2' => 'Positive'];
        $treatments = ['' => 'Select Treatment'];
        $treatments_arr = Treatment::select('id', 'disease')->orderBy('disease', 'asc')->get();
        
        foreach ($treatments_arr as $key => $treat) {
            $treatments[$treat->id] = $treat->disease;
        }
        
        return view('referslip_treatment.edit', compact('page_title', 'results', 'referSlip', 'treatments', 'referTreatment'));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ReferSlipTreatmentRequest $request, $id)
    {
        
        $refer_treatment = ReferTreatment::findOrFail($id);
        $refer_treatment->result = request('result');
        $refer_treatment->remarks = request('remarks');
        $refer_treatment->treatment_id = request('treatment_id');
        $refer_treatment->refer_id = request('refer_id');
        $refer_treatment->emp_id = request('emp_id');
        $refer_treatment->family_id = request('family_id');
        $refer_treatment->region_id = auth()->user()->region_id;
       
        if ($request->hasFile('edoc_status')) {
            $name = 'treatment_'.$refer_treatment->refer_id.'_'.$refer_treatment->rt_id;
            $file = $request->file('edoc_status');
            $path = 'public/referslip/treatments';
           // $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $name . '.' . $extension);
            $completeUrl = $path . '/' . $name . '.' . $extension;
            $edoc = $completeUrl;
        } else{
            $edoc = $refer_treatment->edoc_status;
        }

        $refer_treatment->edoc_status = $edoc;
               
        $refer_treatment->save(); 

        Session::flash('success', 'Refer Slip Treatment has been updated successfully.');
        return redirect('referslip/' . $refer_treatment->refer_id);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // use force delete to avoid soft delete
        $refer_treatment = ReferTreatment::findOrFail($id);
        ReferTreatment::where('rt_id', $id)->forceDelete();
        //$this->slip_service->deleteReferSlip($id);
        Session::flash('success', 'Refer Slip Treatment has been deleted successfully.');

        return redirect('referslip/' . $refer_treatment->refer_id);
    }
}
