<?php

namespace Workdo\Taskly\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Invoice;
use App\Models\User;
use Workdo\Hrm\Entities\Employee;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Workdo\Taskly\Entities\ActivityLog;
use App\Imports\TaskImport;
use App\Traits\SendSmsTraits;
use App\Traits\LogsTaskActivity;
use Maatwebsite\Excel\Facades\Excel;


use Workdo\Taskly\Entities\BugComment;
use Workdo\Taskly\Entities\BugFile;
use Workdo\Taskly\Entities\BugReport;
use Workdo\Taskly\Entities\BugStage;
use Workdo\Taskly\Entities\ClientProject;
use Workdo\Taskly\Entities\Comment;
use Workdo\Taskly\Entities\Milestone;
use Workdo\Taskly\Entities\Project;
use Workdo\Taskly\Entities\ProjectFile;
use Workdo\Taskly\Entities\Stage;
use Workdo\Taskly\Entities\SubTask;
use Workdo\Taskly\Entities\Task;
use Workdo\Taskly\Entities\TaskFile;
use Workdo\Taskly\Entities\UserProject;
use Workdo\Taskly\Entities\VenderProject;
use Workdo\TimeTracker\Entities\TimeTracker;
use Workdo\Taskly\Events\CreateBug;
use Workdo\Taskly\Events\CreateMilestone;
use Workdo\Taskly\Events\CreateProject;
use Workdo\Taskly\Events\CreateTask;
use Workdo\Taskly\Events\CreateTaskComment;
use Workdo\Taskly\Events\DestroyBug;
use Workdo\Taskly\Events\DestroyMilestone;
use Workdo\Taskly\Events\DestroyProject;
use Workdo\Taskly\Events\DestroyTask;
use Workdo\Taskly\Events\DestroyTaskComment;
use Workdo\Taskly\Events\UpdateBug;
use Workdo\Taskly\Events\UpdateMilestone;
use Workdo\Taskly\Events\UpdateProject;
use Workdo\Taskly\Events\UpdateTask;
use Workdo\Taskly\Events\UpdateTaskStage;
use Workdo\Taskly\Events\ProjectInviteUser;
use Workdo\Taskly\Events\ProjectShareToClient;
use Workdo\Taskly\Events\ProjectUploadFiles;
use Workdo\Taskly\Events\UpdateBugStage;
use Workdo\Account\Entities\Bill;
use Workdo\Documents\Entities\Document;
use Workdo\Retainer\Entities\Retainer;
use App\Models\Proposal;
use App\Models\Purchase;
use App\Models\Staging;
use App\Models\StagingTask;
use Workdo\Taskly\DataTables\ProjectBugDatatable;
use Workdo\Taskly\DataTables\ProjectDatatable;
use Workdo\Taskly\DataTables\ProjectTaskDatatable;
use Workdo\Taskly\DataTables\ProjectDoneTaskDatatable;
use Workdo\Taskly\Traits\TaskTraits;
use Illuminate\Support\Facades\Validator;


class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
     use TaskTraits;
     use SendSmsTraits;
     use LogsTaskActivity;
    public function index(Request $request)
    {
        if (Auth::user()->isAbleTo('project manage')) {
            $objUser          = Auth::user();
            if(Auth::user()->hasRole('client'))
            {
                $projects = Project::select('projects.*')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')->projectonly()->where('client_projects.client_id', '=', Auth::user()->id)->where('projects.workspace', '=',getActiveWorkSpace());
            }
            else
            {
                $projects = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->projectonly()->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', getActiveWorkSpace());
            }
            if($request->start_date)
            {
                $projects->where('start_date',$request->start_date);
            }
            if($request->end_date)
            {
                $projects->where('end_date',$request->end_date);
            }
            $projects = $projects->paginate(11);
            // dd($projects);

            return view('taskly::projects.index', compact('projects'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if (Auth::user()->isAbleTo('project create')) {
            if (module_is_active('CustomField')) {
                $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id', getActiveWorkSpace())->where('module', '=', 'taskly')->where('sub_module', 'projects')->get();
            } else {
                $customFields = null;
            }
            $workspace_users  = User::where('created_by', creatorId())->emp()->where('workspace_id', getActiveWorkSpace())->orWhere('id', Auth::user()->id)->get();
            return view('taskly::projects.create', compact('customFields', 'workspace_users'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {

        if (Auth::user()->isAbleTo('project create')) {
            $objUser          = Auth::user();
            $currentWorkspace = getActiveWorkSpace();
            $request->validate([
                'name' => 'required',
                'description' => 'required',
            ]);
            $post = $request->all();
            $post['start_date']  = $post['end_date']  = date('Y-m-d');
            $post['workspace']  = $currentWorkspace;
            $post['created_by'] = $objUser->id;
            $post['copylinksetting']   = '{"member":"on","client":"on","milestone":"off","progress":"off","basic_details":"on","activity":"off","attachment":"on","bug_report":"on","task":"off","invoice":"off","timesheet":"off" ,"password_protected":"off"}';
            $userList           = [];
            if (isset($post['users_list'])) {
                $userList = $post['users_list'];
            }
            $user = User::find(creatorId());
            $userList[] = $user->email;

            if (isset($objUser)) {
                $userList[] = $objUser->email;
            }

            $userList = array_unique($userList);
            $objProject = Project::create($post);
            foreach ($userList as $email) {
                $permission    = 'Member';
                $registerUsers = User::where('active_workspace', $currentWorkspace)->where('email', $email)->first();
                if ($registerUsers) {
                    if ($registerUsers->id == $objUser->id) {
                        $permission = 'Owner';
                    }
                } else {
                    $arrUser                      = [];
                    $arrUser['name']              = 'No Name';
                    $arrUser['email']             = 'bohil38865@bustayes.com';
                    $password                     = \Str::random(8);
                    $arrUser['password']          = Hash::make($password);
                    $arrUser['email_verified_at'] = date('Y-m-d h:i:s');
                    $arrUser['currant_workspace'] = $objProject->workspace;
                    $registerUsers                = User::create($arrUser);
                    $registerUsers->password      = $password;

                    //Email notification
                    if (!empty(company_setting('Create User')) && company_setting('Create User')  == true) {
                        $uArr = [
                            'email' => $email,
                            'password' => $password,
                            'company_name' => 'No Name',
                        ];
                        $smtp_error = EmailTemplate::sendEmailTemplate('New User', [$email], $uArr);
                    }
                }
                $this->inviteUser($registerUsers, $objProject, $permission);
            }

            if(Auth::user()->hasRole('client')) {
                    $clients = new ClientProject();
                    $clients['client_id'] = Auth::user()->id;
                    $clients['project_id'] = $objProject->id;
                    $clients->save();
            }
            if (module_is_active('CustomField')) {
                \Workdo\CustomField\Entities\CustomField::saveData($objProject, $request->customField);
            }

            event(new CreateProject($request, $objProject));

            return redirect()->back()->with('success', __('The project has been created successfully.') . ((isset($smtp_error)) ? ' <br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Project $project)
    {
        if (\Auth::user()->isAbleTo('project show')) {
            $daysleft = round((((strtotime($project->end_date) - strtotime(date('Y-m-d'))) / 24) / 60) / 60);

            if ($project) {
                $chartData = $this->getProjectChart(
                    [
                        'workspace_id' => getActiveWorkSpace(),
                        'project_id' => $project->id,
                        'duration' => 'week',
                    ]
                );
            }
            return view('taskly::projects.show', compact('project', 'daysleft', 'chartData'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function getProjectChart($arrParam)
    {
        $arrDuration = [];
        if ($arrParam['duration'] && $arrParam['duration'] == 'week') {
            $previous_week = Project::getFirstSeventhWeekDay(-1);
            foreach ($previous_week['datePeriod'] as $dateObject) {
                $arrDuration[$dateObject->format('Y-m-d')] = $dateObject->format('D');
            }
        }

        $arrTask = [
            'label' => [],
            'color' => [],
        ];
        $stages           = Stage::where('workspace_id', '=', $arrParam['workspace_id'])->orderBy('order');
        $stagesQuery = $stages->pluck('name', 'id')->toArray();
        $userObj          = Auth::user();

        foreach ($arrDuration as $date => $label) {
            $objProject = Task::select('status', DB::raw('count(*) as total'))->where('is_missed',0)->whereDate('created_at', '=', $date)->groupBy('status');
            if (Auth::check() && !Auth::user()->hasRole('client') && !Auth::user()->hasRole('company')) {
                if (isset($userObj) && $userObj) {
                    $objProject->whereRaw("find_in_set('" . $userObj->name . "',assign_to)");
                }
            }
           
            if (isset($arrParam['workspace_id'])) {
                $objProject->where('workspace',$arrParam['workspace_id']);
            }
            $data = $objProject->pluck('total', 'status')->all() ?? [];
            foreach ($stagesQuery as $id => $stage) {
                $arrTask[$id][] = isset($data[$id]) ? $data[$id] : 0;
            }
            $arrTask['label'][] = __($label);
        }
        $arrTask['stages'] = $stagesQuery;
        $arrTask['color'] = $stages->pluck('color')->toArray();
        return $arrTask;
    }


    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Project $project)
    {
        if (Auth::user()->isAbleTo('project edit')) {
            if (module_is_active('CustomField')) {
                $project->customField = \Workdo\CustomField\Entities\CustomField::getData($project, 'taskly', 'projects');
                $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'taskly')->where('sub_module', 'projects')->get();
            } else {
                $customFields = null;
            }
            return view('taskly::projects.edit', compact('project', 'customFields'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, Project $project)
    {
        if (Auth::user()->isAbleTo('project edit')) {
            $project->update($request->all());

            if (module_is_active('CustomField')) {
                \Workdo\CustomField\Entities\CustomField::saveData($project, $request->customField);
            }
            event(new UpdateProject($request, $project));
            return redirect()->back()->with('success', __('The project details are updated successfully.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($projectID)
    {
        $objUser = Auth::user();
        $project = Project::find($projectID);

        if ($project->created_by == $objUser->id || $objUser->type == 'company') {
            $task = Task::where('project_id', '=', $project->id)->count();
            $bug = BugReport::where('project_id', '=', $project->id)->count();

            if ($task == 0 && $bug == 0) {
                UserProject::where('project_id', '=', $projectID)->delete();
                $ProjectFiles = ProjectFile::where('project_id', '=', $projectID)->get();
                foreach ($ProjectFiles as $ProjectFile) {

                    delete_file($ProjectFile->file_path);
                    $ProjectFile->delete();
                }

                Milestone::where('project_id', '=', $projectID)->delete();
                ActivityLog::where('project_id', '=', $projectID)->delete();

                if (module_is_active('CustomField')) {
                    $customFields = \Workdo\CustomField\Entities\CustomField::where('module', 'taskly')->where('sub_module', 'projects')->get();
                    foreach ($customFields as $customField) {
                        $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $projectID)->where('field_id', $customField->id)->first();
                        if (!empty($value)) {
                            $value->delete();
                        }
                    }
                }
                event(new DestroyProject($project));
                $project->delete();
                return redirect()->route('projects.index')->with('success', __('The project has been deleted'));
            } else {
                return redirect()->route('projects.index')->with('error', __('There are some Task and Bug on Project, please remove it first!'));
            }
        } else {
            return redirect()->route('projects.index')->with('error', __("You can't Delete Project!"));
        }
    }
    public function milestone($projectID)
    {
        $currentWorkspace = getActiveWorkSpace();
        $project          = Project::find($projectID);
        return view('taskly::projects.milestone', compact('currentWorkspace', 'project'));
    }

    public function milestoneStore($projectID, Request $request)
    {
        $project          = Project::find($projectID);
        $request->validate(
            [
                'title' => 'required',
                'status' => 'required',
                'cost' => 'required',
                'summary' => 'required',
            ]
        );

        $milestone             = new Milestone();
        $milestone->project_id = $project->id;
        $milestone->title      = $request->title;
        $milestone->status     = $request->status;
        $milestone->cost       = $request->cost;
        $milestone->summary    = $request->summary;
        $milestone->save();


        ActivityLog::create(
            [
                'user_id' => Auth::user()->id,
                'user_type' => get_class(Auth::user()),
                'project_id' => $project->id,
                'log_type' => 'Create Milestone',
                'remark' => json_encode(['title' => $milestone->title]),
            ]
        );
        event(new CreateMilestone($request, $milestone));

        return redirect()->back()->with('success', __('The milestone has been created successfully.'));
    }

    public function milestoneEdit($milestoneID)
    {
        $currentWorkspace = getActiveWorkSpace();
        $milestone        = Milestone::find($milestoneID);

        return view('taskly::projects.milestoneEdit', compact('milestone', 'currentWorkspace'));
    }

    public function milestoneUpdate($milestoneID, Request $request)
    {
        $request->validate(
            [
                'title' => 'required',
                'status' => 'required',
                'cost' => 'required',
            ]
        );

        $milestone          = Milestone::find($milestoneID);
        $milestone->title   = $request->title;
        $milestone->status  = $request->status;
        $milestone->cost    = $request->cost;
        $milestone->summary = $request->summary;
        $milestone->progress = $request->progress;
        $milestone->start_date = $request->start_date;
        $milestone->end_date = $request->end_date;
        $milestone->save();

        event(new UpdateMilestone($request, $milestone));

        return redirect()->back()->with('success', __('The milestone details are updated successfully.'));
    }

    public function milestoneDestroy($milestoneID)
    {
        $milestone        = Milestone::find($milestoneID);

        event(new DestroyMilestone($milestone));

        $milestone->delete();

        return redirect()->back()->with('success', __('The milestone has been deleted.'));
    }

    public function milestoneShow($milestoneID)
    {
        $currentWorkspace = getActiveWorkSpace();
        $milestone        = Milestone::find($milestoneID);

        return view('taskly::projects.milestoneShow', compact('currentWorkspace', 'milestone'));
    }

    public function inviteUser($user, $project, $permission)
    {
        // assign project
        $arrData               = [];
        $arrData['user_id']    = $user->id;
        $arrData['project_id'] = $project->id;
        $is_invited            = UserProject::where($arrData)->first();
        $smtp_error = [];
        $smtp_error['status'] = true;
        $smtp_error['msg'] = '';
        $company_settings = getCompanyAllSetting();
        $project->url = route('projects.show',$project->id);

        if (!$is_invited) {
            UserProject::create($arrData);
            if ($permission != 'company') {
                if (!empty($company_settings['User Invited']) && $company_settings['User Invited']  == true) {
                    $uArr = [
                        'name' => $user->name,
                        'project' => $project->name,
                        'project_creater_name' => $project->creater->name,
                        'url' => $project->url,
                    ];
                    try {

                        if($company_settings['User Invited'])
                        {
                            $resp = EmailTemplate::sendEmailTemplate('User Invited', [$user->email], $uArr);
                        }
                        else
                        {
                                    $smtp_error['status'] = false;
                                    $smtp_error['msg'] = __('Something went wrong please try again ');
                        }
                    } catch(\Exception $e)
                    {
                        $smtp_error['status'] = false;
                        $smtp_error['msg'] = $e->getMessage();
                    }
                }
            }
            return $smtp_error;
        } else {
            $smtp_error['status'] = false;
            $smtp_error['msg'] = 'User already invited.';
            return $smtp_error;
        }
    }
    public function popup($projectID)
    {
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();
        $project          = Project::where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();
        $workspace_clients  = User::where('created_by', creatorId())->where('type', 'client')->where('workspace_id', getActiveWorkSpace())->get();

        $workspace_users = User::where('created_by', '=', creatorId())->emp()->where('workspace_id', getActiveWorkSpace())->whereNOTIn(
            'id',
            function ($q) use ($project) {
                $q->select('user_id')->from('user_projects')->where('project_id', '=', $project->id);
            }
        )->get();

        return view('taskly::projects.invite', compact('currentWorkspace', 'project', 'workspace_users', 'workspace_clients'));
    }

    public function invite(Request $request, $projectID)
    {
        $currentWorkspace = getActiveWorkSpace();
        $post             = $request->all();
        $userList         = $post['users_list'];

        $objProject = Project::find($projectID);

        foreach ($userList as $email) {
            $permission    = 'Member';
            $registerUsers = User::where('email', $email)->where('workspace_id', $currentWorkspace)->first();
            if ($registerUsers) {
                $user_in = $this->inviteUser($registerUsers, $objProject, $permission);
                if ($user_in['status'] != true) {
                    return redirect()->back()->with('error', $user_in['msg']);
                }
            } else {
                $arrUser                      = [];
                $arrUser['name']              = 'No Name';
                $arrUser['email']             = 'bohil38865@bustayes.com';
                $password                     = \Str::random(8);
                $arrUser['password']          = Hash::make($password);
                $arrUser['email_verified_at'] = date('Y-m-d h:i:s');
                $arrUser['currant_workspace'] = $objProject->workspace;
                $registerUsers                = User::create($arrUser);
                $registerUsers->password      = $password;

                //Email notification
                if (!empty(company_setting('Create User')) && company_setting('Create User')  == true) {
                    $uArr = [
                        'email' => $email,
                        'password' => $password,
                        'company_name' => 'No Name',
                    ];
                    $smtp_error = EmailTemplate::sendEmailTemplate('New User', [$email], $uArr);
                }
                $this->inviteUser($registerUsers, $objProject, $permission);
            }

            event(new ProjectInviteUser($request, $registerUsers, $objProject));

            ActivityLog::create(
                [
                    'user_id' => Auth::user()->id,
                    'user_type' => get_class(Auth::user()),
                    'project_id' => $objProject->id,
                    'log_type' => 'Invite User',
                    'remark' => json_encode(['user_id' => $registerUsers->id]),
                ]
            );
        }

        return redirect()->back()->with('success', __('Users Invited Successfully!') . ((!empty($smtp_error) && $smtp_error['is_success'] == false && !empty($smtp_error['error'])) ? '<br> <span class="text-danger">' . $smtp_error['error'] . '</span>' : ''));
    }

    public function sharePopup($projectID)
    {
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();
        $project          = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();

        $clients = User::where('created_by', '=', creatorId())->where('type', 'client')->where('workspace_id', getActiveWorkSpace())->whereNOTIn(
            'id',
            function ($q) use ($project) {
                $q->select('client_id')->from('client_projects')->where('project_id', '=', $project->id);
            }
        )->get();
        return view('taskly::projects.share', compact('currentWorkspace', 'project', 'clients'));
    }

    public function sharePopupVender($projectID)
    {
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();
        $project          = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();

        $venders = User::where('created_by', '=', creatorId())->where('type', 'vendor')->where('workspace_id', getActiveWorkSpace())->whereNOTIn(
            'id',
            function ($q) use ($project) {
                $q->select('vender_id')->from('vender_projects')->where('project_id', '=', $project->id);
            }
        )->get();
        return view('taskly::projects.share_vender', compact('currentWorkspace', 'project', 'venders'));
    }


    public function share($projectID, Request $request)
    {

        $project = Project::find($projectID);
        foreach ($request->clients as $client_id) {
            $client = User::where('type', 'client')->where('id', $client_id)->first();

            if (ClientProject::where('client_id', '=', $client_id)->count() == 0) {
                ClientProject::create(
                    [
                        'client_id' => $client_id,
                        'project_id' => $projectID,
                        'permission' => '',
                    ]
                );
            }

            $company_settings = getCompanyAllSetting();
            $project->url = route('projects.show',$project->id);


            if (!empty($company_settings['Project Assigned']) && $company_settings['Project Assigned']  == true) {
                $uArr = [
                    'name' => $client->name,
                    'project' => $project->name,
                    'project_creater_name' => $project->creater->name,
                    'url' => $project->url,
                ];
                try {

                    if($company_settings['Project Assigned'])
                    {
                        $resp = EmailTemplate::sendEmailTemplate('Project Assigned', [$client->email], $uArr);
                    }
                    else
                    {
                        $smtp_error = __('Email Not Sent!!');
                    }
                } catch(\Exception $e)
                {
                    $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                }
            }


            event(new ProjectShareToClient($request, $client, $project));

            ActivityLog::create(
                [
                    'user_id' => Auth::user()->id,
                    'user_type' => get_class(Auth::user()),
                    'project_id' => $project->id,
                    'log_type' => 'Share with Client',
                    'remark' => json_encode(['client_id' => $client->id]),
                ]
            );
        }

        return redirect()->back()->with('success', __('Project Share Successfully!') . ((isset($smtp_error)) ? ' <br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
    }

    public function sharePopupVenderStore($projectID, Request $request)
    {
        $project = Project::find($projectID);
        foreach ($request->vendors as $vender_id) {
            $client = User::where('type', 'vendor')->where('id', $vender_id)->first();

            if (VenderProject::where('vender_id', '=', $vender_id)->count() == 0) {
                VenderProject::create(
                    [
                        'vender_id' => $vender_id,
                        'project_id' => $projectID,
                        'permission' => '',
                    ]
                );
            }
            $company_settings = getCompanyAllSetting();
            $project->url = route('projects.show',$project->id);


            if (!empty($company_settings['Project Assigned']) && $company_settings['Project Assigned']  == true) {
                $uArr = [
                    'name' => $client->name,
                    'project' => $project->name,
                    'project_creater_name' => $project->creater->name,
                    'url' => $project->url,
                ];
                try {

                    if($company_settings['Project Assigned'])
                    {
                        $resp = EmailTemplate::sendEmailTemplate('Project Assigned', [$client->email], $uArr);
                    }
                    else
                    {
                        $smtp_error = __('Email Not Sent!!');
                    }
                } catch(\Exception $e)
                {
                    $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                }
            }

            event(new ProjectShareToClient($request , $client , $project));

            ActivityLog::create(
                [
                    'user_id' => Auth::user()->id,
                    'user_type' => get_class(Auth::user()),
                    'project_id' => $project->id,
                    'log_type' => 'Share with Vender',
                    'remark' => json_encode(['vender_id' => $client->id]),
                ]
            );

        }

        return redirect()->back()->with('success', __('Project Share Successfully!') . ((isset($smtp_error)) ? ' <br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
    }

    public function getFirstSeventhWeekDay($week = null)
    {
        $first_day = $seventh_day = null;

        if (isset($week)) {
            $first_day   = Carbon::now()->addWeeks($week)->startOfWeek();
            $seventh_day = Carbon::now()->addWeeks($week)->endOfWeek();
        }

        $dateCollection['first_day']   = $first_day;
        $dateCollection['seventh_day'] = $seventh_day;

        $period = CarbonPeriod::create($first_day, $seventh_day);

        foreach ($period as $key => $dateobj) {
            $dateCollection['datePeriod'][$key] = $dateobj;
        }

        return $dateCollection;
    }

    public function gantt($projectID, $duration = 'Week')
    {
        if (\Auth::user()->isAbleTo('sub-task manage')) {
            $objUser          = Auth::user();
            $currentWorkspace = getActiveWorkSpace();
            $is_client = '';

            if ($objUser->hasRole('client')) {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();

                $is_client = 'client.';
            } else {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();
            }
            $tasks      = [];

            if ($objUser->type == 'client' || $objUser->type == 'company') {
                $tasksobj = Task::where('project_id', '=', $project->id)->orderBy('start_date')->get();
            } else {
                $tasksobj = Task::where('project_id', '=', $project->id)->whereRaw("FIND_IN_SET(" . $objUser->email . ",assign_to)")->orderBy('start_date')->get();
            }
            foreach ($tasksobj as $task) {
                $tmp                 = [];
                $tmp['id']           = 'task_' . $task->id;
                $tmp['name']         = $task->title;
                $tmp['start']        = $task->start_date;
                $tmp['end']          = $task->due_date;
                $tmp['custom_class'] = strtolower($task->priority);
                $tmp['progress']     = $task->subTaskPercentage();
                $tmp['extra']        = [
                    'priority' => __($task->priority),
                    'comments' => count($task->comments),
                    'duration' => Date::parse($task->start_date)->format('d M Y H:i A') . ' - ' . Date::parse($task->due_date)->format('d M Y H:i A'),
                ];
                $tasks[]             = $tmp;
            }

            return view('taskly::projects.gantt', compact('currentWorkspace', 'project', 'tasks', 'duration', 'is_client'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function ganttPost($projectID, Request $request)
    {
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();

        if ($objUser->hasRole('client')) {
            $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();
        }
        if ($project) {
            $id               = trim($request->task_id, 'task_');
            $task             = Task::find($id);
            $task->start_date = $request->start;
            $task->due_date   = $request->end;
            $task->save();

            return response()->json(
                [
                    'is_success' => true,
                    'message' => __("Time Updated"),
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'is_success' => false,
                    'message' => __("Something is wrong."),
                ],
                400
            );
        }
    }
    public function taskBoard(Request $request)
    {
        if (\Auth::user()->isAbleTo('task manage')) {
            $currentWorkspace = getActiveWorkSpace();

            $objUser = Auth::user();
            if (Auth::user()->hasRole('client')) {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('projects.workspace', '=', $currentWorkspace)->first();
            } else {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->first();
            }
            $stages = $statusClass = [];
            // if ($project) {
                $stages = Stage::where('workspace_id', '=', getActiveWorkSpace())->orderBy('order')->get();
                foreach ($stages as $status) {
                    $statusClass[] = 'task-list-' . str_replace(' ', '_', $status->id);

                    $task          = Task::query();
                    if (!Auth::user()->hasRole('client') && !Auth::user()->hasRole('company')) {
                        if (isset($objUser) && $objUser) {
                            $task->whereRaw("find_in_set('" . $objUser->email . "',assign_to)");
                        }
                    }
                    $task->orderBy('order');
                    $status['tasks'] = $task->where('status', '=', $status->id)->get();
                }
                $users = User::select('users.*')->get();
                return view('taskly::projects.taskboard', compact('currentWorkspace', 'project', 'stages', 'statusClass', 'users'));
            // } else {
            //     return redirect()->back()->with('error', __('Task Note Found.'));
            // }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function taskCreate(Request $request)
    {
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();
        if (module_is_active('CustomField')) {
            $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id', getActiveWorkSpace())->where('module', '=', 'taskly')->where('sub_module', 'tasks')->get();
        } else {
            $customFields = null;
        }
        if ($objUser->hasRole('client')) {
            $project  = Project::select('projects.*')->where('projects.workspace', '=', $currentWorkspace)->first();
            $projects = Project::select('projects.*')->join('client_projects', 'client_projects.project_id', '=', 'projects.id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->get();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->first();;
            $projects = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->get();
        }

        $users = User::select('users.*')->get();
        $stages = Stage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->get();

        return view('taskly::projects.taskCreate', compact('currentWorkspace', 'customFields', 'project', 'projects', 'users','stages'));
    }
    
    public function multipleTaskCreate(Request $request)
    {
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();
        if (module_is_active('CustomField')) {
            $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id', getActiveWorkSpace())->where('module', '=', 'taskly')->where('sub_module', 'tasks')->get();
        } else {
            $customFields = null;
        }
        if ($objUser->hasRole('client')) {
            $project  = Project::select('projects.*')->where('projects.workspace', '=', $currentWorkspace)->first();
            $projects = Project::select('projects.*')->join('client_projects', 'client_projects.project_id', '=', 'projects.id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->get();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->first();;
            $projects = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->get();
        }

        $users = User::select('users.*')->get();
        $stages = Stage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->get();

        return view('taskly::projects.multipletask', compact('currentWorkspace', 'customFields', 'project', 'projects', 'users','stages'));
    } 

public function taskStore(Request $request)
{
    $request->validate([
        'title' => 'required',
        'assign_to' => 'required',
        'assignor' => 'required',
        'priority' => 'required',
        'start_date' => 'required',
        'due_date' => 'required',
        'description' => 'nullable',
    ]);
    
    $objUser = Auth::user();
    $currentWorkspace = getActiveWorkSpace();
    $post = $request->all();

    // Handle All Members selection
    if (isset($post['assign_to']) && in_array('all_members', $post['assign_to'])) {
        $allUsers = \App\Models\User::where('workspace_id', getActiveWorkSpace())
            ->whereNotIn('email', ['company@example.com', 'president@5core.com', 'superadmin@example.com'])
            ->pluck('email')
            ->toArray();
        $post['assign_to'] = $allUsers;
        $request->merge(['assign_to' => $allUsers]);
    }
    
    // Handle All Managers selection
    if (isset($post['assign_to']) && in_array('all_managers', $post['assign_to'])) {
        $managerEmails = ['tech-support@5core.com', 'support@5core.com', 'mgr-advertisement@5core.com', 'mgr-content@5core.com', 'hr@5core.com','inventory@5core.com']; // Add all manager emails here
        $post['assign_to'] = $managerEmails;
        $request->merge(['assign_to' => $managerEmails]);
    }
    
    // Rest of your existing code...
    if(!empty($request->stage_id)) {
        $stage = Stage::where('workspace_id', '=', $currentWorkspace)->where('name',$request->stage_id)->orderBy('order')->first();
    } else {
        $stage = Stage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->first();
    }
    
    if ($stage) {
        $split_tasks = $request->filled('split_tasks') ? 1 : 0;
        $upload = null;
        if ($request->hasFile('file')) {
           $fileName =time() . "_" . $request->file->getClientOriginalName();
            $upload = upload_file($request, 'file', $fileName, 'tasks', []);
            // Check if upload failed
            if (!isset($upload['flag']) || $upload['flag'] != 1 || !isset($upload['url'])) {
                return redirect()->back()->withInput()->with('error', __('File upload failed: ') . ($upload['msg'] ?? __('Unknown error occurred')));
            }
        }
        
        if($split_tasks) {
            foreach($request->assign_to as $kay => $assign_to) {
                $post['status'] = $stage->name;
                $post['assign_to'] = $assign_to;
                $post['assignor'] = is_array($request->assignor) ? implode(",", $request->assignor) : $request->assignor;
                $post['link1'] = $request->link1;
                $post['group'] = $request->group;
                $post['eta_time'] = $request->eta_time ?? 0;
                $post['link2'] = $request->link2;
                $post['split_tasks'] = $split_tasks;
                $post['link3'] = $request->link3;
                $post['link4'] = $request->link4;
                $post['link5'] = $request->link5;
                $post['link7'] = $request->link7;
                $post['link8'] = $request->link8;
                $post['link6'] = $request->link6;
                $post['link9'] = $request->link9;
                $post['workspace'] = getActiveWorkSpace();
                $task = Task::create($post);
                $taskID = $task->id;
                // Log task creation activity
                $this->logTaskCreation($task->title, 'Task created and assigned to: ' . $assign_to);
                $this->sendSms($task);
                        if ($request->hasFile('file') && $upload !== null) {
                            $postFile =[];
                            $postFile['task_id']   = $taskID;
                            $postFile['file']      = $upload['url'];
                            $postFile['name']      = $request->file->getClientOriginalName();
                            $postFile['extension']      = $request->file->getClientOriginalExtension();
                            $postFile['file_size']      = $request->file->getSize();
                            $postFile['created_by'] = Auth::user()->id;
                            $postFile['user_type']  = 'User';
                            $TaskFile            = TaskFile::create($postFile);
                        }
            }  
        } else {
            $post['status'] = $stage->name;
            $post['assign_to'] = implode(",",$request->assign_to);
            $post['assignor'] = is_array($request->assignor) ? implode(",", $request->assignor) : $request->assignor;
            $post['link1'] = $request->link1;
            $post['group'] = $request->group;
            $post['split_tasks'] = $split_tasks;
            $post['eta_time'] = $request->eta_time ?? 0;
            $post['link2'] = $request->link2;
            $post['link3'] = $request->link3;
            $post['link4'] = $request->link4;
            $post['link5'] = $request->link5;
            $post['link7'] = $request->link7;
            $post['link8'] = $request->link8;
            $post['link6'] = $request->link6;
            $post['link9'] = $request->link9;
            $post['workspace'] = getActiveWorkSpace();
            $task = Task::create($post);
            $taskID = $task->id;
            // Log task creation activity
            $this->logTaskCreation($task->title, 'Task created and assigned to: ' . $post['assign_to']);
            $this->sendSms($task);
             if ($request->hasFile('file') && $upload !== null) {
              $postFile =[];
                $postFile['task_id']   = $taskID;
                $postFile['file']      = $upload['url'];
                $postFile['name']      = $request->file->getClientOriginalName();
                $postFile['extension']      = $request->file->getClientOriginalExtension();
                $postFile['file_size']      = $request->file->getSize();
                $postFile['created_by'] = Auth::user()->id;
                $postFile['user_type']  = 'User';
                $TaskFile            = TaskFile::create($postFile);
             }
        }
        
        // Rest of your file handling and activity logging code...
        
        $returnUrl = route('projecttask.list',['is_add_enable'=>'true']);
        return redirect($returnUrl)->with('success', __('The task has been created successfully.'));
    } else {
        return redirect()->back()->with('error', __('Please add stages first.'));
    }
}

// save multiple task
public function multipleTaskSave(Request $request)
{
    $request->validate([
    'title' => 'required|array',
    'title.*' => 'required|string',
    'assign_to' => 'required|array',
    'assign_to.*' => 'required',
    'assign_by' => 'required|array',
    'priority' => 'required|array',
    'priority.*' => 'required|string',
    'duration' => 'required|array',
    'duration.*' => 'required|string',
]);

$currentWorkspace = getActiveWorkSpace();
$objUser = Auth::user();

$titles        = $request->title;
$assign_tos    = $request->assign_to;
$assign_bys    = $request->assign_by;
$groups        = $request->group ?? [];
$priorities    = $request->priority;
$durations     = $request->duration;
$descriptions  = $request->description ?? [];
$eta_times     = $request->eta_time ?? [];
$stage_ids     = $request->stage_id ?? [];
$links_data    = $request->links_data ?? [];

// Loop through each task index
foreach ($titles as $index => $title) {

    $assign_to = $assign_tos[$index] ?? null;
    $assign_by = $assign_bys[$index] ?? $objUser->id;
    $group     = $groups[$index] ?? null;
    $priority  = $priorities[$index] ?? 'normal';
    $duration  = $durations[$index] ?? null;
    $description = $descriptions[$index] ?? null;
    $eta_time  = $eta_times[$index] ?? 0;
    $stageName = $stage_ids[$index] ?? 'todo';
    $links     = $links_data[$index] ?? null;

    // Find the stage (default to first if not found)
    $stage = Stage::where('workspace_id', $currentWorkspace)
        ->where('name', $stageName)
        ->orderBy('order')
        ->first() ?? Stage::where('workspace_id', $currentWorkspace)->orderBy('order')->first();

    if (!$stage) {
        return back()->with('error', __('Please add stages first.'));
    }

    // Handle duration (split start_date & due_date)
    [$start_date, $due_date] = explode(' to ', $duration);

    $taskData = [
        'title'        => $title,
        'assign_to'    => $assign_to,
        'assignor'     => $assign_by,
        'group'        => $group,
        'priority'     => $priority,
        'status'       => $stage->name,
        'start_date'   => trim($start_date),
        'due_date'     => trim($due_date),
        'description'  => $description,
        'workspace'    => $currentWorkspace,
        'eta_time'     => $eta_time,
        'link1'        => $links,
        'created_by'   => $objUser->id,
    ];

    $task = Task::create($taskData);
// dd($task);
    // Optional: file handling
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $fileName = time() . "_" . $file->getClientOriginalName();
        $upload = upload_file($request, 'file', $fileName, 'tasks', []);

        TaskFile::create([
            'task_id'    => $task->id,
            'file'       => $upload['url'],
            'name'       => $file->getClientOriginalName(),
            'extension'  => $file->getClientOriginalExtension(),
            'file_size'  => $file->getSize(),
            'created_by' => $objUser->id,
            'user_type'  => 'User',
        ]);
    }

    // Logging, notification, etc.
    $this->logTaskCreation($task->title, 'Task created and assigned to: ' . $assign_to);
    $this->sendSms($task);
}

return redirect()->route('projecttask.list', ['is_add_enable' => 'true'])
    ->with('success', __('All tasks created successfully.'));
}
// staging event create
public function StagingCreateEvent(Request $request)
{
    // Step 1: Validate request
    $validator = Validator::make($request->all(), [
        'event_name' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }

    // Step 2: Store the event
    $staging = Staging::create([
        'user_id' => Auth::id(),
        'event' => $request->event_name,
        'event_note' => $request->event_note,
        'status' => 1, // default pending, optional
    ]);

    // Step 3: Return response
    return response()->json([
        'success' => true,
        'message' => 'Event logged successfully.',
        'data' => $staging
    ]);
}
// staging event delete
public function StagingDelete(Request $request)
{
    $request->validate([
        'id' => 'required|integer|exists:stagings,id'
    ]);

    $staging = Staging::find($request->id);

    // Optional: ensure only the user who created it can delete
    if ($staging->user_id !== auth()->id()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized to delete this event.'
        ], 403);
    }

    $staging->delete();

    return response()->json([
        'success' => true,
        'message' => 'Event deleted successfully.'
    ]);
}

// create task
public function StagingSubmitTask(Request $request)
{
    $request->validate([
        'event_id' => 'required|exists:stagings,id',
        'tasks' => 'required|array|min:1',
    ]);

    foreach ($request->tasks as $task) {
        StagingTask::create([
            'event_id'    => $request->event_id,
            'user_id'     => Auth::id(),
            'group'       => $task['group'] ?? '',
            'task'        => $task['task'] ?? '',
            'assignor_id' => $task['assignor_id'] ?? null,
            'assignee_id' => $task['assignee_id'] ?? null,
            'start_date'  => $task['start_date'] ?? null,
            'end_date'    => $task['end_date'] ?? null,
            'status'      => $task['status_value'] ?? 'pending',
            'priority'    => $task['priority_value'] ?? 'medium',
            'task_note'   => $task['task_note'] ?? null,
            'l1' => $task['l1'] ?? null,
            'l2' => $task['l2'] ?? null,
            'l3' => $task['l3'] ?? null,
            'l4' => $task['l4'] ?? null,
            'l5' => $task['l5'] ?? null,
            'l6' => $task['l6'] ?? null,
            'l7' => $task['l7'] ?? null,
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Tasks saved successfully!',
    ]);
}

    public function taskShow($taskID)
    {
        $currentWorkspace = getActiveWorkSpace();
        $task             = Task::find($taskID);
        $objUser          = Auth::user();

        $clientID = '';
        if ($objUser->hasRole('client')) {
            $clientID = $objUser->id;
        }

        $users           = User::select('users.*')->join('user_projects', 'user_projects.user_id', '=', 'users.id')->get();
        $assign_to = explode(",", $task->assign_to);


        return view('taskly::projects.taskShow', compact('currentWorkspace','users', 'task', 'clientID','assign_to'));
    }
    public function TaskMember(Request $request, $taskID){
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();
        if ($objUser->hasRole('client')) {
            $project = Project::where('projects.workspace', '=', $currentWorkspace)->first();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->first();
        }
        if ($project) {
            $post['assign_to'] = implode(",", $request->assign_to);
            $task              = Task::find($taskID);
            $task->update($post);
        }
        return redirect()->back()->with('success', __('Member assinged Successfully!'));

    }
    public function taskEdit($taskId)
    {
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();

        $users           = User::select('users.*')->get();
        $task            = Task::find($taskId);

        $stages = Stage::where('workspace_id', '=', getActiveWorkSpace())->where('created_by',creatorId())->get();
        if (module_is_active('CustomField')) {
            $task->customField = \Workdo\CustomField\Entities\CustomField::getData($task, 'taskly', 'tasks');
            $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'taskly')->where('sub_module', 'tasks')->get();
        } else {

            $customFields = null;
        }
        $task->assign_to = explode(",", $task->assign_to);

        return view('taskly::projects.taskEdit', compact('currentWorkspace', 'users', 'task', 'customFields','stages'));
    }
    // public function bulkAction(Request $request)
    // {
    //     $selectedIds = $request->selected_ids;
    //     $actionType = $request->action_type;
    //     if($actionType == 'delete')
    //     {
    //         Task::whereIn('id',$selectedIds)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
    //     }else{
    //         foreach($selectedIds as $selectedId){
               
    //             $actionData = $this->duplicateTask($selectedId);
    //         }
    //     }
    //     return response()->json(
    //         [
    //             'is_success' => true,
    //             'message' =>"Successfully deleted",
    //         ],
    //         200
    //     );
    // }
public function bulkAction(Request $request)
{
    $selectedIds = $request->selected_ids;
    $actionType = $request->action_type;
    $deleteRating = $request->delete_rating;
    $deleteFeedback = $request->delete_feedback;

    // Server-side permission check for delete action
    if ($actionType == 'delete') {
        $allowedEmails = ['president@5core.com', 'tech-support@5core.com', 'mgr-advertisement@5core.com', 'mgr-content@5core.com','sjoy7486@gmail.com','sr.manager@5core.com','ritu.kaur013@gmail.com','support@5core.com','mgr-operations@5core.com','inventory@5core.com'];
        
        // Check if user is in allowed emails list (can delete all tasks)
        if (in_array(Auth::user()->email, $allowedEmails)) {
            Task::whereIn('id', $selectedIds)->update([
                'deleted_at' => date('Y-m-d H:i:s'),
                'delete_rating' => $deleteRating,
                'delete_feedback' => $deleteFeedback
            ]);
        } else {
            // Regular user can only delete tasks where they are the assignor
            $userEmail = Auth::user()->email;
            
            // Get tasks where user is assignor
            $userAssignorTasks = Task::whereIn('id', $selectedIds)
                ->whereRaw("FIND_IN_SET(?, assignor)", [$userEmail])
                ->get();
            

            if ($userAssignorTasks->count() != count($selectedIds)) {
                return response()->json([
                    'is_success' => false,
                    'message' => __('Permission denied - You can only delete tasks where you are the assignor'),
                ], 403);
            }
            
            // Delete only the tasks where user is assignor
            Task::whereIn('id', $selectedIds)
                ->whereRaw("FIND_IN_SET(?, assignor)", [$userEmail])
                ->update([
                    'deleted_at' => date('Y-m-d H:i:s'),
                    'delete_rating' => $deleteRating,
                    'delete_feedback' => $deleteFeedback
                ]);
        }
    } else {
        // Handle duplicate action
        $testArray = [];
        foreach($selectedIds as $selectedId) {
            $this->duplicateTask($selectedId);
            // $testArray[]=$selectedId;
        }
        // return $testArray;
    }
    
    return response()->json([
        'is_success' => true,
        'message' => "Successfully processed",
    ], 200);
}

    public function bulkUpdateAssignor(Request $request)
    {
        try {
            $taskIds = $request->input('task_ids');
            $assignorEmail = $request->input('assignor_email');

            if (empty($taskIds) || empty($assignorEmail)) {
                return response()->json([
                    'is_success' => false,
                    'message' => 'Missing required data'
                ], 400);
            }

            // Parse task IDs if they come as JSON string
            if (is_string($taskIds)) {
                $taskIds = json_decode($taskIds, true);
            }

            // Validate assignor exists
            $assignor = User::where('email', $assignorEmail)->first();
            if (!$assignor) {
                return response()->json([
                    'is_success' => false,
                    'message' => 'Assignor not found'
                ], 404);
            }

            // Update assignor for all selected tasks
            $updatedCount = Task::whereIn('id', $taskIds)
                ->where('workspace', getActiveWorkSpace())
                ->update(['assignor' => $assignorEmail]);

            return response()->json([
                'is_success' => true,
                'message' => "Assignor updated successfully for {$updatedCount} task(s)",
                'updated_count' => $updatedCount
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'is_success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkUpdateAssignee(Request $request)
    {
        try {
            $taskIds = $request->input('task_ids');
            $assigneeEmail = $request->input('assignee_email');

            if (empty($taskIds) || empty($assigneeEmail)) {
                return response()->json([
                    'is_success' => false,
                    'message' => 'Missing required data'
                ], 400);
            }

            // Parse task IDs if they come as JSON string
            if (is_string($taskIds)) {
                $taskIds = json_decode($taskIds, true);
            }

            // Validate assignee exists
            $assignee = User::where('email', $assigneeEmail)->first();
            if (!$assignee) {
                return response()->json([
                    'is_success' => false,
                    'message' => 'Assignee not found'
                ], 404);
            }

            // Update assignee for all selected tasks
            $updatedCount = Task::whereIn('id', $taskIds)
                ->where('workspace', getActiveWorkSpace())
                ->update(['assign_to' => $assigneeEmail]);

            return response()->json([
                'is_success' => true,
                'message' => "Assignee updated successfully for {$updatedCount} task(s)",
                'updated_count' => $updatedCount
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'is_success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    public function bulkUpdateETC(Request $request)
{
    try {
        $taskIds = $request->input('task_ids');
        $etcValue = $request->input('etc_value');

        if (empty($taskIds) || empty($etcValue)) {
            return response()->json([
                'is_success' => false,
                'message' => 'Missing required data'
            ], 400);
        }

        // Parse task IDs if they come as JSON string
        if (is_string($taskIds)) {
            $taskIds = json_decode($taskIds, true);
        }

        // Validate ETC value
        if (!is_numeric($etcValue) || $etcValue < 0) {
            return response()->json([
                'is_success' => false,
                'message' => 'Invalid ETC value. Please enter a positive number.'
            ], 400);
        }

        // Update ETC for all selected tasks
        $updatedCount = Task::whereIn('id', $taskIds)
            ->where('workspace', getActiveWorkSpace())
            ->update(['eta_time' => $etcValue]);

        return response()->json([
            'is_success' => true,
            'message' => "ETC updated successfully for {$updatedCount} task(s)",
            'updated_count' => $updatedCount
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'is_success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], 500);
    }
}
public function bulkUpdateDate(Request $request)
{
   try {
        $taskIds = $request->input('task_ids');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $updateDueDateOnly = $request->input('update_due_date_only', false);

        if (empty($taskIds)) {
            return response()->json([
                'is_success' => false,
                'message' => 'No tasks selected'
            ], 400);
        }

        // Parse task IDs if they come as JSON string
        if (is_string($taskIds)) {
            $taskIds = json_decode($taskIds, true);
        }

        // Validate dates
        if ($startDate && !strtotime($startDate)) {
            return response()->json([
                'is_success' => false,
                'message' => 'Invalid start date format'
            ], 400);
        }

        if ($endDate && !strtotime($endDate)) {
            return response()->json([
                'is_success' => false,
                'message' => 'Invalid end date format'
            ], 400);
        }

        // Prepare update data
        $updateData = [];
        
        // Only update start date if it's provided AND we're not in "due date only" mode
        if ($startDate) {
            $updateData['start_date'] = $startDate;
        }
        
        // Always update end date if provided
        if ($endDate) {
            $updateData['due_date'] = $endDate;
        }

        // If no data to update
        if (empty($updateData)) {
            return response()->json([
                'is_success' => false,
                'message' => 'No date values provided for update'
            ], 400);
        }

        // Update dates for all selected tasks
        $updatedCount = Task::whereIn('id', $taskIds)
            ->where('workspace', getActiveWorkSpace())
            ->update($updateData);

        return response()->json([
            'is_success' => true,
            'message' => "Dates updated successfully for {$updatedCount} task(s)",
            'updated_count' => $updatedCount,
            'updated_fields' => array_keys($updateData)
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'is_success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], 500);
    }
}

public function bulkUpdatePriority(Request $request)
{
    try {
        $taskIds = $request->input('task_ids');
        $priority = $request->input('priority');

        if (empty($taskIds) || empty($priority)) {
            return response()->json([
                'is_success' => false,
                'message' => 'Missing required data'
            ], 400);
        }

        // Parse task IDs if they come as JSON string
        if (is_string($taskIds)) {
            $taskIds = json_decode($taskIds, true);
        }

        // Validate priority value
        $validPriorities = ['urgent', 'high', 'normal', 'low'];
        if (!in_array($priority, $validPriorities)) {
            return response()->json([
                'is_success' => false,
                'message' => 'Invalid priority value. Valid options are: ' . implode(', ', $validPriorities)
            ], 400);
        }

        // Update priority for all selected tasks
        $updatedCount = Task::whereIn('id', $taskIds)
            ->where('workspace', getActiveWorkSpace())
            ->update(['priority' => $priority]);

        return response()->json([
            'is_success' => true,
            'message' => "Priority updated successfully for {$updatedCount} task(s)",
            'updated_count' => $updatedCount
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'is_success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], 500);
    }
}
    public function inlineUpdate(Request $request)
    {
        $taskId = $request->task_id;
        $column = $request->column;
        $value = $request->value;
        if(!empty($taskId) && !empty($column) && !empty($value))
        {
            $post[$column] = $value;
            $task              = Task::find($taskId);
            $task->update($post);
        }
        return response()->json(
            [
                'is_success' => true,
                'message' => "Successfully Updated",
            ],
            200
        );
    }

    // update rework reason
    public function saveReworkReason(Request $request)
    {
        $taskId = $request->task_id;
        $status = 'Rework';
        $value = $request->rework_reason;
        if(!empty($taskId) && !empty($status) && !empty($value))
        {
          $task = Task::find($taskId);
          $task->status = $status;
          $task->rework_reason = $value;
          $task->save();
          $taskUpdated = Task::find($taskId);
          $this->sendATRSms($taskUpdated);
          return redirect()->back()->with('success', __('The task Status are updated successfully.'));
        }
        else{
        return redirect()->back()->with('warning', __('Something wrong please try again.'));   
        }    
    }

    public function updateEtcDone(Request $request)
{
    $taskId = $request->task_id;
    $etc = $request->etc;
    $completionDate = $request->completion_date; // Get the completion date
    
    if(!empty($taskId) && !empty($etc) && !empty($completionDate))
    {
        $task = Task::find($taskId);
        $task->etc_done = $etc;
        $task->completion_date = $completionDate; // Save completion date
        $task->status = "Done";
        $task->save();
        $taskUpdated = Task::find($taskId);
      $this->sendATCSms($taskUpdated);
        return response()->json([
            'is_success' => true,
            'message' => "Successfully Updated",
            "data" => $task
        ], 200);
    }
    
    return response()->json([
        'is_success' => false,
        'message' => "Missing required data"
    ], 400);
}
    public function taskUpdate(Request $request, $taskID)
    {
       
       
        $request->validate(
            [
                'title' => 'required',
                'assign_to' => 'required',
                'priority' => 'required',
                'start_date' => 'required',
                'due_date' => 'required',
                'description' => 'nullable',
            ]
        );
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();

            // Get the existing task first to preserve existing assignors
            $task = Task::find($taskID);
            
            $post              = $request->all();
             if(!empty($request->stage_id))
            {
             $stage = Stage::where('workspace_id', '=', $currentWorkspace)->where('name',$request->stage_id)->orderBy('order')->first();
            }else
            {
             $stage = Stage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->first();
            }
            $post['status']    = $stage->name;
            if($request->assignor)
            {
                // Get existing assignors from the task
                $existingAssignors = [];
                if ($task && $task->assignor) {
                    $existingAssignors = array_filter(array_map('trim', explode(',', $task->assignor)));
                }
                
                // Get new assignors from request
                $newAssignors = is_array($request->assignor) ? $request->assignor : [$request->assignor];
                $newAssignors = array_filter(array_map('trim', $newAssignors));
                
                // Merge existing and new assignors, remove duplicates
                $mergedAssignors = array_unique(array_merge($existingAssignors, $newAssignors));
                
                // Convert back to comma-separated string
                $post['assignor'] = implode(',', $mergedAssignors);
            }
             if($request->eta_time)
            {
                $post['eta_time'] = $request->eta_time;
            }
            
            // return $request->assign_to;
                $split_tasks = $request->filled('split_tasks') ? 1 : 0;
                $post['link1'] = $request->link1;
                $post['group'] = $request->group;
                $post['link2'] = $request->link2;
                $post['split_tasks'] = $split_tasks;
                  $post['link3'] = $request->link3;
                  $post['link4'] = $request->link4;
                  $post['link5'] = $request->link5;
                  $post['link7'] = $request->link7;
                  $post['link8'] = $request->link8;
                  $post['link6'] = $request->link6;
                  $post['link9'] = $request->link9;
            $post['assign_to'] =implode(',',$request->assign_to) ;
            $task->update($post);
            // Log task update activity
            $this->logTaskEdit($task->title, 'Task updated - Assigned to: ' . $post['assign_to']);
            if (module_is_active('CustomField')) {
                \Workdo\CustomField\Entities\CustomField::saveData($task, $request->customField);
            }
            event(new UpdateTask($request, $task));
            $task = Task::with(['stage'])->find($taskID);
            $message = "The task details are updated successfully.";
            $returnUrl =route('projecttask.list');
            return response()->json([
                'status'        =>  true,
                'response_code' =>  200,
                'message'       =>  $message,
                'data'          =>  ['task'=>$task,'redirect_url'=>$returnUrl]
            ], 200);
                return redirect($returnUrl)->with('success', __('The task details are updated successfully.'));
       
    }

    public function taskOrderUpdate(Request $request, $projectID)
    {
        $currentWorkspace = getActiveWorkSpace();

        if (isset($request->sort)) {
            foreach ($request->sort as $index => $taskID) {
                $task        = Task::find($taskID);
                $task->order = $index;
                $task->save();
            }
        }

        if ($request->new_status != $request->old_status) {
            $new_status   = Stage::where('name',$request->new_status)->first();
            $old_status   = Stage::where('name',$request->old_status)->first();
            $user         = Auth::user();
            $task         = Task::find($request->id);
            $task->status = $request->new_status;
            $task->save();

            $name = $user->name;
            $id   = $user->id;

            ActivityLog::create(
                [
                    'user_id' => $id,
                    'user_type' => get_class($user),
                    'project_id' => $projectID,
                    'log_type' => 'Move',
                    'remark' => json_encode(
                        [
                            'title' => $task->title,
                            'old_status' => $old_status->name,
                            'new_status' => $new_status->name,
                        ]
                    ),
                ]
            );

            event(new UpdateTaskStage($request, $task));

            return $task->toJson();
        }
    }
    public function commentStoreFile(Request $request, $taskID, $clientID = '')
    {
        $currentWorkspace = getActiveWorkSpace();
        $fileName = $taskID . time() . "_" . $request->file->getClientOriginalName();

        $upload = upload_file($request, 'file', $fileName, 'tasks', []);

        if ($upload['flag'] == 1) {
            $post['task_id']   = $taskID;
            $post['file']      = $upload['url'];
            $post['name']      = $request->file->getClientOriginalName();
            $post['extension']      = $request->file->getClientOriginalExtension();
            $post['file_size']      = $request->file->getSize();
            if ($clientID) {
                $post['created_by'] = $clientID;
                $post['user_type']  = 'Client';
            } else {
                $post['created_by'] = Auth::user()->id;
                $post['user_type']  = 'User';
            }
            $TaskFile            = TaskFile::create($post);
            $user                = $TaskFile->user;
            $TaskFile->deleteUrl = '';
            if (empty($clientID)) {
                $TaskFile->deleteUrl = route(
                    'comment.destroy.file',
                    [
                        $currentWorkspace,
                        $taskID,
                        $TaskFile->id,
                    ]
                );
            }

            return $TaskFile->toJson();
        } else {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => $upload['msg'],
                ],
                401
            );
        }
    }

    public function subTaskStore(Request $request, $taskID, $clientID = '')
    {
        $post             = [];
        $post['task_id']  = $taskID;
        $post['name']     = $request->name;
        $post['due_date'] = $request->due_date;
        $post['status']   = 0;

        if ($clientID) {
            $post['created_by'] = $clientID;
            $post['user_type']  = 'Client';
        } else {
            $post['created_by'] = Auth::user()->id;
            $post['user_type']  = 'User';
        }
        $subtask = SubTask::create($post);
        if ($subtask->user_type == 'Client') {
            $user = $subtask->client;
        } else {
            $user = $subtask->user;
        }
        $subtask->updateUrl = route(
            'subtask.update',
            [
                $subtask->id,
            ]
        );
        $subtask->deleteUrl = route(
            'subtask.destroy',
            [
                $subtask->id,
            ]
        );

        return $subtask->toJson();
    }
    public function subTaskDestroy($subtaskID)
    {
        $subtask = SubTask::find($subtaskID);
        $subtask->delete();

        return "true";
    }
    public function subTaskUpdate($subtaskID)
    {
        $subtask         = SubTask::find($subtaskID);
        $subtask->status = (int)!$subtask->status;
        $subtask->save();
        return $subtask->toJson();
    }


    public function commentDestroyFile(Request $request, $taskID, $fileID)
    {
        $commentFile = TaskFile::find($fileID);
        delete_file($commentFile->file);
        $commentFile->delete();

        return "true";
    }
    public function commentStore(Request $request, $taskID, $clientID = '')
    {
        $task    = Task::find($taskID);

        $post             = [];
        $post['task_id']  = $taskID;
        $post['comment']  = $request->comment;
        if ($clientID) {
            $post['created_by'] = $clientID;
            $post['user_type']  = 'Client';
        } else {
            $post['created_by'] = Auth::user()->id;
            $post['user_type']  = 'User';
        }
        $comment = Comment::create($post);
        if ($comment->user_type == 'Client') {
            $user = $comment->client;
        } else {
            $user = $comment->user;
        }

        if (empty($clientID)) {
            $comment->deleteUrl = route(
                'comment.destroy',
                [
                    $projectID,
                    $taskID,
                    $comment->id,
                ]
            );
        }

        event(new CreateTaskComment($request, $comment));

        return $comment->toJson();
    }
    public function commentDestroy(Request $request, $taskID, $commentID)
    {

        $comment = Comment::find($commentID);

        event(new DestroyTaskComment($request, $comment));
        $comment->delete();

        return "true";
    }
   public function taskDestroy(Request $request, $taskID)
{
    // Get task details before deletion for logging
    $task = Task::find($taskID);
    $taskTitle = $task ? $task->title : 'Unknown Task';
    event(new DestroyTask($taskID));
    Comment::where('task_id', '=', $taskID)->delete();
    SubTask::where('task_id', '=', $taskID)->delete();
    $TaskFiles = TaskFile::where('task_id', '=', $taskID)->get();
    foreach ($TaskFiles as $TaskFile) {
        delete_file($TaskFile->file);
        $TaskFile->delete();
    }
    if (module_is_active('CustomField')) {
        $customFields = \Workdo\CustomField\Entities\CustomField::where('module', 'taskly')->where('sub_module', 'tasks')->get();
        foreach ($customFields as $customField) {
            $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $taskID)->where('field_id', $customField->id)->first();
            if (!empty($value)) {
                $value->delete();
            }
        }
    }
    Task::where('id', $taskID)->update([
        'deleted_at' => date('Y-m-d H:i:s'),
        'delete_rating' => $request->delete_rating ?? 0,
        'delete_feedback' => $request->delete_feedback ?? null
    ]);
    $message =__('The task has been deleted.');
    
    return response()->json([
        'status' => true,
        'response_code' => 200,
        'message' => $message,
        'data' => ['delete_id'=>$taskID]
    ], 200);
}

    public function bugReport($project_id)
    {
        if (\Auth::user()->isAbleTo('bug manage')) {
            $currentWorkspace = getActiveWorkSpace();

            $objUser = Auth::user();
            if ($objUser->hasRole('client')) {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $project_id)->first();
            } else {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $project_id)->first();
            }

            $stages = $statusClass = [];

            $stages = BugStage::where('workspace_id', '=', $currentWorkspace)->where('created_by',creatorId())->orderBy('order')->get();

            foreach ($stages as &$status) {
                $statusClass[] = 'task-list-' . str_replace(' ', '_', $status->id);
                $bug           = BugReport::where('project_id', '=', $project_id);
                if ($objUser->type != 'client') {
                    if (!Auth::user()->hasRole('client') && !Auth::user()->hasRole('company')) {
                        $bug->where('assign_to', '=', $objUser->email);
                    }
                }
                $bug->orderBy('order');

                $status['bugs'] = $bug->where('status', '=', $status->id)->with('user')->get();
            }
            return view('taskly::projects.bug_report', compact('currentWorkspace', 'project', 'stages', 'statusClass'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function bugReportCreate($project_id)
    {
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();

        if (module_is_active('CustomField')) {
            $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id', getActiveWorkSpace())->where('module', '=', 'taskly')->where('sub_module', 'bugs')->get();
        } else {
            $customFields = null;
        }
        if ($objUser->hasRole('client')) {
            $project = Project::where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $project_id)->first();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $project_id)->first();
        }
        $arrStatus = BugStage::where('workspace_id', '=', $currentWorkspace)->where('created_by',creatorId())->orderBy('order')->pluck('name', 'id')->all();
        $users     = User::select('users.*')->join('user_projects', 'user_projects.user_id', '=', 'users.id')->where('project_id', '=', $project_id)->get();

        return view('taskly::projects.bug_report_create', compact('currentWorkspace', 'project', 'users', 'arrStatus', 'customFields'));
    }

    public function bugReportStore(Request $request, $project_id)
    {
        $request->validate(
            [
                'title' => 'required',
                'priority' => 'required',
                'assign_to' => 'required',
                'status' => 'required',
                'description' => 'required',
            ]
        );
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();

        if ($objUser->hasRole('client')) {
            $project = Project::where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $project_id)->first();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $project_id)->first();
        }

        if ($project) {
            $post               = $request->all();
            $post['project_id'] = $project_id;
            $bug                = BugReport::create($post);

            if (module_is_active('CustomField')) {
                \Workdo\CustomField\Entities\CustomField::saveData($bug, $request->customField);
            }

            ActivityLog::create(
                [
                    'user_id' => $objUser->id,
                    'user_type' => get_class($objUser),
                    'project_id' => $project_id,
                    'log_type' => 'Create Bug',
                    'remark' => json_encode(['title' => $bug->title]),
                ]
            );
            event(new CreateBug($request, $bug));

            return redirect()->back()->with('success', __('Bug Create Successfully!'));
        } else {
            return redirect()->back()->with('error', __("You can't Add Bug!"));
        }
    }

    public function bugReportOrderUpdate(Request $request, $project_id)
    {
        if (isset($request->sort)) {
            foreach ($request->sort as $index => $taskID) {
                $bug        = BugReport::find($taskID);
                $bug->order = $index;
                $bug->save();
            }
        }
        if ($request->new_status != $request->old_status) {
            $new_status  = BugStage::find($request->new_status);
            $old_status  = BugStage::find($request->old_status);
            $user        = Auth::user();
            $bug         = BugReport::find($request->id);
            $bug->status = $request->new_status;
            $bug->save();

            $name = $user->name;
            $id   = $user->id;

            event(new UpdateBugStage($request, $bug));

            ActivityLog::create(
                [
                    'user_id' => $id,
                    'user_type' => get_class($user),
                    'project_id' => $project_id,
                    'log_type' => 'Move Bug',
                    'remark' => json_encode(
                        [
                            'title' => $bug->title,
                            'old_status' => $old_status->name,
                            'new_status' => $new_status->name,
                        ]
                    ),
                ]
            );

            return $bug->toJson();
        }
    }

    public function bugReportEdit($project_id, $bug_id)
    {
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();

        if ($objUser->hasRole('client')) {
            $project = Project::where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $project_id)->first();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $project_id)->first();
        }
        $users     = User::select('users.*')->join('user_projects', 'user_projects.user_id', '=', 'users.id')->where('project_id', '=', $project_id)->get();
        $bug       = BugReport::find($bug_id);
        if (module_is_active('CustomField')) {
            $bug->customField = \Workdo\CustomField\Entities\CustomField::getData($bug, 'taskly', 'bugs');
            $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'taskly')->where('sub_module', 'bugs')->get();
        } else {
            $customFields = null;
        }
        $arrStatus = BugStage::where('workspace_id', '=', $currentWorkspace)->where('created_by',creatorId())->orderBy('order')->pluck('name', 'id')->all();

        return view('taskly::projects.bug_report_edit', compact('currentWorkspace', 'project', 'users', 'bug', 'arrStatus', 'customFields'));
    }

    public function bugReportUpdate(Request $request, $project_id, $bug_id)
    {

        $request->validate(
            [
                'title' => 'required',
                'priority' => 'required',
                'assign_to' => 'required',
                'status' => 'required',
            ]
        );
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();

        if ($objUser->hasRole('client')) {
            $project = Project::where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $project_id)->first();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $project_id)->first();
        }
        if ($project) {
            $post = $request->all();
            $bug  = BugReport::find($bug_id);
            $bug->update($post);

            if (module_is_active('CustomField')) {
                \Workdo\CustomField\Entities\CustomField::saveData($bug, $request->customField);
            }
            event(new UpdateBug($request, $bug));
            return redirect()->back()->with('success', __('Bug Updated Successfully!'));
        } else {
            return redirect()->back()->with('error', __("You can't Edit Bug!"));
        }
    }

    public function bugReportDestroy($project_id, $bug_id)
    {

        $objUser = Auth::user();
        BugComment::where('bug_id', '=', $bug_id)->delete();
        $bugfiles = BugFile::where('bug_id', '=', $bug_id)->get();
        foreach ($bugfiles as $bugfile) {
            delete_file($bugfile->file);
            $bugfile->delete();
        }
        if (module_is_active('CustomField')) {
            $customFields = \Workdo\CustomField\Entities\CustomField::where('module', 'taskly')->where('sub_module', 'bugs')->get();
            foreach ($customFields as $customField) {
                $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $bug_id)->where('field_id', $customField->id)->first();
                if (!empty($value)) {
                    $value->delete();
                }
            }
        }
        $bug = BugReport::where('id', $bug_id)->first();

        event(new DestroyBug($bug));

        $bug     = BugReport::where('id', $bug_id)->delete();

        return redirect()->back()->with('success', __('Bug Deleted Successfully!'));
    }

    public function bugReportShow($project_id, $bug_id)
    {
        $currentWorkspace = getActiveWorkSpace();
        $bug              = BugReport::find($bug_id);
        $objUser          = Auth::user();

        $clientID = '';
        if ($objUser->hasRole('client')) {
            $clientID = $objUser->id;
        }


        return view('taskly::projects.bug_report_show', compact('currentWorkspace', 'bug', 'clientID'));
    }

    public function bugCommentStore(Request $request, $project_id, $bugID, $clientID = '')
    {
        $post             = [];
        $post['bug_id']   = $bugID;
        $post['comment']  = $request->comment;
        if ($clientID) {
            $post['created_by'] = $clientID;
            $post['user_type']  = 'Client';
        } else {
            $post['created_by'] = Auth::user()->id;
            $post['user_type']  = 'User';
        }
        $comment = BugComment::create($post);
        if ($comment->user_type == 'Client') {
            $user = $comment->client;
        } else {
            $user = $comment->user;
        }
        if (empty($clientID)) {
            $comment->deleteUrl = route(
                'bug.comment.destroy',
                [
                    $project_id,
                    $bugID,
                    $comment->id,
                ]
            );
        }

        return $comment->toJson();
    }

    public function bugCommentDestroy(Request $request, $project_id, $bug_id, $comment_id)
    {

        $comment = BugComment::find($comment_id);
        $comment->delete();
        return "true";
    }

    public function bugStoreFile(Request $request, $project_id, $bug_id, $clientID = '')
    {

        if($request->file){
            $fileName =  $request->file->getClientOriginalName();
            $upload = upload_file($request, 'file', $fileName, 'tasks');
            if ($upload['flag'] == 1) {
                $post['bug_id']    = $bug_id;
                $post['file']      = $upload['url'];
                $post['name']      = $fileName;
                $post['extension'] = "." . $request->file->getClientOriginalExtension();
                $post['file_size'] = round(($request->file->getSize() / 1024) / 1024, 2) . ' MB';

                if ($clientID) {
                    $post['created_by'] = $clientID;
                    $post['user_type']  = 'Client';
                } else {
                    $post['created_by'] = Auth::user()->id;
                    $post['user_type']  = 'User';
                }
                $TaskFile            = BugFile::create($post);
                $user                = $TaskFile->user;
                $TaskFile->deleteUrl = '';
                if (empty($clientID)) {
                    $TaskFile->deleteUrl = route(
                        'bug.comment.destroy.file',
                        [
                            $project_id,
                            $bug_id,
                            $TaskFile->id,
                        ]
                    );
                }

                return $TaskFile->toJson();
            } else {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => $upload['msg'],
                    ],
                    401
                );
            }
        }else{
            return response()->json(
                [
                    'is_success' => false,
                    'error' => 'Please select file.',
                ],
                401
            );

        }
    }

    public function bugDestroyFile(Request $request, $project_id, $bug_id, $file_id)
    {
        $commentFile = BugFile::find($file_id);
        delete_file($commentFile->file);
        $commentFile->delete();

        return "true";
    }

    public function tracker($id)
    {
        if (Auth::user()->isAbleTo('time tracker manage')) {
            $currentWorkspace = getActiveWorkSpace();
            $treckers = TimeTracker::where('project_id', $id)->get();

            return view('taskly::time_trackers.index', compact('currentWorkspace', 'treckers', 'id'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function fileUpload($id, Request $request)
    {
        $project = Project::find($id);
        $file_name = $request->file->getClientOriginalName();

        $upload = upload_file($request, 'file', $file_name, 'projects', []);


        if ($upload['flag'] == 1) {
            $file                 = ProjectFile::create(
                [
                    'project_id' => $project->id,
                    'file_name' => $file_name,
                    'file_path' => $upload['url'],
                ]
            );
            $return               = [];
            $return['is_success'] = true;
            $return['download']   = get_file($upload['url']);
            $return['delete']     = route(
                'projects.file.delete',
                [

                    $project->id,
                    $file->id,
                ]
            );

            event(new ProjectUploadFiles($request, $upload, $project));

            ActivityLog::create(
                [
                    'user_id' => Auth::user()->id,
                    'user_type' => get_class(Auth::user()),
                    'project_id' => $project->id,
                    'log_type' => 'Upload File',
                    'remark' => json_encode(['file_name' => $file_name]),
                ]
            );
            return response()->json($return);
        } else {

            return response()->json(
                [
                    'is_success' => false,
                    'error' => $upload['msg'],
                ],
                401
            );
        }
    }

    public function fileDownload($id, $file_id)
    {
        $project = Project::find($id);
        $file = ProjectFile::find($file_id);
        if ($file) {
            $filename  = $file->file_name;
            $file_path = get_base_file($file->file_path);
            return \Response::download($file_path);
        } else {
            return redirect()->back()->with('error', __('File is not exist.'));
        }
    }

    public function fileDelete($id, $file_id)
    {
        $project = Project::find($id);

        $file = ProjectFile::find($file_id);
        if ($file) {
            delete_file($file->file_path);
            $file->delete();

            return response()->json(['is_success' => true], 200);
        } else {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('File is not exist.'),
                ],
                200
            );
        }
    }
    public function userDelete($project_id, $user_id)
    {
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();
        $project          = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $project_id)->first();
        if (count($project->user_tasks($user_id)) == 0) {
            UserProject::where('user_id', '=', $user_id)->where('project_id', '=', $project->id)->delete();
            return redirect()->back()->with('success', __('User Deleted Successfully!'));
        } else {
            return redirect()->back()->with('warning', __('Please Remove User From Tasks!'));
        }
    }

    public function clientDelete($project_id, $client_id)
    {

        if (Auth::user()->hasRole('company')) {
            ClientProject::where('client_id', $client_id)->where('project_id', $project_id)->delete();
            return redirect()->back()->with('success', __('Client Deleted Successfully!'));
        } else {
            return redirect()->back()->with('warning', __('Please Remove Client From Tasks!'));
        }
    }


    public function vendorDelete($project_id, $vender_id)
    {
        if (Auth::user()->hasRole('company')) {
            VenderProject::where('vender_id', $vender_id)->where('project_id', $project_id)->delete();
            return redirect()->back()->with('success', __('Vendor Deleted Successfully!'));
        } else {
            return redirect()->back()->with('warning', __('Please Remove Vendor From Tasks!'));
        }
    }


    public function List(ProjectDatatable $dataTable)
    {
        if (Auth::user()->isAbleTo('project manage')) {

            return $dataTable->render('taskly::projects.list');
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function TaskList(ProjectTaskDatatable $dataTable)
    {
        if (\Auth::user()->isAbleTo('task manage')) {
            $currentWorkspace = getActiveWorkSpace();
            $objUser = Auth::user();
            $stages = Stage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->get();
            $users = User::select('users.*')->get();
           
            if($objUser->hasRole('company') || $objUser->hasRole('Manager All Access') || $objUser->hasRole('hr') )
            {
                    $competeTask = Task::where('status','Done')->where('is_missed',0)->where('status',"!=","")->where('workspace', getActiveWorkSpace())->where('deleted_at',NULL)->count();
                    $pendingTask = Task::whereNotIn('status',['Done'])->where('is_missed',0)->where('status',"!=","")->where('workspace', getActiveWorkSpace())->where('deleted_at',NULL)->count();
                     $overdueTask = Task::whereNotIn('status', [''])->where('is_missed',0)->where('status',"!=","")->where('workspace', getActiveWorkSpace())
                           ->where('due_date', '<', now())->where('deleted_at',NULL)
                           ->count();
            }else
            { 
                $email = $objUser->email;
                    $competeTask = Task::where('status','Done')->where('is_missed',0)->where('status',"!=","")->where('workspace', getActiveWorkSpace())->where(function ($query) use ($email) {
                                $query->where('assignor', 'like', "%$email%")
                                    ->orWhere('assign_to', 'like', "%$email%");
                            })->where('deleted_at',NULL)->count();
                    $pendingTask = Task::whereNotIn('status',['Done'])->where('is_missed',0)->where('status',"!=","")->where('workspace', getActiveWorkSpace())->where(function ($query) use ($email) {
                                   $query->where('assignor', 'like', "%$email%")
                                        ->orWhere('assign_to', 'like', "%$email%");
                                    })->where('deleted_at',NULL)->count();
                       $overdueTask = Task::whereNotIn('status', [''])->where('is_missed',0)->where('status',"!=","")->where('workspace', getActiveWorkSpace())
                                    ->where('due_date', '<', now())
                                    ->where(function ($query) use ($email) {
                                        $query->where('assignor', 'like', "%$email%")
                                              ->orWhere('assign_to', 'like', "%$email%");
                                    })->where('deleted_at',NULL)->count();
            }
                // Calculate total tasks
        $totalTask = $competeTask + $pendingTask;
            $priority = collect([
                ['value'=>"urgent", 'color' => 'danger'],
                ['value'=>"Take your time", 'color' => 'warning'],
                ['value'=>"normal", 'color' => 'success']
            ]);
            $users = User::select('users.*')->get();
         
            return $dataTable->render('taskly::projects.tasklist',compact('currentWorkspace','stages','users','competeTask','pendingTask','overdueTask','totalTask','priority'));
        } else { 
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function getTodayCompletedTasks(Request $request)
    {
        $currentWorkspace = getActiveWorkSpace();
        $objUser = Auth::user();
        $today = now()->format('Y-m-d');
        
        // Base query for today's completed tasks
        // If completion_date doesn't exist, we'll use updated_at as fallback
        $query = Task::where('status', 'Done')->where('is_missed',0)
            ->where('workspace', $currentWorkspace)
            ->where('deleted_at', NULL)
            ->where(function($q) use ($today) {
                $q->whereDate('completion_date', $today)
                  ->orWhere(function($subQ) use ($today) {
                      $subQ->whereNull('completion_date')
                           ->whereDate('updated_at', $today);
                  });
            });
            
        // Apply assignee filter if provided
        if ($request->has('assignee_name') && !empty($request->input('assignee_name'))) {
            $assigneeEmails = $request->input('assignee_name');
            $query->where(function ($q) use ($assigneeEmails) {
                foreach ($assigneeEmails as $email) {
                    $q->orWhere('assign_to', 'like', "%$email%");
                }
            });
        }
        
        // Apply assignor filter if provided
        if ($request->has('assignor_name') && !empty($request->input('assignor_name'))) {
            $assignorEmails = $request->input('assignor_name');
            $query->where(function ($q) use ($assignorEmails) {
                foreach ($assignorEmails as $email) {
                    $q->orWhere('assignor', 'like', "%$email%");
                }
            });
        }
        
        // Apply other filters if needed
        if ($request->has('status_name') && !empty($request->input('status_name'))) {
            $statusName = $request->input('status_name');
            $query->where('status', 'like', "%$statusName%");
        }
         if ($request->has('priority') && !empty($request->input('priority'))) {
            $priority = $request->input('priority');
            $query->where('priority', 'like', "%$priority%");
        }
        
        
        if ($request->has('group_name') && !empty($request->input('group_name'))) {
            $groupName = $request->input('group_name');
            $query->where('group', 'like', "%$groupName%");
        }
        
        if ($request->has('task_name') && !empty($request->input('task_name'))) {
            $taskName = $request->input('task_name');
            $query->where('title', 'like', "%$taskName%");
        }
        
        // If user doesn't have admin roles, filter by their own tasks
        if (!$objUser->hasRole('company') && !$objUser->hasRole('Manager All Access') && !$objUser->hasRole('hr')) {
            $email = $objUser->email;
            $query->where(function ($q) use ($email) {
                $q->where('assignor', 'like', "%$email%")
                  ->orWhere('assign_to', 'like', "%$email%");
            });
        }
        
        $todayCompletedCount = $query->count();
        
        return response()->json([
            'today_completed_count' => $todayCompletedCount,
            'date' => $today
        ]);
    }

    public function getUrgentETCData(Request $request)
    {
        $currentWorkspace = getActiveWorkSpace();
        $objUser = Auth::user();
        
        // Base query for urgent status tasks
        $query = Task::where('status', 'Urgent')->where('is_missed',0)
            ->where('workspace', $currentWorkspace)
            ->where('deleted_at', NULL);
            
        // Apply assignee filter if provided
        if ($request->has('assignee_name') && !empty($request->input('assignee_name'))) {
            $assigneeEmails = $request->input('assignee_name');
            $query->where(function ($q) use ($assigneeEmails) {
                foreach ($assigneeEmails as $email) {
                    $q->orWhere('assign_to', 'like', "%$email%");
                }
            });
        }
        
        // Apply assignor filter if provided
        if ($request->has('assignor_name') && !empty($request->input('assignor_name'))) {
            $assignorEmails = $request->input('assignor_name');
            $query->where(function ($q) use ($assignorEmails) {
                foreach ($assignorEmails as $email) {
                    $q->orWhere('assignor', 'like', "%$email%");
                }
            });
        }
        
        // Apply status filter if provided
        if ($request->has('status_name') && !empty($request->input('status_name'))) {
            $statusName = $request->input('status_name');
            $query->where('status', 'like', "%$statusName%");
        }
          if ($request->has('priority') && !empty($request->input('priority'))) {
            $priority = $request->input('priority');
            $query->where('priority', 'like', "%$priority%");
        }
        
        // Apply group filter if provided
        if ($request->has('group_name') && !empty($request->input('group_name'))) {
            $groupName = $request->input('group_name');
            $query->where('group', 'like', "%$groupName%");
        }
        
        // Apply task name filter if provided
        if ($request->has('task_name') && !empty($request->input('task_name'))) {
            $taskName = $request->input('task_name');
            $query->where('title', 'like', "%$taskName%");
        }
        
        // If user doesn't have admin roles, filter by their own tasks
        if (!$objUser->hasRole('company') && !$objUser->hasRole('Manager All Access') && !$objUser->hasRole('hr')) {
            $email = $objUser->email;
            $query->where(function ($q) use ($email) {
                $q->where('assignor', 'like', "%$email%")
                  ->orWhere('assign_to', 'like', "%$email%");
            });
        }
        
        // Calculate total ETC hours for urgent tasks
        $urgentTasks = $query->get();
        $totalETCMinutes = 0;
        
        foreach ($urgentTasks as $task) {
            if (!empty($task->eta_time)) {
                // Check if eta_time contains colon (hours:minutes format)
                if (strpos($task->eta_time, ':') !== false) {
                    // Convert eta_time from "hours:minutes" to minutes and add to total
                    $etcParts = explode(':', $task->eta_time);
                    if (count($etcParts) >= 2) {
                        $hours = (int)$etcParts[0];
                        $minutes = (int)$etcParts[1];
                        $totalETCMinutes += ($hours * 60) + $minutes;
                    }
                } else {
                    // eta_time is just minutes
                    $totalETCMinutes += (int)$task->eta_time;
                }
            }
        }
        
        // Convert minutes to hours (round up to show at least 1 hour if there are minutes)
        $totalETCHours = $totalETCMinutes > 0 ? max(1, round($totalETCMinutes / 60)) : 0;
        
        return response()->json([
            'urgent_etc_hours' => $totalETCHours,
            'urgent_task_count' => $urgentTasks->count()
        ]);
    }

    public function BugList(ProjectBugDatatable $dataTable,$project_id)
    {
        if (\Auth::user()->isAbleTo('bug manage'))
        {
            $currentWorkspace = getActiveWorkSpace();

            $objUser = Auth::user();
            if ($objUser->hasRole('client')) {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $project_id)->first();
            } else {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $project_id)->first();
            }

            return $dataTable->render('taskly::projects.bug_report_list',compact('currentWorkspace', 'project'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function copyproject($id)
    {
        if (Auth::user()->isAbleTo('project create')) {
            $project = Project::find($id);
            return view('taskly::projects.copy', compact('project'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function copyprojectstore(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('project create')) {
            $project                          = Project::find($id);

            $duplicate                          = new Project();
            $duplicate['name']                  = $project->name;
            $duplicate['status']                = $project->status;
            $duplicate['image']                 = $project->image;
            $duplicate['description']           = $project->description;
            $duplicate['start_date']            = $project->start_date;
            $duplicate['end_date']              = $project->end_date;
            $duplicate['is_active']             = $project->is_active;
            $duplicate['currency']              = $project->currency;
            $duplicate['project_progress']      = $project->project_progress;
            $duplicate['progress']              = $project->progress;
            $duplicate['task_progress']         = $project->task_progress;
            $duplicate['tags']                  = $project->tags;
            $duplicate['estimated_hrs']         = $project->estimated_hrs;
            $duplicate['workspace']             = getActiveWorkSpace();
            $duplicate['created_by']            = creatorId();
            $duplicate->save();



            if (isset($request->user) && in_array("user", $request->user)) {
                $users = UserProject::where('project_id', $project->id)->get();
                foreach ($users as $user) {
                    $users = new UserProject();
                    $users['user_id'] = $user->user_id;
                    $users['project_id'] = $duplicate->id;
                    $users->save();
                }
            } else {
                $objUser = Auth::user();
                $users              = new UserProject();
                $users['user_id']   = $objUser->id;
                $users['project_id'] = $duplicate->id;
                $users->save();
            }

            if (isset($request->client) && in_array("client", $request->client)) {
                $clients = ClientProject::where('project_id', $project->id)->get();
                foreach ($clients as $client) {
                    $clients = new ClientProject();
                    $clients['client_id'] = $client->client_id;
                    $clients['project_id'] = $duplicate->id;
                    $clients->save();
                }
            }

            if (isset($request->task) && in_array("task", $request->task)) {
                $tasks = Task::where('project_id', $project->id)->where('is_missed',0)->get();
                foreach ($tasks as $task) {
                    $project_task                   = new Task();
                    $project_task['title']          = $task->title;
                    $project_task['priority']       = $task->priority;
                    $project_task['project_id']     = $duplicate->id;
                    $project_task['description']    = $task->description;
                    $project_task['start_date']     = $task->start_date;
                    $project_task['due_date']       = $task->due_date;
                    $project_task['milestone_id']   = $task->milestone_id;
                    $project_task['status']         = $task->status;
                    $project_task['assign_to']      = $task->assign_to;
                    $project_task['workspace']      = getActiveWorkSpace();
                    $project_task->save();

                    if (in_array("sub_task", $request->task)) {
                        $sub_tasks = SubTask::where('task_id', $task->id)->get();
                        foreach ($sub_tasks as $sub_task) {
                            $subtask                = new SubTask();
                            $subtask['name']        = $sub_task->name;
                            $subtask['due_date']    = $sub_task->due_date;
                            $subtask['task_id']     = $project_task->id;
                            $subtask['user_type']   = $sub_task->user_type;
                            $subtask['created_by']  = $sub_task->created_by;
                            $subtask['status']      = $sub_task->status;
                            $subtask->save();
                        }
                    }
                    if (in_array("task_comment", $request->task)) {
                        $task_comments = Comment::where('task_id', $task->id)->get();
                        foreach ($task_comments as $task_comment) {
                            $comment                = new Comment();
                            $comment['comment']     = $task_comment->comment;
                            $comment['created_by']  = $task_comment->created_by;
                            $comment['task_id']     = $project_task->id;
                            $comment['user_type']   = $task_comment->user_type;
                            $comment->save();
                        }
                    }
                    if (in_array("task_files", $request->task)) {
                        $task_files = TaskFile::where('task_id', $task->id)->get();
                        foreach ($task_files as $task_file) {
                            $file               = new TaskFile();
                            $file['file']       = $task_file->file;
                            $file['name']       = $task_file->name;
                            $file['extension']  = $task_file->extension;
                            $file['file_size']  = $task_file->file_size;
                            $file['created_by'] = $task_file->created_by;
                            $file['task_id']    = $project_task->id;
                            $file['user_type']  = $task_file->user_type;
                            $file->save();
                        }
                    }
                }
            }

            if (isset($request->bug) && in_array("bug", $request->bug)) {
                $bugs = BugReport::where('project_id', $project->id)->get();
                foreach ($bugs as $bug) {
                    $project_bug                   = new BugReport();
                    $project_bug['title']          = $bug->title;
                    $project_bug['priority']       = $bug->priority;
                    $project_bug['description']    = $bug->description;
                    $project_bug['assign_to']      = $bug->assign_to;
                    $project_bug['project_id']     = $duplicate->id;
                    $project_bug['status']         = $bug->status;
                    $project_bug['order']          = $bug->order;
                    $project_bug->save();

                    if (in_array("bug_comment", $request->bug)) {
                        $bug_comments = BugComment::where('bug_id', $bug->id)->get();
                        foreach ($bug_comments as $bug_comment) {
                            $bugcomment                 = new BugComment();
                            $bugcomment['comment']      = $bug_comment->comment;
                            $bugcomment['created_by']   = $bug_comment->created_by;
                            $bugcomment['bug_id']       = $project_bug->id;
                            $bugcomment['user_type']    = $bug_comment->user_type;
                            $bugcomment->save();
                        }
                    }
                    if (in_array("bug_files", $request->bug)) {
                        $bug_files = BugFile::where('bug_id', $bug->id)->get();
                        foreach ($bug_files as $bug_file) {
                            $bugfile               = new BugFile();
                            $bugfile['file']       = $bug_file->file;
                            $bugfile['name']       = $bug_file->name;
                            $bugfile['extension']  = $bug_file->extension;
                            $bugfile['file_size']  = $bug_file->file_size;
                            $bugfile['created_by'] = $bug_file->created_by;
                            $bugfile['bug_id']     = $project_bug->id;
                            $bugfile['user_type']  = $bug_file->user_type;
                            $bugfile->save();
                        }
                    }
                }
            }
            if (isset($request->milestone) && in_array("milestone", $request->milestone)) {
                $milestones = Milestone::where('project_id', $project->id)->get();
                foreach ($milestones as $milestone) {
                    $post                   = new Milestone();
                    $post['project_id']     = $duplicate->id;
                    $post['title']          = $milestone->title;
                    $post['status']         = $milestone->status;
                    $post['cost']           = $milestone->cost;
                    $post['summary']        = $milestone->summary;
                    $post['progress']       = $milestone->progress;
                    $post['start_date']     = $milestone->start_date;
                    $post['end_date']       = $milestone->end_date;
                    $post->save();
                }
            }
            if (isset($request->project_file) && in_array("project_file", $request->project_file)) {
                $project_files = ProjectFile::where('project_id', $project->id)->get();
                foreach ($project_files as $project_file) {
                    $ProjectFile                = new ProjectFile();
                    $ProjectFile['project_id']  = $duplicate->id;
                    $ProjectFile['file_name']   = $project_file->file_name;
                    $ProjectFile['file_path']   = $project_file->file_path;
                    $ProjectFile->save();
                }
            }
            if (isset($request->activity) && in_array('activity', $request->activity)) {
                $where_in_array = [];
                if (isset($request->milestone) && in_array("milestone", $request->milestone)) {
                    array_push($where_in_array, "Create Milestone");
                }
                if (isset($request->task) && in_array("task", $request->task)) {
                    array_push($where_in_array, "Create Task", "Move");
                }
                if (isset($request->bug) && in_array("bug", $request->bug)) {
                    array_push($where_in_array, "Create Bug", "Move Bug");
                }
                if (isset($request->client) && in_array("client", $request->client)) {
                    array_push($where_in_array, "Share with Client");
                }
                if (isset($request->user) && in_array("user", $request->user)) {
                    array_push($where_in_array, "Invite User");
                }
                if (isset($request->project_file) && in_array("project_file", $request->project_file)) {
                    array_push($where_in_array, "Upload File");
                }
                if (count($where_in_array) > 0) {
                    $activities = ActivityLog::where('project_id', $project->id)->whereIn('log_type', $where_in_array)->get();
                    foreach ($activities as $activity) {
                        $activitylog                = new ActivityLog();
                        $activitylog['user_id']     = $activity->user_id;
                        $activitylog['user_type']   = $activity->user_type;
                        $activitylog['project_id']  = $duplicate->id;
                        $activitylog['log_type']    = $activity->log_type;
                        $activitylog['remark']      = $activity->remark;
                        $activitylog->save();
                    }
                }
            }
            return redirect()->back()->with('success', 'Project Created Successfully');
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function fileImportExport()
    {
        if (Auth::user()->isAbleTo('project import')) {
            return view('taskly::projects.import');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function fileImport(Request $request)
    {
        if (Auth::user()->isAbleTo('project import')) {
            session_start();

            $error = '';

            $html = '';

            if ($request->file->getClientOriginalName() != '') {
                $file_array = explode(".", $request->file->getClientOriginalName());

                $extension = end($file_array);
                if ($extension == 'csv') {
                    $file_data = fopen($request->file->getRealPath(), 'r');

                    $file_header = fgetcsv($file_data);
                    $html .= '<table class="table table-bordered"><tr>';

                    for ($count = 0; $count < count($file_header); $count++) {
                        $html .= '
                                <th>
                                    <select name="set_column_data" class="form-control set_column_data" data-column_number="' . $count . '">
                                        <option value="">Set Count Data</option>
                                        <option value="name">Name</option>
                                        <option value="status">Status</option>
                                        <option value="description">Description</option>
                                        <option value="start_date">Start Date</option>
                                        <option value="end_date">End Date</option>
                                        <option value="budget">Budget</option>
                                    </select>
                                </th>
                                ';
                    }
                    $html .= '</tr>';
                    $limit = 0;
                    while (($row = fgetcsv($file_data)) !== false) {
                        $limit++;

                        $html .= '<tr>';

                        for ($count = 0; $count < count($row); $count++) {
                            $html .= '<td>' . $row[$count] . '</td>';
                        }

                        $html .= '</tr>';

                        $temp_data[] = $row;
                    }
                    $_SESSION['file_data'] = $temp_data;
                } else {
                    $error = 'Only <b>.csv</b> file allowed';
                }
            } else {

                $error = 'Please Select CSV File';
            }
            $output = array(
                'error' => $error,
                'output' => $html,
            );

            return json_encode($output);
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function fileImportModal()
    {
        if (Auth::user()->isAbleTo('project import')) {
            return view('taskly::projects.import_modal');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function projectImportdata(Request $request)
    {
        if (Auth::user()->isAbleTo('project import')) {
            session_start();
            $html = '<h3 class="text-danger text-center">Below data is not inserted</h3></br>';
            $flag = 0;
            $html .= '<table class="table table-bordered"><tr>';
            $file_data = $_SESSION['file_data'];

            unset($_SESSION['file_data']);

            $user = Auth::user();


            foreach ($file_data as $row) {
                $projects = Project::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->Where('name', 'like', $row[$request->name])->get();

                if ($projects->isEmpty()) {

                    try {
                        $project = Project::create([
                            'name' => $row[$request->name],
                            'status' => $row[$request->status],
                            'description' => $row[$request->description],
                            'start_date' => $row[$request->start_date],
                            'end_date' => $row[$request->end_date],
                            'budget' => $row[$request->budget],
                            'created_by' => creatorId(),
                            'workspace' => getActiveWorkSpace(),
                        ]);
                        UserProject::create([
                            'user_id' => creatorId(),
                            'project_id' => $project->id,
                            'is_active' => 1,
                        ]);
                    } catch (\Exception $e) {
                        $flag = 1;
                        $html .= '<tr>';

                        $html .= '<td>' . $row[$request->name] . '</td>';
                        $html .= '<td>' . $row[$request->status] . '</td>';
                        $html .= '<td>' . $row[$request->description] . '</td>';
                        $html .= '<td>' . $row[$request->start_date] . '</td>';
                        $html .= '<td>' . $row[$request->end_date] . '</td>';
                        $html .= '<td>' . $row[$request->budget] . '</td>';

                        $html .= '</tr>';
                    }
                } else {
                    $flag = 1;
                    $html .= '<tr>';

                    $html .= '<td>' . $row[$request->name] . '</td>';
                    $html .= '<td>' . $row[$request->status] . '</td>';
                    $html .= '<td>' . $row[$request->description] . '</td>';
                    $html .= '<td>' . $row[$request->start_date] . '</td>';
                    $html .= '<td>' . $row[$request->end_date] . '</td>';
                    $html .= '<td>' . $row[$request->budget] . '</td>';

                    $html .= '</tr>';
                }
            }

            $html .= '
                            </table>
                            <br />
                            ';
            if ($flag == 1) {

                return response()->json([
                    'html' => true,
                    'response' => $html,
                ]);
            } else {
                return response()->json([
                    'html' => false,
                    'response' => 'Data Imported Successfully',
                ]);
            }
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
    public function CopylinkSetting($id)
    {
        if (Auth::user()->isAbleTo('project setting')) {
            $project = Project::find($id);
            return view('taskly::projects.copylink_setting', compact('project'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function CopylinkSettingSave(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('project setting')) {
            $data = [];
            $data['basic_details']  = isset($request->basic_details) ? 'on' : 'off';
            $data['member']  = isset($request->member) ? 'on' : 'off';
            $data['client']  = isset($request->client) ? 'on' : 'off';
            $data['vendor']  = isset($request->vendor) ? 'on' : 'off';
            $data['milestone']  = isset($request->milestone) ? 'on' : 'off';
            $data['activity']  = isset($request->activity) ? 'on' : 'off';
            $data['attachment']  = isset($request->attachment) ? 'on' : 'off';
            $data['bug_report']  = isset($request->bug_report) ? 'on' : 'off';
            $data['task']  = isset($request->task) ? 'on' : 'off';
            $data['invoice']  = isset($request->invoice) ? 'on' : 'off';
            $data['bill']  = isset($request->bill) ? 'on' : 'off';
            $data['documents']  = isset($request->documents) ? 'on' : 'off';
            $data['timesheet']  = isset($request->timesheet) ? 'on' : 'off';
            $data['progress']  = isset($request->progress) ? 'on' : 'off';
            $data['retainer']  = isset($request->retainer) ? 'on' : 'off';
            $data['proposal']  = isset($request->proposal) ? 'on' : 'off';
            $data['password_protected']  = isset($request->password_protected) ? 'on' : 'off';
            $data['procurement']  = isset($request->procurement) ? 'on' : 'off';

            $project = Project::find($id);
            if (isset($request->password_protected) && $request->password_protected == 'on') {
                $project->password = base64_encode($request->password);
            } else {
                $project->password = null;
            }
            $project->copylinksetting = (count($data) > 0) ? json_encode($data) : null;
            $project->save();

            return redirect()->back()->with('success', __('Copy Link Setting Save Successfully!'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
    public function PasswordCheck(Request $request, $id = null, $lang = null)
    {
        $id_de = Crypt::decrypt($id);
        $project = Project::find($id_de);
        if (!empty($project->copylinksetting) && json_decode($project->copylinksetting)->password_protected == 'on') {
            if (!empty($request->password) && $request->password == base64_decode($project->password)) {
                \Session::put('checked_' . $project->id, $project->id);
                return redirect()->route('project.shared.link', [$id, $lang]);
            } else {
                return redirect()->route('project.shared.link', [$id, $lang])->with('error', __('Password is wrong! Please enter valid password'));
            }
        }
    }
    public function ProjectSharedLink($id = null, $lang = null)
    {
        try {
            $id_de = Crypt::decrypt($id);
        } catch (\Throwable $th) {
            return redirect()->route('login', [$lang]);
        }
        $project = Project::find($id_de);
        $company_id = $project->created_by;
        $workspace_id = $project->workspace;
        $project_id = \Session::get('checked_' . $id_de);
        if ($lang == '') {
            $lang = !empty(company_setting('defult_language', $company_id, $workspace_id)) ? company_setting('defult_language', $company_id, $workspace_id) : 'en';
        }
        \App::setLocale($lang);

        if (!empty($project->copylinksetting) && json_decode($project->copylinksetting)->password_protected == 'on' && $project_id != $id_de) {
            return view('taskly::projects.password_check', compact('company_id', 'workspace_id', 'id', 'lang'));
        }
        if ($project) {
            $bills = [];
            if (module_is_active('Account')) {
                $bills = Bill::where('workspace', '=', getActiveWorkSpace($company_id))->where('bill_module', '=', 'taskly')->where('category_id', '=', $project->id)->get();
            }

            $documents = [];

            if (module_is_active('Documents')) {
                $documents = Document::with(['Type', 'user', 'Project'])->where('project_id', $project->id)->where('created_by',$project->created_by)->where('workspace_id', getActiveWorkSpace($company_id))->get();
            }

            //chartdata
            $chartData = $this->getProjectChart(
                [
                    'workspace_id' => $workspace_id,
                    'project_id' => $project->id,
                    'duration' => 'week',
                ]
            );
            if (date('Y-m-d') == $project->end_date || date('Y-m-d') >= $project->end_date) {
                $daysleft = 0;
            } else {
                $daysleft = round((((strtotime($project->end_date) - strtotime(date('Y-m-d'))) / 24) / 60) / 60);
            }

            $stages = $statusClass = [];

            if ($project) {
                $stages = Stage::where('workspace_id', '=', $workspace_id)->orderBy('order')->get();
                foreach ($stages as $status) {
                    $statusClass[] = 'task-list-' . str_replace(' ', '_', $status->id);
                    $task          = Task::where('project_id', '=', $id_de)->where('is_missed',0);
                    $task->orderBy('order');
                    $status['tasks'] = $task->where('status', '=', $status->id)->get();
                }
            }

            $bug_stages = BugStage::where('workspace_id', '=', $workspace_id)->orderBy('order')->get();
            foreach ($bug_stages as &$status) {
                $statusClass[] = 'task-list-' . str_replace(' ', '_', $status->id);
                $bug           = BugReport::where('project_id', '=', $id_de);
                $bug->orderBy('order');

                $status['bugs'] = $bug->where('status', '=', $status->id)->get();
            }
            $invoices = Invoice::where('workspace', '=', $workspace_id)->where('invoice_module', '=', 'taskly')->where('category_id', $project_id)->get();
            $retainers =[];
            if(module_is_active('Retainer'))
            {
                $retainers = Retainer::where('workspace', '=', $workspace_id)->where('account_type', '=', 'Projects')->where('category_id', $project->id)->get();
            }

            $proposals= Proposal::where('workspace', '=', $workspace_id)->where('account_type', '=', 'Projects')->where('category_id', $project->id)->get();

            return view('taskly::projects.sharedlink', compact('company_id', 'workspace_id', 'project', 'id', 'lang', 'chartData', 'daysleft', 'stages', 'bug_stages', 'invoices', 'bills', 'documents','retainers','proposals'));

        } else {
            return redirect()->route('project.shared.link', [$id, $lang])->with('error', __('Project not found Please check the link!'));
        }
    }
    public function ProjectLinkTaskShow($taskID)
    {
        if ($taskID) {
            $task    = Task::find($taskID);
            return view('taskly::projects.linktaskShow', compact('task', 'project'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    public function ProjectLinkbugReportShow($projectID, $bug_id)
    {
        if (!empty($projectID) && !empty($bug_id)) {
            $project = Project::find($projectID);
            $bug     = BugReport::find($bug_id);
            return view('taskly::projects.link_bug_report_show', compact('bug', 'project'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function calendar(Request $request,$id){
        $project = Project::find($id);
        $tasks    = Task::where('workspace', $project->workspace)->where('project_id' ,$id);
        $current_month_task = Task::select('title', 'start_date', 'due_date', 'created_at')->where('workspace', getActiveWorkSpace())
        ->whereRaw('MONTH(start_date) = ? AND MONTH(due_date) = ?', [date('m'), date('m')])->where('project_id' ,$id)
        ->get();
        if (!empty($request->start_date)) {
            $tasks->where('start_date', '>=', $request->start_date);
        }
        if (!empty($request->due_date)) {
            $tasks->where('due_date', '<=', $request->due_date);
        }
        $tasks = $tasks->get();
        $arrTask = [];
        foreach ($tasks as $task) {
            $arr['id']        = $task['id'];
            $arr['title']     = $task['title'];
            $arr['start']     = $task['start_date'];
            $arr['end']       = date('Y-m-d', strtotime($task['due_date'] . ' +1 day'));
            $arr['className'] = 'event-danger task-edit';
            $arr['url']       = route('tasks.edit', [$id,$task['id']]);
            $arrTask[]    = $arr;
        }
        $arrTask =  json_encode($arrTask);

        return view('taskly::projects.calendar', compact('current_month_task','arrTask' ,'id'));
    }


    public function finance(Request $request ,$id){
        $project = Project::find($id);
        $query = Proposal::where('workspace',getActiveWorkSpace())->where('account_type' ,'Projects')->where('category_id',$id);
        $proposals = $query->with('customers')->get();
        return view('taskly::projects.proposal' ,compact('project' ,'id' ,'proposals'));

    }

    public function proposal(Request $request ,$id){
        $project = Project::find($id);
        $query = Proposal::where('workspace',getActiveWorkSpace())->where('account_type' ,'Projects')->where('category_id',$id);
        $proposals = $query->with('customers')->get();

        return view('taskly::projects.proposal', compact( 'id','project','proposals'));

    }

    public function purchases(Request $request ,$id){
        $project = Project::find($id);

        $purchases = Purchase::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->with('vender', 'category', 'user')->get();

        return view('taskly::projects.purchases', compact( 'id'));
    }
     public function taskCountData(Request $request)
    {
            $objUser = Auth::user();
        
            $assignee_name = $request->assignee_name;
            $assignor_name = $request->assignor_name;
            $status_name = $request->status_name;
            $priority = $request->priority;
            $group_name = $request->group_name;
            $task_name = $request->task_name;
            $searchValue ="";
            if (request()->has('search_value') && !empty(request()->input('search_value'))) {
                $searchValue = request()->input('search_value');
            }
            $workspaceId = getActiveWorkSpace();
            if($objUser->hasRole('company') || $objUser->hasRole('Manager All Access') || $objUser->hasRole('hr') )
            {
                    $taskBaseQuery = Task::join('stages', 'stages.name', '=', 'tasks.status')
                        ->leftJoin('users as assignor_users', 'assignor_users.email', '=', 'tasks.assignor')
                        ->where('tasks.workspace', $workspaceId)->where('is_missed',0)
                        ->whereNull('tasks.deleted_at')
                        ->select('tasks.*', 'stages.name as stage_name', 'assignor_users.name as assigner_name','eta_time','etc_done')
                        ->distinct('tasks.id');
                        if (!empty($searchValue)) {
                            $taskBaseQuery->where(function ($query) use ($searchValue) {
                                // Search by assignor name
                                $query->where('assignor_users.name', 'like', "%$searchValue%")
                                    // Search by users in assign_to field (using FIND_IN_SET)
                                    ->orWhereRaw("
                                        EXISTS (
                                            SELECT 1 
                                            FROM users 
                                            WHERE FIND_IN_SET(users.email, tasks.assign_to) 
                                            AND users.name LIKE ?
                                        )", ["%$searchValue%"])
                                    // Search by task title
                                    ->orWhere('tasks.title', 'like', "%$searchValue%")
                                    // Search by group name
                                    ->orWhere('tasks.group', 'like', "%$searchValue%");
                            });
                        }

                    $completedTask = (clone $taskBaseQuery)
                        ->where('tasks.status', 'Done');
                         $pendingTask = (clone $taskBaseQuery)
                        ->where('tasks.status', '!=', 'Done')
                        ->where('tasks.status', '!=', '');
                 
                    $overdueTask = (clone $taskBaseQuery)
                        ->where('tasks.status', '!=', '')
                        ->where('tasks.status', '!=', '')
                        ->where('tasks.due_date', '<', now());
                    $totalTask = (clone $taskBaseQuery);
                     $totalETAmin = (clone $taskBaseQuery);
                      $totalATCMin = (clone $taskBaseQuery);
                  
                    if($assignor_name && count($assignor_name) ){
                        $completedTask->where('assignor', 'like', "%$assignor_name[0]%");
                        $pendingTask->where('assignor', 'like', "%$assignor_name[0]%");
                         $overdueTask->where('assignor', 'like', "%$assignor_name[0]%");
                        $totalTask->where('assignor', 'like', "%$assignor_name[0]%");
                        $totalETAmin->where('assignor', 'like', "%$assignor_name[0]%");
                        $totalATCMin->where('assignor', 'like', "%$assignor_name[0]%");

                    }
                    if($group_name && !empty($group_name) ){
                        $completedTask->where('group', 'like', "%$group_name%");
                        $pendingTask->where('group', 'like', "%$group_name%");
                         $overdueTask->where('group', 'like', "%$group_name%");
                        $totalTask->where('group', 'like', "%$group_name%");
                        $totalETAmin->where('group', 'like', "%$group_name%");
                        $totalATCMin->where('group', 'like', "%$group_name%");
                    } 
                    if($priority && !empty($priority) ){
                        $completedTask->where('priority', 'like', "%$priority%");
                        $pendingTask->where('priority', 'like', "%$priority%");
                         $overdueTask->where('priority', 'like', "%$priority%");
                        $totalTask->where('priority', 'like', "%$priority%");
                        $totalETAmin->where('priority', 'like', "%$priority%");
                        $totalATCMin->where('priority', 'like', "%$priority%");
                    }
                     if($task_name && !empty($task_name) ){
                        $completedTask->where('title', 'like', "%$task_name%");
                        $pendingTask->where('title', 'like', "%$task_name%");
                        $overdueTask->where('title', 'like', "%$task_name%");
                        $totalTask->where('title', 'like', "%$task_name%");
                        $totalETAmin->where('title', 'like', "%$task_name%");
                        $totalATCMin->where('title', 'like', "%$task_name%");
                    }
                    if($assignee_name && count($assignee_name) ){
                        $completedTask->where('assign_to', 'like', "%$assignee_name[0]%");
                        $pendingTask->where('assign_to', 'like', "%$assignee_name[0]%");
                        $overdueTask->where('assign_to', 'like', "%$assignee_name[0]%");
                        $totalTask->where('assign_to', 'like', "%$assignee_name[0]%");
                        $totalETAmin->where('assign_to', 'like', "%$assignee_name[0]%");
                        $totalATCMin->where('assign_to', 'like', "%$assignee_name[0]%");

                    }
                   
                    $completedTask = $completedTask->count();
                    $pendingTask =$pendingTask->count();
                    
                    $totalETAmin = collect($totalETAmin->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
                    
                    $totalATCMin = collect($totalATCMin->where('etc_done',">",0)->pluck('etc_done')->toArray())->sum();
                    $overdueTask = $overdueTask->count();

                    $totalTask = $totalTask->count();
            }else
            {
                      $email = $objUser->email;
                    $workspaceId = getActiveWorkSpace();
                    
                    // Base query with all common filters
                    $taskBaseQuery = Task::join('stages', 'stages.name', '=', 'tasks.status')
                        ->leftJoin('users as assignor_users', 'assignor_users.email', '=', 'tasks.assignor')
                        ->where('tasks.workspace', $workspaceId)->where('is_missed',0)
                        ->whereNull('tasks.deleted_at')
                        ->where(function ($query) use ($email) {
                            $query->whereRaw("FIND_IN_SET(?, tasks.assign_to)", [$email])
                                  ->orWhere('tasks.assignor', $email);
                        })
                        ->select('tasks.*', 'stages.name as stage_name', 'assignor_users.name as assigner_name','eta_time','etc_done')
                        ->distinct('tasks.id');
                    
                    // Total Tasks
                    $totalTask = (clone $taskBaseQuery)->count('tasks.id');
                    
                    // Completed Tasks
                    $completedTask = (clone $taskBaseQuery)
                        ->where('tasks.status', 'Done')
                        ->count('tasks.id');
                    
                    // Pending Tasks
                    $pendingTask = (clone $taskBaseQuery)
                        ->where('tasks.status', '!=', 'Done')
                        ->where('tasks.status', '!=', '')
                        ->count('tasks.id');
                    
                    // Overdue Tasks
                    $overdueTask = (clone $taskBaseQuery)
                        ->where('tasks.status', '!=', '')
                        ->where('tasks.status', '!=', '')
                        ->where('tasks.due_date', '<', now())
                        ->count('tasks.id');
                        
                     $totalETAmin = collect((clone $taskBaseQuery)->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
                     $totalATCMin = collect((clone $taskBaseQuery)->where('etc_done',">",0)->pluck('etc_done')->toArray())->sum();
            }
            
        // $totalETAHours = number_format($totalETAmin/60,2);
        $totalETAHours = round($totalETAmin/60);
        $totalATCHours = round($totalATCMin/60);

        return response()->json(
            [
                'is_success' => true,
                'data' =>['complete_count'=>$completedTask,'pending_count'=>$pendingTask,'overdue_count'=>$overdueTask,'total_count'=>$totalTask,'total_eta'=>$totalETAHours,'total_atc' => $totalATCHours]
            ],
            200
        );
    }
        public function doneTasklist(ProjectDoneTaskDatatable $dataTable)
        {
            if (\Auth::user()->isAbleTo('task manage')) {
                $currentWorkspace = getActiveWorkSpace();
                $objUser = Auth::user();
                $stages = Stage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->get();
                $users = User::select('users.*')->get();
               
                if($objUser->hasRole('company') || $objUser->hasRole('Manager All Access') || $objUser->hasRole('hr') )
                {
                        $competeTask = Task::where('status','Done')->where('is_missed',0)->whereNotNull('deleted_at')->count();
                        $pendingTask = Task::whereNotIn('status',['Done'])->where('is_missed',0)->whereNotNull('deleted_at')->count();
                        $totalETAmin = collect(Task::where('status','Done')->where('is_missed',0)->whereNotNull('deleted_at')->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
                        $totalATCMin = collect(Task::where('status','Done')->where('is_missed',0)->whereNotNull('deleted_at')->where('eta_time',">",0)->pluck('etc_done')->toArray())->sum();
                        
                        // Calculate initial average completion days for all users
                        $tasksWithCompletionDays = Task::where('status','Done')->where('is_missed',0)->whereNotNull('deleted_at')
                            ->whereNotNull('start_date')
                            ->whereNotNull('completion_date')
                            ->where('completion_date', '!=', '0000-00-00')
                            ->where('completion_date', '!=', '0000-00-00 00:00:00')
                            ->get();
                            
                        $totalCompletionDays = 0;
                        $validTasksCount = 0;
                        
                        foreach ($tasksWithCompletionDays as $task) {
                            try {
                                $startDate = \Carbon\Carbon::parse($task->start_date);
                                $completionDate = \Carbon\Carbon::parse($task->completion_date);
                                $days = $startDate->diffInDays($completionDate);
                                $totalCompletionDays += $days;
                                $validTasksCount++;
                            } catch (\Exception $e) {
                                // Skip this task if dates are invalid
                                continue;
                            }
                        }
                        
                        $avgCompletionDays = $validTasksCount > 0 ? round($totalCompletionDays / $validTasksCount, 1) : 0;
                        
                }else
                {
                    $email = $objUser->email;
                        $competeTask = Task::where('status','done')->where('is_missed',0)->whereNotNull('deleted_at')->where(function ($query) use ($email) {
                                    $query->where('assignor', 'like', "%$email%")
                                        ->orWhere('assign_to', 'like', "%$email%");
                                })->count();
                        $pendingTask = Task::whereNotIn('status',['done'])->where('is_missed',0)->whereNotNull('deleted_at')->where(function ($query) use ($email) {
                                   $query->where('assignor', 'like', "%$email%")
                                        ->orWhere('assign_to', 'like', "%$email%");
                                })->count();
                                
                     $totalETAmin = collect(Task::where('status','Done')->where('is_missed',0)->whereNotNull('deleted_at')->where(function ($query) use ($email) {
                                    $query->where('assignor', 'like', "%$email%")
                                        ->orWhere('assign_to', 'like', "%$email%");
                                })->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
                    $totalATCMin = collect(Task::where('status','Done')->where('is_missed',0)->whereNotNull('deleted_at')->where(function ($query) use ($email) {
                                    $query->where('assignor', 'like', "%$email%")
                                        ->orWhere('assign_to', 'like', "%$email%");
                                })->where('etc_done',">",0)->pluck('etc_done')->toArray())->sum();
                                
                    // Calculate initial average completion days for user's tasks
                    $tasksWithCompletionDays = Task::where('status','Done')->where('is_missed',0)->whereNotNull('deleted_at')
                        ->where(function ($query) use ($email) {
                            $query->where('assignor', 'like', "%$email%")
                                  ->orWhere('assign_to', 'like', "%$email%");
                        })
                        ->whereNotNull('start_date')
                        ->whereNotNull('completion_date')
                        ->where('completion_date', '!=', '0000-00-00')
                        ->where('completion_date', '!=', '0000-00-00 00:00:00')
                        ->get();
                        
                    $totalCompletionDays = 0;
                    $validTasksCount = 0;
                    
                    foreach ($tasksWithCompletionDays as $task) {
                        try {
                            $startDate = \Carbon\Carbon::parse($task->start_date);
                            $completionDate = \Carbon\Carbon::parse($task->completion_date);
                            $days = $startDate->diffInDays($completionDate);
                            $totalCompletionDays += $days;
                            $validTasksCount++;
                        } catch (\Exception $e) {
                            // Skip this task if dates are invalid
                            continue;
                        }
                    }
                    
                    $avgCompletionDays = $validTasksCount > 0 ? round($totalCompletionDays / $validTasksCount, 1) : 0;
                }
            
                $priority = collect([
                    ['value'=>"urgent", 'color' => 'danger'],
                    ['value'=>"Take your time", 'color' => 'warning'],
                    ['value'=>"normal", 'color' => 'success']
                ]);
                $users = User::select('users.*')->get();
                $totalETAmin = number_format($totalETAmin/60,2);
                $totalATCMin = number_format($totalATCMin/60,2);
                return $dataTable->render('taskly::projects.done-task.donetasklist',compact('currentWorkspace','totalETAmin','stages','users','competeTask','pendingTask','priority','totalATCMin','avgCompletionDays'));
            } else {
                return redirect()->back()->with('error', 'permission Denied');
            }
        }

    public function doneTaskCountData(Request $request)
    {
        $objUser = Auth::user();
        $workspaceId = getActiveWorkSpace();

        // Define allowed emails that can see all done tasks (same as DataTable)
        $allowedEmails = [
            'president@5core.com',
            'hr@5core.com',
            'tech-support@5core.com',
            'support@5core.com',
            'mgr-advertisement@5core.com',
            'mgr-content@5core.com',
            'ritu.kaur013@gmail.com',
            'sjoy7486@gmail.com',
            'ecomm2@5core.com',
            'sr.manager@5core.com',
            'inventory@5core.com',
            'software13@5core.com',
            'software4@5core.com'
        ];
        
        $currentUserEmail = $objUser->email ?? '';
        $hasEmailAccess = in_array($currentUserEmail, $allowedEmails);

        // Base query for done tasks (matching DataTable exactly)
        $taskBaseQuery = Task::select('tasks.*', 'stages.name as stage_name', 'assignor_users.name as assigner_name')
            ->join('stages', 'stages.name', '=', 'tasks.status')
            ->where('deleted_at', '!=', NULL)
            ->where('is_missed', 0)
            ->where('status', "Done")
            ->leftJoin('users as assignor_users', 'assignor_users.email', '=', 'tasks.assignor')
            ->where('tasks.workspace', $workspaceId)
            ->groupBy('tasks.id');

        // Apply user permissions filter (matching DataTable exactly)
        if (!$hasEmailAccess) {
            // User can only see tasks where they are assignees (NOT assignors)
            $taskBaseQuery->where(function ($query) use ($objUser) {
                $query->whereRaw("FIND_IN_SET(?, assign_to)", [$objUser->email]);
            });
        }
        // If user IS in allowed emails list, they can see all tasks (no additional filter needed)

        // Apply filters (matching DataTable exactly)
        if (request()->has('assignee_name') && !empty(request()->input('assignee_name'))) {
            $assigneeEmails = request()->input('assignee_name');
            $taskBaseQuery->where(function ($query) use ($assigneeEmails) {
                foreach ($assigneeEmails as $email) {
                    $query->orWhereRaw("FIND_IN_SET(?, assign_to)", [$email]);
                }
            });
        }
        
        if (request()->has('assignor_name') && !empty(request()->input('assignor_name'))) {
            $assignorEmails = request()->input('assignor_name');
            $taskBaseQuery->where(function ($query) use ($assignorEmails) {
                foreach ($assignorEmails as $email) {
                    $query->orWhereRaw("FIND_IN_SET(?, assignor)", [$email]);
                }
            });
        }

        // Month filter - show last 30 days instead of strict current month
        if (request()->has('month') && !empty(request()->input('month'))) {
            // Use last 30 days instead of current month to be more flexible
            $thirtyDaysAgo = \Carbon\Carbon::now()->subDays(30)->format('Y-m-d');
            $today = \Carbon\Carbon::now()->format('Y-m-d');
            
            $taskBaseQuery->where(function($query) use ($thirtyDaysAgo, $today) {
                $query->whereNotNull('tasks.completion_date')
                      ->where('tasks.completion_date', '!=', '0000-00-00')
                      ->where('tasks.completion_date', '!=', '0000-00-00 00:00:00')
                      ->whereBetween('tasks.completion_date', [$thirtyDaysAgo, $today]);
            });
        }

        // Date Filter (completion_date) - matching DataTable exactly
        if (request()->has('date_filter') && !empty(request()->input('date_filter'))) {
            $dateFilter = request()->input('date_filter');
            $carbon = \Carbon\Carbon::now();
            if ($dateFilter === 'today') {
                $taskBaseQuery->whereDate('tasks.completion_date', $carbon->toDateString());
            } elseif ($dateFilter === 'yesterday') {
                $taskBaseQuery->whereDate('tasks.completion_date', $carbon->copy()->subDay()->toDateString());
            } elseif ($dateFilter === 'this_week') {
                $taskBaseQuery->whereBetween('tasks.completion_date', [$carbon->copy()->startOfWeek()->toDateString(), $carbon->copy()->endOfWeek()->toDateString()]);
            } elseif ($dateFilter === 'this_month') {
                $taskBaseQuery->whereMonth('tasks.completion_date', $carbon->month)
                             ->whereYear('tasks.completion_date', $carbon->year);
            } elseif ($dateFilter === 'previous_month') {
                $prevMonth = $carbon->copy()->subMonth();
                $taskBaseQuery->whereMonth('tasks.completion_date', $prevMonth->month)
                             ->whereYear('tasks.completion_date', $prevMonth->year);
            } elseif ($dateFilter === 'last_30_days') {
                $taskBaseQuery->whereBetween('tasks.completion_date', [$carbon->copy()->subDays(30)->toDateString(), $carbon->toDateString()]);
            } elseif ($dateFilter === 'custom') {
                // Handle custom date range
                $startDate = request()->input('start_date');
                $endDate = request()->input('end_date');
                
                if (!empty($startDate) && !empty($endDate)) {
                    $startDate = \Carbon\Carbon::parse($startDate)->startOfDay()->format('Y-m-d H:i:s');
                    $endDate = \Carbon\Carbon::parse($endDate)->endOfDay()->format('Y-m-d H:i:s');
                    $taskBaseQuery->whereBetween('tasks.completion_date', [$startDate, $endDate]);
                } elseif (!empty($startDate)) {
                    $startDate = \Carbon\Carbon::parse($startDate)->format('Y-m-d');
                    $taskBaseQuery->whereDate('tasks.completion_date', '>=', $startDate);
                } elseif (!empty($endDate)) {
                    $endDate = \Carbon\Carbon::parse($endDate)->format('Y-m-d');
                    $taskBaseQuery->whereDate('tasks.completion_date', '<=', $endDate);
                }
            }
        }

        if (request()->has('group_name') && !empty(request()->input('group_name'))) {
            $groupName = request()->input('group_name');
            $taskBaseQuery->where('tasks.group', 'like', "%$groupName%");
        }

        if (request()->has('task_name') && !empty(request()->input('task_name'))) {
            $taskName = request()->input('task_name');
            $taskBaseQuery->where('tasks.title', 'like', "%$taskName%");
        }

        // Search filter - handle both search_value and search.value parameters
        $searchValue = null;
        if (request()->has('search_value') && !empty(request()->input('search_value'))) {
            $searchValue = request()->input('search_value');
        } elseif (request()->has('search.value') && !empty(request()->input('search.value'))) {
            $searchValue = request()->input('search.value');
        }
        
        if (!empty($searchValue)) {
            $taskBaseQuery->where(function ($query) use ($searchValue) {
                $query->where('assignor_users.name', 'like', "%$searchValue%")
                    ->orWhereRaw("EXISTS (SELECT 1 FROM users WHERE FIND_IN_SET(users.email, tasks.assign_to) AND users.name LIKE ?)", ["%$searchValue%"])
                    ->orWhere('tasks.title', 'like', "%$searchValue%")
                    ->orWhere('tasks.group', 'like', "%$searchValue%");
            });
        }

        // Get counts and totals
        $totalTasks = $taskBaseQuery->count();
        
        // Temporary debug to see what's happening
        \Log::info('Done task query debug:', [
            'month_requested' => request()->input('month'),
            'current_year' => date('Y'),
            'total_tasks_found' => $totalTasks,
            'assignee_filter' => request()->input('assignee_name'),
            'sql' => $taskBaseQuery->toSql(),
            'bindings' => $taskBaseQuery->getBindings()
        ]);
        
        $totalETAmin = collect((clone $taskBaseQuery)->where('eta_time', ">", 0)->pluck('eta_time')->toArray())->sum();
        $totalATCMin = collect((clone $taskBaseQuery)->where('etc_done', ">", 0)->pluck('etc_done')->toArray())->sum();

        $totalETAHours = round($totalETAmin / 60, 1);
        $totalATCHours = round($totalATCMin / 60, 1);
        
        // Calculate average completion days
        $tasksWithCompletionDays = (clone $taskBaseQuery)
            ->whereNotNull('start_date')
            ->whereNotNull('completion_date')
            ->where('start_date', '!=', '0000-00-00')
            ->where('start_date', '!=', '0000-00-00 00:00:00')
            ->where('completion_date', '!=', '0000-00-00')
            ->where('completion_date', '!=', '0000-00-00 00:00:00')
            ->get(['start_date', 'completion_date']);
            
        $totalCompletionDays = 0;
        $validTasksCount = 0;
        
        foreach ($tasksWithCompletionDays as $task) {
            try {
                $startDate = \Carbon\Carbon::parse($task->start_date);
                $completionDate = \Carbon\Carbon::parse($task->completion_date);
                $days = $startDate->diffInDays($completionDate);
                $totalCompletionDays += $days;
                $validTasksCount++;
            } catch (\Exception $e) {
                // Skip this task if dates are invalid
                continue;
            }
        }
        
        $avgCompletionDays = $validTasksCount > 0 ? round($totalCompletionDays / $validTasksCount, 1) : 0;

        return response()->json([
            'is_success' => true,
            'data' => [
                'total_tasks' => $totalTasks,
                'total_eta_hours' => $totalETAHours,
                'total_atc_hours' => $totalATCHours,
                'avg_completion_days' => $avgCompletionDays,
                'user_has_full_access' => $hasEmailAccess
            ]
        ]);
    }

    public function taskRatingData(Request $request)
{
    $objUser = Auth::user();
    $workspaceId = getActiveWorkSpace();

    // Base query for tasks
    $taskBaseQuery = Task::where('workspace', $workspaceId);

    // Apply user permissions
    if (!$objUser->hasRole('client') && !$objUser->hasRole('company') && !$objUser->hasRole('Manager All Access') && !$objUser->hasRole('hr')) {
        $taskBaseQuery->where(function ($query) use ($objUser) {
            $query->whereRaw("FIND_IN_SET(?, assign_to)", [$objUser->email])
                ->orWhere('assignor', $objUser->email);
        });
    }

    // Get all tasks with delete_rating
    $ratedTasks = (clone $taskBaseQuery)->whereNotNull('delete_rating')
        ->where('delete_rating', '>', 0)
        ->get();

    $totalRatings = $ratedTasks->count();
    
    if ($totalRatings == 0) {
        return response()->json([
            'average_rating' => '0.0',
            'total_ratings' => 0,
            'rating_breakdown' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
            'recent_reviews' => []
        ]);
    }

    // Calculate average rating
    $totalRatingSum = $ratedTasks->sum('delete_rating');
    $averageRating = round($totalRatingSum / $totalRatings, 1);

    // Calculate rating breakdown
    $ratingBreakdown = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
    foreach ($ratedTasks as $task) {
        $rating = (int) $task->delete_rating;
        if ($rating >= 1 && $rating <= 5) {
            $ratingBreakdown[$rating]++;
        }
    }

    // Get recent reviews (tasks with feedback)
    $recentReviews = (clone $taskBaseQuery)->whereNotNull('delete_rating')
        ->where('delete_rating', '>', 0)
        ->whereNotNull('delete_feedback')
        ->where('delete_feedback', '!=', '')
        ->orderBy('updated_at', 'desc')
        ->limit(5)
        ->get()
        ->map(function ($task) {
            return [
                'task_title' => $task->title,
                'rating' => (int) $task->delete_rating,
                'feedback' => $task->delete_feedback,
                'created_at' => $task->updated_at->diffForHumans()
            ];
        });

    return response()->json([
        'average_rating' => number_format($averageRating, 1),
        'total_ratings' => $totalRatings,
        'rating_breakdown' => $ratingBreakdown,
        'recent_reviews' => $recentReviews
    ]);
}


    public function invoice(Request $request ,$id){
        $project = Project::find($id);
        $query = Invoice::where('workspace', getActiveWorkSpace())->where('account_type','Taskly')->where('category_id',$id);
        $invoices = $query->with('customers')->orderBy('id', 'desc')->get();
        return view('taskly::projects.invoice', compact( 'project' ,'id' ,'invoices'));
    }
      public function taskImport(Request $request)
    {
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();
        if ($request->isMethod('post')) {
            $request->validate([
                'file' => 'required',
            ]);
            try {
                Excel::import(new TaskImport(), request()->file('file'));
                $redirectUrl = route('projecttask.list');
                $message = "Tasks imported successfully with WhatsApp notifications";
                Log::info('Tasks imported successfully with WhatsApp notifications enabled');
                return response()->json([
                    'status'        =>  true,
                    'response_code' =>  200,
                    'message'       =>  $message,
                    'data'          =>  ['redirect_url'=>$redirectUrl]
                ], 200);
            } catch (\Exception $e) {
                Log::error('Task import failed: ' . $e->getMessage());
                return response()->json([
                    'status'        =>  false,
                    'response_code' =>  500,
                    'message'       =>  'Task import failed: ' . $e->getMessage()
                ], 500);
            }
        }

        return view('taskly::projects.import', compact('currentWorkspace', 'objUser'));
    }

public function taskTracklist(Request $request)
{
    $currentWorkspace = getActiveWorkSpace();
    $objUser = Auth::user();
    $stages = Stage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->get();
    
    // Get users with their employee and designation information
    $users = User::with(['employee', 'employee.designation','employee.department'])
        ->select('users.*')
        ->whereNotIn('email', ['company@example.com', 'president@5core.com', 'superadmin@example.com'])
        ->get();

        if($objUser->hasRole('company') || $objUser->hasRole('Manager All Access') || $objUser->hasRole('hr') )
            {
                    $taskBaseQuery = Task::join('stages', 'stages.name', '=', 'tasks.status')
                        ->leftJoin('users as assignor_users', 'assignor_users.email', '=', 'tasks.assignor')
                        ->where('tasks.workspace', $currentWorkspace)->where('is_missed',0)
                        ->whereNull('tasks.deleted_at')
                        ->select('tasks.*', 'stages.name as stage_name', 'assignor_users.name as assigner_name','eta_time','etc_done')
                        ->distinct('tasks.id');
            }
            $completedTask = (clone $taskBaseQuery)
                        ->where('tasks.status', 'Done');
            
            $pendingTask = (clone $taskBaseQuery)
                        ->where('tasks.status', '!=', 'Done')
                        ->where('tasks.status', '!=', '');
                 
            $overdueTask = (clone $taskBaseQuery)
                        ->where('tasks.status', '!=', '')
                        ->where('tasks.status', '!=', '')
                        ->where('tasks.due_date', '<', now());

            $totalTask = (clone $taskBaseQuery);
            $totalETAmin = (clone $taskBaseQuery);
            $totalATCMin = (clone $taskBaseQuery);
            $totalETAmin = collect($totalETAmin->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
            
                    
            $totalATCMin = collect($totalATCMin->where('etc_done',">",0)->pluck('etc_done')->toArray())->sum();

            // dd($completedTask->count(),$overdueTask->count(),$pendingTask->count(),$totalTask->count(),round($totalATCMin / 60),round($totalETAmin / 60));
    
    $users = User::with('employee')->select('users.*')
        ->whereNotIn('email',['company@example.com','president@5core.com','superadmin@example.com'])
        ->get();
    
    $resultData = [];

     foreach ($users as $key => $user) {

        $email = $user->email;

        $userTasks = (clone $taskBaseQuery)
            ->where('tasks.assign_to', 'like', "%$email%");

        // User counts
        $total = (clone $userTasks)->count();
        $pending = (clone $userTasks)->where('tasks.status', '!=', 'Done')->count();
        $overdue = (clone $userTasks)->where('tasks.due_date', '<', now())->count();
        $done = (clone $userTasks)->where('tasks.status', 'Done')->count();

        // ETA / ATC totals
        $etaMin = (clone $userTasks)->where('eta_time', '>', 0)->sum('eta_time');
        $atcMin = (clone $userTasks)->where('etc_done', '>', 0)->sum('etc_done');

        $totalETAmin1 = collect((clone $userTasks)->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
            $totalATCMin1 = collect((clone $userTasks)->where('etc_done',">",0)->pluck('etc_done')->toArray())->sum();
            

        // L30
        $carbon = \Carbon\Carbon::now();
        $start30 = now()->subDays(30)->startOfDay();
        $start7 = now()->subDays(7)->startOfDay();
        $end = now()->endOfDay();
        
        $etaL30 = Task::whereRaw("FIND_IN_SET(?, assign_to)", [$email])
            ->where('etc_done', '>', 0)      // Only count ETA where actual time exists
            ->where('eta_time', '>', 0)
            ->whereBetween('completion_date', [$start30, $end])
            ->sum('eta_time');

        $etaL30Hours = round($etaL30 / 60, 1);
       
        $atcL30 = Task::whereRaw("FIND_IN_SET(?, assign_to)", [$email])
        ->where('etc_done', '>', 0)
        ->whereBetween('completion_date', [$start30, $end])
        ->sum('etc_done');

       $atcL30Hours = round($atcL30 / 60, 1);        

        // L7
         $etaL7 = Task::whereRaw("FIND_IN_SET(?, assign_to)", [$email])
            ->where('etc_done', '>', 0)      // Only count ETA where actual time exists
            ->where('eta_time', '>', 0)
            ->whereBetween('completion_date', [$start7, $end])
            ->sum('eta_time');

        $etaL7Hours = round($etaL7 / 60, 1);

        // TeamLogger Total Time Calculation
        $teamloggerTotalHours = 0;
        $teamloggerActiveHours = 0;
        $teamloggerIdleHours = 0;
        
        // Get TeamLogger data for this user (last 30 days)
        $teamloggerData = \App\Models\TeamloggerTime::where('email', $email)
            ->whereBetween('date', [$start30->format('Y-m-d'), $end->format('Y-m-d')])
            ->get();
        
        if ($teamloggerData->count() > 0) {
            $teamloggerTotalHours = round($teamloggerData->sum('total_hours'), 2);
            $teamloggerActiveHours = round($teamloggerData->sum('activeHours'), 2);
            $teamloggerIdleHours = round($teamloggerData->sum('idle_hours'), 2);
        }

        // Build response row (exact same keys as you used)
        $resultData[$key] = [
            'name'          => $user->name,
            'dept'          => $user?->employee?->department?->name ?? 'N/A',
            'designation'   => $user?->employee?->designation?->name ?? 'N/A',

            'total_count'   => $total,
            'pending_count' => $pending,
            'overdue_count' => $overdue,
            'done_count'    => $done,

           'eta_sum'       => number_format($totalETAmin1 / 60, 2),
           'eta_sum_l30'   => $etaL30Hours,
           'atc_sum_l30'   => $atcL30Hours,
           'eta_sum_l7'    => $etaL7Hours,
           
           'teamlogger_total_hours'  => $teamloggerTotalHours,
           'teamlogger_active_hours' => $teamloggerActiveHours,
           'teamlogger_idle_hours'   => $teamloggerIdleHours,
        ];
    } 
    
    usort($resultData, fn ($a, $b) => $b['overdue_count'] <=> $a['overdue_count']);

    return view('taskly::projects.track-task.list',compact('currentWorkspace','resultData','stages','users',
    'completedTask','overdueTask','pendingTask','totalTask','totalATCMin','totalETAmin'));
}

public function getTeamloggerDataByDate(Request $request)
{
    try { 
        $completionDate = $request->input('completion_date');
        $filterAssigneeEmails = $request->input('assignee_emails', []);
        $filterAssignorEmails = $request->input('assignor_emails', []);

        $filterAssigneeEmails = array_map(function($email) {
            return $email === "customercare@5core.com" ? "debhritiksha@gmail.com" : $email;
        }, $filterAssigneeEmails);
        
        $filterAssignorEmails = array_map(function($email) {
            return $email === "customercare@5core.com" ? "debhritiksha@gmail.com" : $email;
        }, $filterAssignorEmails);

        
        if (empty($completionDate)) {
            return response()->json([
                'success' => false,
                'message' => 'Completion date is required'
            ]);
        }
        
        $objUser = Auth::user();
        $workspaceId = getActiveWorkSpace();
        
        // Define allowed emails that can see all done tasks (same as DataTable)
        $allowedEmails = [
            'president@5core.com',
            'hr@5core.com',
            'tech-support@5core.com',
            'support@5core.com',
            'mgr-advertisement@5core.com',
            'mgr-content@5core.com',
            'ritu.kaur013@gmail.com',
            'sjoy7486@gmail.com',
            'ecomm2@5core.com',
            'sr.manager@5core.com',
            'inventory@5core.com',
            'software13@5core.com',
            'software4@5core.com',
        ];
        
        $currentUserEmail = $objUser->email ?? '';
        $hasEmailAccess = in_array($currentUserEmail, $allowedEmails);
        
        // Query tasks for this specific completion date with same filters as DataTable
        $tasksQuery = Task::select('tasks.assign_to', 'tasks.assignor')
            ->join('stages', 'stages.name', '=', 'tasks.status')
            ->where('deleted_at', '!=', NULL)
            ->where('is_missed', 0)
            ->where('status', "Done")
            ->where('tasks.workspace', $workspaceId)
            ->whereDate('tasks.completion_date', $completionDate);
        
        // Apply user permissions filter (matching DataTable exactly)
        if (!$hasEmailAccess) {
            $tasksQuery->where(function ($query) use ($objUser) {
                $query->whereRaw("FIND_IN_SET(?, assign_to)", [$objUser->email]);
            });
        }
             $targetEmails = [];
        if (!empty($filterAssigneeEmails)) {
         // ✅ Case 1: Assignee filter is applied — use only those selected emails
         $targetEmails = $filterAssigneeEmails;
       }
       else{
        // Apply assignee filter if provided
        if (!empty($filterAssigneeEmails)) {
            $tasksQuery->where(function ($query) use ($filterAssigneeEmails) {
                foreach ($filterAssigneeEmails as $email) {
                    $query->orWhereRaw("FIND_IN_SET(?, assign_to)", [$email]);
                }
            });
        }
        
        // Apply assignor filter if provided
        if (!empty($filterAssignorEmails)) {
            $tasksQuery->where(function ($query) use ($filterAssignorEmails) {
                foreach ($filterAssignorEmails as $email) {
                    $query->orWhereRaw("FIND_IN_SET(?, assignor)", [$email]);
                }
            });
        }
        
        $tasks = $tasksQuery->get();
        
        foreach ($tasks as $task) {
            // Handle assign_to (comma-separated emails)
            if (!empty($task->assign_to)) {
                $assignees = explode(',', $task->assign_to);
                foreach ($assignees as $email) {
                    $email = trim($email);
                    if (!empty($email)) {
                        $targetEmails[] = $email;
                    }
                }
            }
            
            // Handle assignor (single email)
            if (!empty($task->assignor)) {
                $targetEmails[] = trim($task->assignor);
            }
        }
        
        // Remove duplicates
        $targetEmails = array_unique($targetEmails);
        
        if (empty($targetEmails)) {
            return response()->json([
                'success' => true,
                'activeHours' => 0,
                'date' => $completionDate,
                'debug' => [
                    'tasks_found' => $tasks->count(),
                    'emails_found' => count($targetEmails)
                ]
            ]);
        }
       }
        
        // Parse completion date and set time range (12:00 PM to 11:59 AM next day)
        $date = \Carbon\Carbon::parse($completionDate);
        $startTime = $date->copy()->setTime(12, 0, 0)->timestamp * 1000;
        $endTime = $date->copy()->addDay()->setTime(11, 59, 59)->timestamp * 1000;
        
        $curl = curl_init();
        $apiUrl = "https://api2.teamlogger.com/api/employee_summary_report?startTime={$startTime}&endTime={$endTime}";
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vaGlwZXJyLmNvbSIsInN1YiI6IjYyNDJhZjhhNmJlMjQ2YzQ5MTcwMmFiYjgyYmY5ZDYwIiwiYXVkIjoic2VydmVyIn0.mRzusxn0Ws9yD7Qmxu9QcFCNiLOnoEXSjy90edAFK4U',
                'Content-Type: application/json'
            ),
        ));
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        // Log for debugging
        \Log::info('Teamlogger API Request for date: ' . $completionDate, [
            'target_emails' => $targetEmails,
            'api_url' => $apiUrl,
            'http_code' => $httpCode,
            'response_preview' => substr($response, 0, 500)
        ]);
        
        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            $totalHours = 0;
            $totalIdleHours = 0;
            $foundEmails = [];
            
            // Try different response structures
            if (isset($data['data']) && is_array($data['data'])) {
                foreach ($data['data'] as $employee) {
                    if (isset($employee['email']) && in_array($employee['email'], $targetEmails)) {
                        $totalHours += isset($employee['totalHours']) ? $employee['totalHours'] : 0;
                        $totalIdleHours += isset($employee['idleHours']) ? $employee['idleHours'] : 0;
                        $foundEmails[] = $employee['email'];
                    }
                }
            } elseif (isset($data['employees']) && is_array($data['employees'])) {
                foreach ($data['employees'] as $employee) {
                    if (isset($employee['email']) && in_array($employee['email'], $targetEmails)) {
                        $totalHours += isset($employee['totalHours']) ? $employee['totalHours'] : 0;
                        $totalIdleHours += isset($employee['idleHours']) ? $employee['idleHours'] : 0;
                        $foundEmails[] = $employee['email'];
                    }
                }
            } elseif (is_array($data)) {
                foreach ($data as $employee) {
                    if (is_array($employee) && isset($employee['email']) && in_array($employee['email'], $targetEmails)) {
                        $totalHours += isset($employee['totalHours']) ? $employee['totalHours'] : 0;
                        $totalIdleHours += isset($employee['idleHours']) ? $employee['idleHours'] : 0;
                        $foundEmails[] = $employee['email'];
                    }
                }
            }
            
            $activeHours = max(0, $totalHours - $totalIdleHours);
            
            \Log::info('Teamlogger data processed', [
                'date' => $completionDate,
                'total_hours' => $totalHours,
                'idle_hours' => $totalIdleHours,
                'active_hours' => $activeHours,
                'target_emails' => $targetEmails,
                'found_emails' => $foundEmails
            ]);
            
            return response()->json([
                'success' => true,
                'activeHours' => round($activeHours, 2),
                'date' => $completionDate,
                'debug' => [
                    'target_emails_count' => count($targetEmails),
                    'found_emails_count' => count($foundEmails),
                    'total_hours' => $totalHours,
                    'idle_hours' => $totalIdleHours
                ]
            ]);
        } else {
            \Log::error('Teamlogger API failed', [
                'http_code' => $httpCode,
                'date' => $completionDate
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch data from Teamlogger API',
                'http_code' => $httpCode
            ]);
        }
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

public function getTeamloggerData(Request $request)
{
    try {
        // Get assignee emails from request, fallback to current user
        $assigneeEmails = $request->input('assignee_emails', []);
        $assignorEmails = $request->input('assignor_emails', []);

         $assigneeEmails = array_map(function($email) {
            return $email === "customercare@5core.com" ? "debhritiksha@gmail.com" : $email;
        }, $assigneeEmails);
        
        $assignorEmails = array_map(function($email) {
            return $email === "customercare@5core.com" ? "debhritiksha@gmail.com" : $email;
        }, $assignorEmails);

        $currentUserEmail = Auth::user()->email;
        
        // If no specific emails provided, use current user
        $targetEmails = [];
        if (!empty($assigneeEmails)) {
            $targetEmails = array_merge($targetEmails, $assigneeEmails);
        }
        if (!empty($assignorEmails)) {
            $targetEmails = array_merge($targetEmails, $assignorEmails);
        }
        if (empty($targetEmails)) {
            $targetEmails = [$currentUserEmail];
        }
        
        // Remove duplicates
        $targetEmails = array_unique($targetEmails);
        
        // Calculate date range based on filter (12:00 PM to 11:59 AM next day)
        $dateFilter = $request->input('date_filter');
        $startTime = null;
        $endTime = null;
        
        if ($dateFilter == 'today') { 
            // Today: from 12:00 PM today to 11:59 AM tomorrow
            $startTime = now()->setTime(12, 0, 0)->timestamp * 1000;
            $endTime = now()->addDay()->setTime(11, 59, 59)->timestamp * 1000;
        } elseif ($dateFilter == 'yesterday') {
            // Yesterday: from 12:00 PM yesterday to 11:59 AM today
            $startTime = now()->subDay()->setTime(12, 0, 0)->timestamp * 1000;
            $endTime = now()->setTime(11, 59, 59)->timestamp * 1000;
        } elseif ($dateFilter == 'this_week') {
            // This week: from 12:00 PM on Monday to 11:59 AM next Monday
            $startTime = now()->startOfWeek()->setTime(12, 0, 0)->timestamp * 1000;
            $endTime = now()->endOfWeek()->addDay()->setTime(11, 59, 59)->timestamp * 1000;
        } elseif ($dateFilter == 'this_month') {
            // This month: from 12:00 PM on 1st to 11:59 AM on next month 1st
            $startTime = now()->startOfMonth()->setTime(12, 0, 0)->timestamp * 1000;
            $endTime = now()->endOfMonth()->addDay()->setTime(11, 59, 59)->timestamp * 1000;
        } elseif ($dateFilter == 'previous_month') {
            // Previous month: from 12:00 PM on 1st to 11:59 AM on this month 1st
            $startTime = now()->subMonth()->startOfMonth()->setTime(12, 0, 0)->timestamp * 1000;
            $endTime = now()->subMonth()->endOfMonth()->addDay()->setTime(11, 59, 59)->timestamp * 1000;
        } elseif ($dateFilter == 'last_30_days') {
            // Last 30 days: from 12:00 PM 30 days ago to 11:59 AM tomorrow
            $startTime = now()->subDays(30)->setTime(12, 0, 0)->timestamp * 1000;
            $endTime = now()->addDay()->setTime(11, 59, 59)->timestamp * 1000;
        } elseif ($dateFilter == 'custom') {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            if (!empty($startDate)) {
                // Custom start: 12:00 PM on selected date
                $startTime = \Carbon\Carbon::parse($startDate)->setTime(12, 0, 0)->timestamp * 1000;
            }
            if (!empty($endDate)) {
                // Custom end: 11:59 AM on day after selected date
                $endTime = \Carbon\Carbon::parse($endDate)->addDay()->setTime(11, 59, 59)->timestamp * 1000;
            }
        }
        
        // Default to last 30 days if no specific filter (12:00 PM to 11:59 AM pattern)
        if (!$startTime || !$endTime) {
            $startTime = now()->subDays(30)->setTime(12, 0, 0)->timestamp * 1000;
            $endTime = now()->addDay()->setTime(11, 59, 59)->timestamp * 1000;
        }
        
        $curl = curl_init();
        
        $apiUrl = "https://api2.teamlogger.com/api/employee_summary_report?startTime={$startTime}&endTime={$endTime}";
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vaGlwZXJyLmNvbSIsInN1YiI6IjYyNDJhZjhhNmJlMjQ2YzQ5MTcwMmFiYjgyYmY5ZDYwIiwiYXVkIjoic2VydmVyIn0.mRzusxn0Ws9yD7Qmxu9QcFCNiLOnoEXSjy90edAFK4U',
                'Content-Type: application/json'
            ),
        ));
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            $totalHours = 0;
            $totalIdleHours = 0;
            $foundEmails = [];
            
            // Try different response structures and sum hours for target emails
            if (isset($data['data']) && is_array($data['data'])) {
                foreach ($data['data'] as $employee) {
                    if (isset($employee['email']) && in_array($employee['email'], $targetEmails)) {
                        $hours = isset($employee['totalHours']) ? $employee['totalHours'] : 0;
                        $idleHours = isset($employee['idleHours']) ? $employee['idleHours'] : 0;
                        $totalHours += $hours;
                        $totalIdleHours += $idleHours;
                        $foundEmails[] = $employee['email'];
                    }
                }
            } elseif (isset($data['employees']) && is_array($data['employees'])) {
                foreach ($data['employees'] as $employee) {
                    if (isset($employee['email']) && in_array($employee['email'], $targetEmails)) {
                        $hours = isset($employee['totalHours']) ? $employee['totalHours'] : 0;
                        $idleHours = isset($employee['idleHours']) ? $employee['idleHours'] : 0;
                        $totalHours += $hours;
                        $totalIdleHours += $idleHours;
                        $foundEmails[] = $employee['email'];
                    }
                }
            } elseif (isset($data['totalHours']) && count($targetEmails) === 1 && $targetEmails[0] === $currentUserEmail) {
                // Single user response structure
                $totalHours = $data['totalHours'];
                $totalIdleHours = isset($data['idleHours']) ? $data['idleHours'] : 0;
                $foundEmails[] = $currentUserEmail;
            } elseif (is_array($data)) {
                foreach ($data as $employee) {
                    if (isset($employee['email']) && in_array($employee['email'], $targetEmails)) {
                        $hours = isset($employee['totalHours']) ? $employee['totalHours'] : 0;
                        $idleHours = isset($employee['idleHours']) ? $employee['idleHours'] : 0;
                        $totalHours += $hours;
                        $totalIdleHours += $idleHours;
                        $foundEmails[] = $employee['email'];
                    }
                }
            }
            
            // Calculate active hours (totalHours - idleHours)
            $activeHours = $totalHours - $totalIdleHours;
            $activeHours = max(0, $activeHours); // Ensure it's not negative
            
            return response()->json([
                'success' => true,
                'totalHours' => round($totalHours, 2),
                'idleHours' => round($totalIdleHours, 2),
                'activeHours' => round($activeHours, 2),
                'targetEmails' => $targetEmails,
                'foundEmails' => $foundEmails,
                'currentUserEmail' => $currentUserEmail
            ]);
            
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch data from Teamlogger API',
                'httpCode' => $httpCode
            ]);
        }
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}



}
