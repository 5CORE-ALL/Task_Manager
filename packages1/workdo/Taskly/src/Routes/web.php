<?php

/*
|------------------------------------------------------------ --------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These 
 
 
 
  
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Workdo\Taskly\Http\Controllers\BugStageController;
use Workdo\Taskly\Http\Controllers\AutomateTaskController;

use Workdo\Taskly\Http\Controllers\DashboardController;
use Workdo\Taskly\Http\Controllers\ProjectController;
use Workdo\Taskly\Http\Controllers\ProjectReportController;

use Workdo\Taskly\Http\Controllers\MissedTaskController;

use Workdo\Taskly\Http\Controllers\StageController;
use App\Http\Controllers\ReportController;


Route::middleware(['web','auth','verified','PlanModuleCheck:Taskly'])->group(function ()
{
    Route::get('dashboard/taskly',[DashboardController::class,'index'])->name('taskly.dashboard'); 
    Route::get('dashboard/taskly/my-tasks',[DashboardController::class,'getMyTasks'])->name('taskly.my.tasks');
    Route::get('dashboard/taskly/my-tasks-graph',[DashboardController::class,'getTasksGraph'])->name('taskly.my.tasks.graph');
    Route::get('dashboard/taskly/employee-tasks',[DashboardController::class,'getEmployeeTasks'])->name('taskly.employee.tasks');
    Route::post('dashboard/taskly/my-team-add',[DashboardController::class,'myTeamAdd'])->name('taskly.my.team.add');
    
     // sales dashboard
    Route::get('dashboard/taskly/sales_dashboard',[DashboardController::class,'sales_dashboard'])->name('taskly.sales.dashboard');

    Route::get('/project/copy/{id}',[ProjectController::class,'copyproject'])->name('project.copy');
    Route::post('/project/copy/store/{id}',[ProjectController::class,'copyprojectstore'])->name('project.copy.store');

    Route::resource('projects', ProjectController::class);
    Route::resource('stages', StageController::class);
    Route::get('project/staging', [StageController::class, 'staging'])->name('project.staging');

    Route::get('projects-list', [ProjectController::class,'List'])->name('projects.list');

    //project import
    Route::get('project/import/export', [ProjectController::class,'fileImportExport'])->name('project.file.import');
    Route::post('project/import', [ProjectController::class,'fileImport'])->name('project.import');
    Route::get('project/import/modal', [ProjectController::class,'fileImportModal'])->name('project.import.modal');
    Route::post('project/data/import/', [ProjectController::class,'projectImportdata'])->name('project.import.data');

    //project Setting
    Route::get('project/setting/{id}', [ProjectController::class,'CopylinkSetting'])->name('project.setting');
    Route::post('project/setting/save{id}', [ProjectController::class,'CopylinkSettingSave'])->name('project.setting.save');

    Route::post('send-mail', [ProjectController::class,'sendMail'])->name('send.mail');
    // Task Board
    Route::get('project/task-board',[ProjectController::class,'taskBoard'])->name('projects.task.board');
    Route::get('projects/{id}/calendar',[ProjectController::class,'calendar'])->name('projects.calendar');

    Route::get('project/task-board/create',[ProjectController::class,'taskCreate'])->name('tasks.create');
    Route::get('project/task-board/create-multiple',[ProjectController::class,'multipleTaskCreate'])->name('tasks.create.multiple');
    Route::post('project/task-board/save-multiple-task',[ProjectController::class,'multipleTaskSave'])->name('tasks.save.multiple');
    Route::post('project/task-board/save',[ProjectController::class,'taskStore'])->name('tasks.save');
    Route::post('projects/{id}/task-board/order-update',[ProjectController::class,'taskOrderUpdate'])->name('tasks.update.order');
    Route::get('project/task-board/edit/{tid}',[ProjectController::class,'taskEdit'])->name('tasks.edit');
    Route::post('project/task-board/{tid}/update',[ProjectController::class,'taskUpdate'])->name('tasks.update');
    Route::delete('project/task-board-delete/{tid}',[ProjectController::class,'taskDestroy'])->name('tasks.destroy');
    Route::get('projects/task-board/{tid}/{cid?}',[ProjectController::class,'taskShow'])->name('tasks.show');
    Route::get('project/task-board-list', [ProjectController::class,'TaskList'])->name('projecttask.list'); 
    Route::get('project/task-missed-list', [MissedTaskController::class,'missedTaskList'])->name('missedTaskList.list');

    Route::get('project/task-done-list', [ProjectController::class,'doneTasklist'])->name('projecttask.done.list');
    Route::get('project/today-completed-tasks', [ProjectController::class,'getTodayCompletedTasks'])->name('projecttask.today.completed');
    Route::get('project/urgent-etc-data', [ProjectController::class,'getUrgentETCData'])->name('projecttask.urgent.etc');
    Route::get('project/task-list/bulk-action', [ProjectController::class,'bulkAction'])->name('projecttask.bulkAction');
    Route::match(['get', 'post'], 'project/task-list/bulk-update-assignor', [ProjectController::class,'bulkUpdateAssignor'])->name('projecttask.bulkUpdateAssignor');
    Route::match(['get', 'post'], 'project/task-list/bulk-update-assignee', [ProjectController::class,'bulkUpdateAssignee'])->name('projecttask.bulkUpdateAssignee');
    Route::match(['get', 'post'], 'project/task-list/bulk-update-etc', [ProjectController::class,'bulkUpdateETC'])->name('projecttask.bulkUpdateETC');
    Route::match(['get', 'post'], 'project/task-list/bulk-update-date', [ProjectController::class, 'bulkUpdateDate'])->name('projecttask.bulkUpdateDate');
    Route::match(['get', 'post'], 'project/task-list/bulk-update-priority', [ProjectController::class, 'bulkUpdatePriority'])->name('projecttask.bulkUpdatePriority');
    Route::get('project/task-list/inline-edit', [ProjectController::class,'inlineUpdate'])->name('projecttask.inlineEdit');
    Route::post('projects/task-member/{cid?}', [ProjectController::class,'TaskMember'])->name('tasks.members');
    Route::get('project/task-count', [ProjectController::class,'taskCountData'])->name('projecttask.count');
    Route::get('project/task-done-count', [ProjectController::class,'doneTaskCountData'])->name('projecttask.done.count');
    Route::get('project/task-rating-data', [ProjectController::class,'taskRatingData'])->name('tasks.rating.data');
    Route::get('project/teamlogger-data', [ProjectController::class,'getTeamloggerData'])->name('projecttask.teamlogger.data');
    Route::get('project/teamlogger-data-by-date', [ProjectController::class,'getTeamloggerDataByDate'])->name('projecttask.teamlogger.by.date');
    Route::get('project/task-track', [ProjectController::class,'taskTracklist'])->name('projecttask.track');
        Route::post('project/update-etc-done', [ProjectController::class,'updateEtcDone'])->name('projecttask.update.etc');
        Route::post('project/task-save-rework',[ProjectController::class,'saveReworkReason'])->name('tasks.save.rework');

        // staging task
        Route::post('projects/staging-create-event', [ProjectController::class,'StagingCreateEvent'])->name('tasks.staging.create.event');
        Route::post('/tasks/staging/delete', [ProjectController::class, 'StagingDelete'])->name('tasks.staging.delete');
        Route::post('/tasks/staging/submit-task', [ProjectController::class, 'StagingSubmitTask'])->name('tasks.staging.submit.tasks');
        Route::post('/tasks/staging/delete-task', [ProjectController::class, 'StagingTaskDestroy'])->name('tasks.staging.delete.task');
        Route::post('/tasks/staging/update-task/{id}', [ProjectController::class, 'StagingTaskUpdate'])->name('tasks.staging.update.task');
        Route::get('/tasks/staging/play/{event_id}', [ProjectController::class, 'StagingTaskTriggerEvent'])->name('tasks.staging.play');


    Route::match(['get', 'post'], '/project/task-board/import', [ProjectController::class,'taskImport'])->name('tasks.import');


    //Automate Task
    Route::get('project/automate-task-board/create',[AutomateTaskController::class,'taskCreate'])->name('automate.tasks.create');
    Route::post('project/automate-task-board/save',[AutomateTaskController::class,'taskStore'])->name('automate.tasks.save');
    Route::get('project/automate-task-board/edit/{tid}',[AutomateTaskController::class,'taskEdit'])->name('automate.tasks.edit');
    Route::post('project/automate-task-board/{tid}/update',[AutomateTaskController::class,'taskUpdate'])->name('automate.tasks.update');
    Route::delete('project/automate-task-board-delete/{tid}',[AutomateTaskController::class,'taskDestroy'])->name('automate.tasks.destroy');
    Route::get('project/automate-task-board-list', [AutomateTaskController::class,'TaskList'])->name('projecttask.automate.list');
        Route::get('project/automate-task-count', [AutomateTaskController::class,'taskCountData'])->name('projecttask.automate.count');

    Route::get('project/automate-task-list/bulk-action', [AutomateTaskController::class,'bulkAction'])->name('projecttask.automate.bulkAction');
    Route::get('project/automate-task-pause/{tid}',[AutomateTaskController::class,'taskPauseResume'])->name('automate.tasks.pause');
    Route::match(['get', 'post'], '/project/automate-task-board/import', [AutomateTaskController::class,'autoMateTaskImport'])->name('automate.tasks.import');
    Route::get('project/automate-task-board/report', [AutomateTaskController::class,'taskReport'])->name('automate.tasks.report'); 

    // refired miss task
    Route::post('/task/re-fire', [AutomateTaskController::class, 'reFire'])->name('task.reFire');

    // scheduler error report
    Route::post('/scheduler-error-report', [AutomateTaskController::class, 'create_scheduler_error_report'])
    ->name('scheduler-error-report.store');
    Route::get('/scheduler-error-report', [AutomateTaskController::class, 'show_scheduler_error_report'])
    ->name('scheduler-error-report.index');

    // scheduler report's
    Route::get('project/all-scheduler-list',[AutomateTaskController::class,'allSchedulerList'])->name('project.scheduler.list');


    // Gantt Chart
    Route::get('projects/{id}/gantt/{duration?}',[ProjectController::class,'gantt'])->name('projects.gantt');
    Route::post('projects/{id}/gantt',[ProjectController::class,'ganttPost'])->name('projects.gantt.post');

    // finance page
    Route::get('projects/{id}/proposal',[ProjectController::class,'proposal'])->name('projects.proposal');
    Route::get('projects/{id}/invoice',[ProjectController::class,'invoice'])->name('projects.invoice');



    // bug report
    Route::get('projects/{id}/bug_report',[ProjectController::class,'bugReport'])->name('projects.bug.report');
    Route::get('projects/{id}/bug_report/create',[ProjectController::class,'bugReportCreate'])->name('projects.bug.report.create');
    Route::post('projects/{id}/bug_report',[ProjectController::class,'bugReportStore'])->name('projects.bug.report.store');
    Route::post('projects/{id}/bug_report/order-update',[ProjectController::class,'bugReportOrderUpdate'])->name('projects.bug.report.update.order');
    Route::get('projects/{id}/bug_report/{bid}/show',[ProjectController::class,'bugReportShow'])->name('projects.bug.report.show');
    Route::get('projects/{id}/bug_report/{bid}/edit',[ProjectController::class,'bugReportEdit'])->name('projects.bug.report.edit');
    Route::post('projects/{id}/bug_report/{bid}/update',[ProjectController::class,'bugReportUpdate'])->name('projects.bug.report.update');
    Route::delete('projects/{id}/bug_report/{bid}',[ProjectController::class,'bugReportDestroy'])->name('projects.bug.report.destroy');
    Route::get('projects/{id}/bug_report-list', [ProjectController::class,'BugList'])->name('projectbug.list');


    Route::get('projects/invite/{id}',[ProjectController::class,'popup'])->name('projects.invite.popup');
    Route::get('projects/share/{id}',[ProjectController::class,'sharePopup'])->name('projects.share.popup');
    Route::get('projects/share/vender/{id}',[ProjectController::class,'sharePopupVender'])->name('projects.share.vender.popup');
    Route::post('projects/share/vender/store/{id}',[ProjectController::class,'sharePopupVenderStore'])->name('projects.share.vender');
    Route::get('projects/milestone/{id}',[ProjectController::class,'milestone'])->name('projects.milestone');
    Route::post('projects/{id}/file',[ProjectController::class,'fileUpload'])->name('projects.file.upload');
    Route::post('projects/share/{id}',[ProjectController::class,'share'])->name('projects.share');


    // stages.index
    // project
    Route::get('projects/milestone/{id}',[ProjectController::class,'milestone'])->name('projects.milestone');
    Route::post('projects/milestone/{id}/store',[ProjectController::class,'milestoneStore'])->name('projects.milestone.store');
    Route::get('projects/milestone/{id}/show',[ProjectController::class,'milestoneShow'])->name('projects.milestone.show');
    Route::get('projects/milestone/{id}/edit',[ProjectController::class,'milestoneEdit'])->name('projects.milestone.edit');
    Route::post('projects/milestone/{id}/update',[ProjectController::class,'milestoneUpdate'])->name('projects.milestone.update');
    Route::delete('projects/milestone/{id}',[ProjectController::class,'milestoneDestroy'])->name('projects.milestone.destroy');
    Route::delete('projects/{id}/file/delete/{fid}',[ProjectController::class,'fileDelete'])->name('projects.file.delete');


    Route::post('projects/invite/{id}/update',[ProjectController::class,'invite'])->name('projects.invite.update');

    Route::resource('bugstages', BugStageController::class);


    Route::post('project/comment/{tid}/file/{cid?}',[ProjectController::class,'commentStoreFile'])->name('comment.store.file');
    Route::delete('project/comment/{tid}/file/{fid}',[ProjectController::class,'commentDestroyFile'])->name('comment.destroy.file');
    Route::post('project/comment/{tid}/{cid?}',[ProjectController::class,'commentStore'])->name('comment.store');
    Route::delete('project/comment/{tid}/{cid}',[ProjectController::class,'commentDestroy'])->name('comment.destroy');
    Route::post('project/sub-task/update/{stid}',[ProjectController::class,'subTaskUpdate'])->name('subtask.update');
    Route::post('project/sub-task/{tid}/{cid?}',[ProjectController::class,'subTaskStore'])->name('subtask.store');
    Route::delete('project/sub-task/{stid}',[ProjectController::class,'subTaskDestroy'])->name('subtask.destroy');

    Route::post('projects/{id}/bug_comment/{tid}/file/{cid?}',[ProjectController::class,'bugStoreFile'])->name('bug.comment.store.file');
    Route::delete('projects/{id}/bug_comment/{tid}/file/{fid}',[ProjectController::class,'bugDestroyFile'])->name('bug.comment.destroy.file');
    Route::post('projects/{id}/bug_comment/{tid}/{cid?}',[ProjectController::class,'bugCommentStore'])->name('bug.comment.store');
    Route::delete('projects/{id}/bug_comment/{tid}/{cid}',[ProjectController::class,'bugCommentDestroy'])->name('bug.comment.destroy');
    Route::delete('projects/{id}/client/{uid}',[ProjectController::class,'clientDelete'])->name('projects.client.delete');
    Route::delete('projects/{id}/user/{uid}',[ProjectController::class,'userDelete'])->name('projects.user.delete');
    Route::delete('projects/{id}/vendor/{uid}',[ProjectController::class,'vendorDelete'])->name('projects.vendor.delete');

    // Project Report
    Route::resource('project_report', ProjectReportController::class);

    Route::post('reports-quarterly-cashflow/{id}', [ProjectReportController::class, 'quarterlyCashflow'])->name('projectreport.quarterly.cashflow');

    Route::post('project_report_data',[ProjectReportController::class,'ajax_data'])->name('projects.ajax');
    Route::post('project_report/tasks/{id}',[ProjectReportController::class,'ajax_tasks_report'])->name('tasks.report.ajaxdata');
    Route::get('report-view', [ReportController::class, 'index'])->name('report.view');
    Route::post('report-view', [ReportController::class, 'store'])->name('report.store');
    Route::put('report-view/{id}', [ReportController::class, 'update'])->name('report.update');
    Route::delete('report-view/{id}', [ReportController::class, 'destroy'])->name('report.destroy');
});
Route::middleware(['web'])->group(function ()
{
    Route::get('projects/{id}/file/{fid}',[ProjectController::class,'fileDownload'])->name('projects.file.download');

    Route::post('project/password/check/{id}/{lang?}', [ProjectController::class,'PasswordCheck'])->name('project.password.check');
    Route::get('project/shared/link/{id}/{lang?}', [ProjectController::class,'ProjectSharedLink'])->name('project.shared.link');
    Route::get('project/link/task/show/{tid}/',[ProjectController::class,'ProjectLinkTaskShow'])->name('Project.link.task.show');
    Route::get('projects/{id}/link/bug_report/{bid}/show',[ProjectController::class,'ProjectLinkbugReportShow'])->name('projects.link.bug.report.show');
});
