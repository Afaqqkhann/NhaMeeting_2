<?php
namespace App\Services;

use App\Models\Family;

class FamilyService
{
    protected $family;

    public function __construct(Family $family)
    {
        $this->family = $family;
    }
    public function getEmpFamily($emp_id)
    {
        $families = $this->family::select('family_id', 'name')->where('emp_id', $emp_id)->orderBy('name', 'asc')
            ->get();
        return response()->json(['families' => $families]);
    }
}
