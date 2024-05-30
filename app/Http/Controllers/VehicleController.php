<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleAuctionPlace;
use App\Models\VPlace;
use DB;
use Illuminate\Http\Request;
use Input;
use Session;
use Validator;
use Carbon\Carbon;
use App\Http\Requests\StoreVehicleRequest;
use Auth;

class VehicleController extends Controller
{

    public function index()
    {
        $page_title = 'Vehicle Management';
        $query = DB::table('TBL_VEHICLE')->orderBy('TBL_VEHICLE.vehid', 'desc')
            ->leftJoin('TBL_REGION', 'TBL_VEHICLE.region_id', '=', 'TBL_REGION.region_id')
            ->leftJoin('TBL_ZONE', 'TBL_VEHICLE.zone_id', '=', 'TBL_ZONE.zone_id')
            ->leftJoin('TBL_PLACE', 'TBL_VEHICLE.place_id', '=', 'TBL_PLACE.place_id')
            ->leftJoin('TBL_PLACE_TYPE', 'TBL_PLACE.place_type_id', '=', 'TBL_PLACE_TYPE.place_type_id')
            ->leftJoin('TBL_VEHICLE_PHYSICAL', 'TBL_VEHICLE.physical_id', '=', 'TBL_VEHICLE_PHYSICAL.physical_id');

        if (!(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('vmis_hq'))) {
            $query = $query->where('TBL_VEHICLE.region_id', auth()->user()->region_id);
        }
        $veh = $query->get();

        $date = Carbon::now()->format('Y-m-d');
        $userId = Auth::user()->id;

        $data = array(
            'veh_tt_auction' => DB::select("SELECT VEH_TT_AUCTION('$date'," . $userId . ") veh_tt_auction FROM dual"),
            'veh_tt_auction_u' => DB::select("SELECT VEH_TT_AUCTION_U('$date'," . $userId . ") veh_tt_auction_u FROM dual"),
            'veh_tt' => DB::select("SELECT VEHICLES_TOTAL('$date'," . $userId . ") veh_tt FROM dual"),
            'veh_tt_u' => DB::select("SELECT VEHICLES_TOTAL_USER('$date'," . $userId . ") veh_tt_u FROM dual"),
            'veh_exp' => DB::select("SELECT VEH_EXP('$date'," . $userId . ") veh_exp FROM dual"),
            'veh_exp_u' => DB::select("SELECT VEH_EXP_USER('$date'," . $userId . ") veh_exp_u FROM dual"),
            'veh_exp_c_m' => DB::select("SELECT VEH_EXP_C_M('$date'," . $userId . ") veh_exp_c_m FROM dual"),
            'veh_exp_c_m_u' => DB::select("SELECT VEH_EXP_C_M_U('$date'," . $userId . ") veh_exp_c_m_u FROM dual"),
            'veh_tt_ws' => DB::select("SELECT VEH_TT_WS('$date'," . $userId . ") veh_tt_ws FROM dual"),
            'weh_tt_ws_u' => DB::select("SELECT VEH_TT_WS_U('$date'," . $userId . ") weh_tt_ws_u FROM dual"),
            'veh_prog' => DB::select("SELECT VEH_PROGRESS('$date'," . $userId . ") veh_prog FROM dual"),
            'veh_prog_u' => DB::select("SELECT VEH_PROGRESS_U('$date'," . $userId . ") veh_prog_u FROM dual"),

        );

        return view('vehicles.index', compact('page_title', 'veh', 'data'));
    }

    public function create()
    {
        $page_title = 'Add Vehicle';
        $v_type = ['' => 'Select Car type', 'Car' => 'Car', 'Van' => 'Van', 'Bus' => 'Bus', 'Coaster' => 'Coaster', 'Pickup' => 'Pickup', 'Jeep' => 'Jeep', 'Sky lifter' => 'Sky lifter'];
        $en_title = ['' => 'Select Entitlement', 'Allocated' => 'Allocated', 'Pool' => 'Pool', 'Pick & drop' => 'Pick & drop'];
        $insure = ['' => 'Select Insurance type', 'Y' => 'Yes', 'N' => 'No'];
        $fuel = ['' => 'Select Fuel type', 'Petrol' => 'Petrol', 'Diesel' => 'Diesel'];
        $t_type = ['' => 'Select Car type', 'Automatic' => 'Automatic', 'Manual' => 'Manual'];

        $trans = ['' => 'Select Transmission type', '' => 'Allocate', 'Not Allocate' => 'Not Allocate'];

        $phys = DB::table('TBL_VEHICLE_PHYSICAL')->get();
        $phy = array(null => 'Select Vehicle Physical Title');

        // $reg = array(null => 'Select Region');

        $query = DB::table('TBL_REGION')->orderBy('region_name', 'ASC');
        if ((auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('vmis_hq')))
            $quer = $query->where('region_id', auth()->user()->region_id);

        $regs = $query->get();
        //dd($regs);

        foreach ($regs as $r) {
            $reg[$r->region_id] = $r->region_name;
        }

        $sections = DB::table('TBL_Section')->orderBy('section_name', 'ASC')->get();
        $sectionsArr = array(null => 'Select Section');

        foreach ($sections as $section) {
            $sectionsArr[$section->section_id] = $section->section_name;
        }

        foreach ($phys as $authorty3) {
            $phy[$authorty3->physical_id] = $authorty3->phy_title;
        }

        $makers = DB::table('TBL_VEHICLE_MAKE_LOOKUP')->get();
        $make = array(null => 'Select Vehicle Makers');

        foreach ($makers as $authorty3) {
            $make[$authorty3->vehmake] = $authorty3->vehmake;
        }

        $types = DB::table('TBL_VEHICLE_TYPE_LOOKUP')->get();
        $type = array(null => 'Select Vehicle Type');

        foreach ($types as $authorty3) {
            $type[$authorty3->vehtype] = $authorty3->vehtype; //. ' ' . $package->contract_code;
        }
        return view('vehicles.create', compact('page_title', 'v_type', 'en_title', 'insure', 'fuel', 'phy', 'make', 'type', 't_type', 'reg', 'sectionsArr'));
    }

    public function getRegionPlaces(Request $request)
    {
        $places = VPlace::where('region_id', $request->input('regionID'))->get();

        return response()->json(['places' => $places]);
    }

    public function store(StoreVehicleRequest $request)
    {

        $place = VPlace::findOrFail($request->input('place_id'));
        $vehi = Vehicle::orderBy('vehid', 'desc')->first();
        $vehicle = new Vehicle();
        $vehicle->vehid = ($vehi) ? $vehi->vehid + 1 : 1;
        $vehicle->vehno = $request->input('vehno');
        $vehicle->make = $request->input('make');
        $vehicle->body_type = $request->input('body_type');
        $vehicle->model = $request->input('model');
        $vehicle->enginenum = $request->input('enginenum');
        $vehicle->chasisnum = $request->input('chasisnum');
        $vehicle->deployedwith = $request->input('deployedwith');
        $vehicle->entitelment = $request->input('entitelment');
        $vehicle->purchasesource = $request->input('purchasesource');
        $vehicle->remarks = $request->input('remarks');
        $vehicle->fuellimit = trim($request->input('fuellimit'));
        $vehicle->purchasedate = ($request->input('purchasedate')) ? date('Y-m-d', strtotime($request->input('purchasedate'))) : '';
        $vehicle->purchase_amt = $request->input('purchase_amt');
        $vehicle->seating_capacity = $request->input('seating_capacity');
        $vehicle->engine_capacity = $request->input('engine_capacity');
        $vehicle->transmission = $request->input('transmission');
        $vehicle->district_registration = $request->input('district_registration');
        $vehicle->manafactured = $request->input('manafactured');
        $vehicle->token_expiry = ($request->input('token_expiry')) ? date('Y-m-d', strtotime($request->input('token_expiry'))) : '';
        $vehicle->insurance = $request->input('insurance');
        $vehicle->fuel_type = $request->input('fuel_type');
        $vehicle->physical_id = $request->input('physical_id');
        $vehicle->region_id = $request->input('regID');
        $vehicle->place_id = $request->input('place_id');
        $vehicle->section_id = $request->input('section_id');
        $vehicle->zone_id = $place->zone_id;
        $vehicle->place_type_id = $place->place_type_id;
        $vehicle->comments = $request->input('comments');
        $vehicle->status = 1;

        if ($request->hasFile('edoc')) {
            $name = $request->vehno . '-edoc';
            $file = $request->file('edoc');
            $path = 'public/assets/img/vehicles';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $name . '.' . $extension);
            $completeUrl = $path . '/' . $name . '.' . $extension;
            $vehicle->edoc = $completeUrl;
        }

        if ($request->hasFile('veh_pica')) {
            $name = $request->vehno . '-A';
            $file = $request->file('veh_pica');
            $path = 'assets/img/vehicles';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move('public/' . $path, $name . '.' . $extension);
            $completeUrl = $path . '/' . $name . '.' . $extension;
            $vehicle->vehphotoa = $completeUrl;
        }

        if ($request->hasFile('veh_picb')) {
            $name = $request->vehno . '-B';
            $file = $request->file('veh_picb');
            $path = 'assets/img/vehicles';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move('public/' . $path, $name . '.' . $extension);
            $completeUrl = $path . '/' . $name . '.' . $extension;
            $vehicle->vehphotob = $completeUrl;
        }

        $vehicle->save();

        // $vehicle->save();
        Session::flash('success', 'Vehicle is created successfully.');
        return redirect('vehicles');
        // }
    }
    public function edit($id)
    {
        $page_title = 'Vehicle Management';
        $vehicle = Vehicle::find($id);
        //dd($vehicle);
        $insure = ['' => 'Select Insurance type', 'Y' => 'Yes', 'N' => 'No'];
        $fuel = ['' => 'Select Fuel type', 'Petrol' => 'Petrol', 'Diesel' => 'Diesel'];
        $phy = ['' => 'Select Physical type', 'Allocate' => 'Allocate', 'Not Allocate' => 'Not Allocate'];
        $t_type = ['' => 'Select Car type', 'Automatic' => 'Automatic', 'Manual' => 'Manual'];
        $en_title = ['' => 'Select Entitlement', 'Allocated' => 'Allocated', 'Pool' => 'Pool', 'Pick & drop' => 'Pick & drop'];


        $phys = DB::table('TBL_VEHICLE_PHYSICAL')->get();
        $phy = array(null => 'Select Vehicle Physical Title');
        foreach ($phys as $authorty3) {
            $phy[$authorty3->physical_id] = $authorty3->phy_title; //. ' ' . $package->contract_code;
        }

        $regs = DB::table('TBL_REGION')->orderBy('region_name', 'ASC')->get();
        $reg = array(null => 'Select Region');
        foreach ($regs as $authorty3) {
            $reg[$authorty3->region_id] = $authorty3->region_name;
        }

        $places = DB::table('V_PLACE')->where('region_id', $vehicle->region_id)->orderBy('place_title', 'ASC')->get();
        $placeArr = array(null => 'Select PLACE');
        foreach ($places as $place) {
            $placeArr[$place->place_id] = $place->place_title;
        }

        $sections = DB::table('TBL_Section')->orderBy('section_name', 'ASC')->get();
        $sectionsArr = array(null => 'Select Section');

        foreach ($sections as $section) {
            $sectionsArr[$section->section_id] = $section->section_name;
        }

        $makers = DB::table('TBL_VEHICLE_MAKE_LOOKUP')->get();
        $make = array(null => 'Select Vehicle Makers');

        foreach ($makers as $authorty3) {
            $make[$authorty3->vehmake] = $authorty3->vehmake;
        }

        $types = DB::table('TBL_VEHICLE_TYPE_LOOKUP')->get();
        $type = array(null => 'Select Vehicle Type');

        foreach ($types as $authorty3) {
            $type[$authorty3->vehtype] = $authorty3->vehtype;
        }


        return view('vehicles.edit', compact('page_title', 'phy', 'placeArr', 'make', 'type', 't_type', 'vehicle', 'en_title', 'insure', 'fuel', 'reg', 'sectionsArr'));
    }
    public function update(StoreVehicleRequest $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $place = VPlace::findOrFail($request->input('place_id'));

        if ($request->hasFile('veh_pica')) {
            $name = $vehicle->vehno . '-A';
            $file = $request->file('veh_pica');
            $path = 'assets/img/vehicles';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move('public/' . $path, $name . '.' . $extension);
            $completeUrl = $path . '/' . $name . '.' . $extension;
            $vehImg = $completeUrl;
        } else {
            $vehImg = $vehicle->vehphotoa;
        }

        if ($request->hasFile('veh_picb')) {
            $name = $vehicle->vehno . '-B';
            $file = $request->file('veh_picb');
            $path = 'assets/img/vehicles';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move('public/' . $path, $name . '.' . $extension);
            $completeUrl = $path . '/' . $name . '.' . $extension;
            $vehiImg = $completeUrl;
        } else {

            $vehiImg = $vehicle->vehphotob;
        }

        if ($request->hasFile('edoc')) {
            $name = $vehicle->vehno . '-edoc';
            $file = $request->file('edoc');
            $path = 'public/assets/img/vehicles';
            $path = str_replace('&', '_', $path);
            $extension = $file->getClientOriginalExtension();
            $file->move($path, $name . '.' . $extension);
            $completeUrl = $path . '/' . $name . '.' . $extension;
            $edoc = $completeUrl;
        } else {

            $edoc = $vehicle->edoc;
        }

        $vehicle->vehno = $request->input('vehno');
        $vehicle->make = $request->input('make');
        $vehicle->body_type = $request->input('body_type');
        $vehicle->model = $request->input('model');
        $vehicle->enginenum = $request->input('enginenum');
        $vehicle->chasisnum = $request->input('chasisnum');
        $vehicle->deployedwith = $request->input('deployedwith');
        $vehicle->entitelment = $request->input('entitelment');
        $vehicle->purchasesource = $request->input('purchasesource');
        $vehicle->seating_capacity = $request->input('seating_capacity');
        $vehicle->engine_capacity = $request->input('engine_capacity');
        $vehicle->manafactured = $request->input('manafactured');
        $vehicle->token_expiry = ($request->input('token_expiry')) ? date('Y-m-d', strtotime($request->input('token_expiry'))) : '';
        $vehicle->insurance = $request->input('insurance');
        $vehicle->fuel_type = trim($request->input('fuel_type'));
        $vehicle->physical_id = $request->input('physical_id');
        $vehicle->transmission = $request->input('transmission');
        $vehicle->district_registration = $request->input('district_registration');
        $vehicle->remarks = $request->input('remarks');
        $vehicle->fuellimit = $request->input('fuellimit');
        $vehicle->region_id = $request->input('regID');
        $vehicle->place_id = $request->input('place_id');
        $vehicle->section_id = $request->input('section_id');
        $vehicle->zone_id = $place->zone_id;
        $vehicle->place_type_id = $place->place_type_id;
        $vehicle->purchase_amt = $request->input('purchase_amt');
        $vehicle->purchasedate = ($request->input('purchasedate')) ? date('Y-m-d', strtotime($request->input('purchasedate'))) : '';
        $vehicle->vehphotoa = $vehImg;
        $vehicle->vehphotob = $vehiImg;
        $vehicle->edoc = $edoc;

        $vehicle->save();
        Session::flash('success', 'Vehicle updated successfully.');
        return Redirect('vehicles');
    }

    public function show($id)
    {
        //$appoint = DB::table('TBL_APPOINTMENT')->where('EMP_ID', $id)->orderBy('appointment_id', 'DESC  ')->get();
        $page_title = 'Show Vehicle Info';
        $vehicle = DB::table('TBL_VEHICLE')->where('vehid', $id)
            ->leftJoin('TBL_REGION', 'TBL_VEHICLE.region_id', '=', 'TBL_REGION.region_id')
            ->leftJoin('TBL_VEHICLE_PHYSICAL', 'TBL_VEHICLE.physical_id', '=', 'TBL_VEHICLE_PHYSICAL.physical_id')
            ->first();

        $row = DB::table('TBL_VEHICLE_ALLOTMENT')->where('vehid', $id)->orderBy('id', $id)
            ->join('TBL_EMP', 'TBL_VEHICLE_ALLOTMENT.emp_id', '=', 'TBL_EMP.EMP_id')
            ->join('TBL_VEHICLE', 'TBL_VEHICLE_ALLOTMENT.vehid', '=', 'TBL_VEHICLE.vehid')
            ->select('TBL_VEHICLE_ALLOTMENT.id as id', 'TBL_VEHICLE_ALLOTMENT.remarks as allot_remarks', 'TBL_EMP.emp_name as employee', 'TBL_VEHICLE_ALLOTMENT.allotmentdate as dated', 'TBL_VEHICLE_ALLOTMENT.status as veh_status', 'TBL_VEHICLE_ALLOTMENT.edoc as e_document', 'TBL_VEHICLE.vehno as vehicle_no')
            ->get();
        //  dd($row);
        $fleet = DB::table('TBL_VEHICLE_FLEET_CARD')->where('vehid', $id)
            ->get();

        $maintenance = DB::table('TBL_VEHICLE_MAINTEMANCE')->where('vehid', $id)
            ->join('TBL_EMP', 'TBL_VEHICLE_MAINTEMANCE.emp_id', '=', 'TBL_EMP.emp_id')
            ->join('TBL_VEHICLE_WORKSHOPS', 'TBL_VEHICLE_MAINTEMANCE.workshop_id', '=', 'TBL_VEHICLE_WORKSHOPS.wsid')
            ->join('TBL_VEHICLE_REPAIR_HEADS', 'TBL_VEHICLE_MAINTEMANCE.head_id', '=', 'TBL_VEHICLE_REPAIR_HEADS.id')
            ->join('TBL_REGION', 'TBL_VEHICLE_MAINTEMANCE.region_id', '=', 'TBL_REGION.region_id')
            ->get();
        $pols = DB::table('TBL_VEHICLE_POL')->where('vehid', $id)->get();

        $auctions = VehicleAuctionPlace::where('veh_id', $id)->get();

        return view('vehicles.show', compact('vehicle', 'auctions', 'row', 'pols', 'fleet', 'maintenance'));
    }

    public function destroy($id)
    {
        $vehicle = DB::table('TBL_VEHICLE')->where('vehid', $id)->delete();
        Session::flash('success', ' Vehicle has been deleted successfully.');
        return redirect('vehicles');
    }
}
