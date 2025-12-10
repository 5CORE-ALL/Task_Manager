<?php

namespace Workdo\Hrm\Http\Controllers\Api;

use App\Models\Setting;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Hrm\Entities\Employee;
use Workdo\Hrm\Entities\Branch;
use Workdo\Hrm\Entities\Department;
use Workdo\Hrm\Entities\Designation;

class EmployeeApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $employees = Employee::with(['branch', 'department', 'designation'])
            ->get(['id', 'name', 'email', 'phone', 'employee_id', 'branch_id', 'department_id', 'designation_id']);
        
        return response()->json([
            'status'        =>  true,
            'response_code' =>  200,
            'message'       =>  "",
            'data'          => $employees
        ], 200);
    }
}
