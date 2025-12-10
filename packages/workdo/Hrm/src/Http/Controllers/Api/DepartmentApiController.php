<?php

namespace Workdo\Hrm\Http\Controllers\Api;

use App\Models\Setting;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Hrm\Entities\Branch;
use Workdo\Hrm\Entities\Department;
use Workdo\Hrm\Entities\Designation;
use Workdo\Hrm\Entities\Employee;
use Workdo\Hrm\Events\CreateDepartment;
use Workdo\Hrm\Events\DestroyDepartment;
use Workdo\Hrm\Events\UpdateDepartment;

class DepartmentApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
            $departments = Department::with('branch')->get(['name']);
            return response()->json([
                'status'        =>  true,
                'response_code' =>  200,
                'message'       =>  "",
                'data'          => $departments
            ], 200);
        
    }

   
}
