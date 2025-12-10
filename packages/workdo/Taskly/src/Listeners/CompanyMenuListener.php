<?php

namespace Workdo\Taskly\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{

    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'Taskly';
        $menu = $event->menu;
        // $menu->add([
        //     'category' => 'General',
        //     'title' => __('My Dashboard'),
        //     'icon' => '',
        //     'name' => 'taskly-dashboards',
        //     'parent' => '',
        //     'order' => 10,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'taskly.dashboard',
        //     'module' => $module,
        //     'permission' => 'taskly dashboard manage'
        // ]);
        $menu->add([
            'category' => 'Finance',
            'title' => __('Action Manager'),
            'icon' => 'square-check',
            'name' => 'projects',
            'parent' => null,
            'order' => 300,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'project manage'
        ]);
        $menu->add([
            'category' => 'Finance',
            'title' => __('Action'),
            'icon' => '',
            'name' => 'project',
            'parent' => 'projects',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'projecttask.list',
            'module' => $module,
            'permission' => 'task manage'
        ]);
         $menu->add([
            'category' => 'Finance',
            'title' => __('Automate Task'),
            'icon' => '',
            'name' => 'project',
            'parent' => 'projects',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'projecttask.automate.list',
            'module' => $module,
            'permission' => 'automate-task manage'
        ]);
               // Define specific email addresses that should have access to these menus
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
            // Add more email addresses as needed
        ];
        
        $currentUserEmail = auth()->user()->email ?? '';
        $hasEmailAccess = in_array($currentUserEmail, $allowedEmails);
        
        // Archive Done Task menu - show to ALL users
        $menu->add([
            'category' => 'Finance',
            'title' => __('DAR'),
            'icon' => '',
            'name' => 'project-report',
            'parent' => 'projects',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'projecttask.done.list',
            'module' => $module,
            'permission' => '' // No permission check - show to all users
        ]);

           $menu->add([
            'category' => 'Finance',
            'title' => __('Cron Job Report'),
            'icon' => '',
            'target'=> '_blank',
            'name' => 'project-report',
            'parent' => 'projects',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'project.scheduler.list',
            'module' => $module,
            'permission' => '' // No permission check - show to all users
        ]);
        
        // Check if user has permission OR is in the allowed email list for other restricted menus
        if (\Auth::user()->can('project report manage') || $hasEmailAccess) {
            $menu->add([
                'category' => 'Finance',
                'title' => __('Track Task'),
                'icon' => '',
                'name' => 'project',
                'parent' => 'projects',
                'order' => 20,
                'ignore_if' => [],
                'depend_on' => [],
                'route' => 'projecttask.track',
                'module' => $module,
                'permission' => '' // Remove permission check since we're handling it manually
            ]);
                $menu->add([
                    'category' => 'Finance',
                    'title' => __('Missed Task'),
                    'icon' => '',
                    'name' => 'missed-task',
                    'parent' => 'projects',
                    'order' => 21,
                    'ignore_if' => [],
                    'depend_on' => [],
                    'route' => 'missedTaskList.list',
                    'module' => $module,
                    'permission' => '' // No restriction - showing for all
                ]);
        }


        // $menu->add([
        //     'category' => 'Finance',
        //     'title' => __('Reviews'),
        //     'icon' => '',
        //     'name' => 'reviews',
        //     'parent' => 'projects',
        //     'order' => 21,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'reviews.index',
        //     'module' => $module,
        //     'permission' => 'task manage'
        // ]);
        // $menu->add([
        //     'category' => 'Finance',
        //     'title' => __('Done Clear'),
        //     'icon' => '',
        //     'name' => 'done-clear',
        //     'parent' => 'projects',
        //     'order' => 20,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'done-clear.index',
        //     'module' => $module,
        //     'permission' => '' // No restriction - showing for all
        // ]);
        $menu->add([
            'category' => 'Finance',
            'title' => __('Forms & reports'),
            'icon' => '',
            'name' => 'project-report-form',
            'parent' => 'projects',
            'order' => 21,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'report.view', // This should match your route name for the report blade
            'module' => $module,
            'permission' => 'project report manage'
        ]);
        $menu->add([
            'category' => 'Finance',
            'title' => __('System Setup'),
            'icon' => '',
            'name' => 'system-setup',
            'parent' => 'projects',
            'order' => 30,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'stages.index',
            'module' => $module,
            'permission' => 'taskly setup manage'
        ]);
    }
}
