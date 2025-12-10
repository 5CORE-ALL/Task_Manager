<?php

namespace Workdo\Hrm\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'Hrm';
        $menu = $event->menu;
        $currentUser = \Auth::user();
        // $menu->add([
        //     'category' => 'General',
        //     'title' => __('HRM Dashboard'),
        //     'icon' => '',
        //     'name' => 'hrm-dashboard',
        //     'parent' => 'dashboard',
        //     'order' => 30,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'hrm.dashboard',
        //     'module' => $module,
        //     'permission' => 'hrm dashboard manage'
        // ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('HR'),
            'icon' => 'ti ti-3d-cube-sphere',
            'name' => 'hrm',
            'parent' => null,
            'order' => 450,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'hrm manage'
        ]);
        // $menu->add([
        //     'category' => 'HR',
        //     'title' => __('Team'),
        //     'icon' => '',
        //     'name' => 'employee',
        //     'parent' => 'hrm',
        //     'order' => 10,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'employee.index',
        //     'module' => $module,
        //     'permission' => 'employee manage'
        // ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Recruitment'),
             'icon' => 'ti ti-user-plus', //
            'name' => 'Recruitment',
            'parent' => 'hrm',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.Recruitment',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'employee manage'
        ]);
        //  $menu->add([
        //     'category' => 'HR',
        //     'title' => __('Resources'),
        //      'icon' => 'ti ti-user-plus', //
        //     'name' => 'Resources',
        //     'parent' => 'hrm',
        //     'order' => 10,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'redirect.resources',
        //     'attributes' => ['target' => '_blank'],
        //     'module' => $module,
        //     'permission' => 'employee manage'
        // ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Training'),
            'icon' => 'ti ti-graduate-cap', // <-- changed icon here
            'name' => 'Training',
            'parent' => 'hrm',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.Training',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'employee manage'
        ]);
        
        $menu->add([
            'category' => 'HR',
            'title' => __('Attendance'),
            'icon' => '',
            'name' => 'attendance',
            'parent' => 'hrm',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'attendance manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Mark Attendance'),
            'icon' => '',
            'name' => 'mark-attendance',
            'parent' => 'attendance',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'attendance.index',
            'module' => $module,
            'permission' => 'attendance manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Bulk Attendance'),
            'icon' => '',
            'name' => 'bulk-attendance',
            'parent' => 'attendance',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'attendance.bulkattendance',
            'module' => $module,
            'permission' => 'bulk attendance manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Manage Leave'),
            'icon' => '',
            'name' => 'manage-leave',
            'parent' => 'hrm',
            'order' => 25,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'leave.index',
            'module' => $module,
            'permission' => 'leave manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Our Policy'),
            'icon' => 'ti ti-graduate-cap', // <-- changed icon here
            'name' => 'Our Policy',
            'parent' => 'hrm',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.5policy',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'employee manage'
        ]);
           $menu->add([
            'category' => 'HR',
            'title' => __('Leave Policy'),
            'icon' => 'ti ti-graduate-cap', // <-- changed icon here
            'name' => 'Leave Policy',
            'parent' => 'hrm',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.policy',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'employee manage'
        ]);
                $menu->add([
            'category' => 'HR',
            'title' => __('Form'),
            'icon' => 'ti ti-forms',
            'name' => 'form',
            'parent' => 'hrm',
            'order' => 11,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.form',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'employee manage'
        ]);
        
        // $menu->add([
        //     'category' => 'HR',
        //     'title' => __('InterviewExit'),
        //     'icon' => 'ti ti-logout',
        //     'name' => 'interview-exit',
        //     'parent' => 'form',
        //     'order' => 10,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'exitinterview.index',
        //     'module' => $module,
        //     'permission' => 'employee manage'
        // ]);
        //  $menu->add([
        //     'category' => 'HR',
        //     'title' => __('7-Day Onboarding'),
        //     'icon' => 'ti ti-logout',
        //     'name' => '7-Day Onboarding',
        //     'parent' => 'form',
        //     'order' => 10,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'redirect.7-Day-Onboarding',
        //     'attributes' => ['target' => '_blank'],
        //     'module' => $module,
        //     'permission' => 'employee manage'
        // ]);
        //  $menu->add([
        //     'category' => 'HR',
        //     'title' => __('30-Day Onboarding'),
        //     'icon' => 'ti ti-logout',
        //     'name' => '30-Day Onboarding',
        //     'parent' => 'form',
        //     'order' => 10,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'redirect.30-Day-Onboarding',
        //     'attributes' => ['target' => '_blank'],
        //     'module' => $module,
        //     'permission' => 'employee manage'
        // ]);
        //  $menu->add([
        //     'category' => 'HR',
        //     'title' => __('Improvement Request'),
        //     'icon' => 'ti ti-logout',
        //     'name' => 'Improvement Request',
        //     'parent' => 'form',
        //     'order' => 10,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'redirect.improvement-request',
        //     'attributes' => ['target' => '_blank'],
        //     'module' => $module,
        //     'permission' => 'employee manage'
        // ]);
        // $menu->add([
        //     'category' => 'HR',
        //     'title' => __('Monthly Growth & Improvement'),
        //     'icon' => 'ti ti-logout',
        //     'name' => 'Monthly Growth & Improvement',
        //     'parent' => 'form',
        //     'order' => 10,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'redirect.Monthly-Growth',
        //     'attributes' => ['target' => '_blank'],
        //     'module' => $module,
        //     'permission' => 'employee manage'
        // ]);
            // Payroll Management Menu (moved under HR, after Leave Policy)
            $menu->add([
                'category' => 'HR',
                'title' => __('Payroll'),
                'icon' => 'ti ti-receipt',
                'name' => 'payroll-management',
                'parent' => 'hrm',
                'order' => 12, // comes after Leave Policy
                'ignore_if' => [],
                'depend_on' => [],
                'route' => '',
                'module' => $module,
                'permission' => ''
            ]);
            if ($currentUser && in_array(strtolower($currentUser->email), ['president@5core.com', 'hr@5core.com', 'software2@5core.com', 'inventory@5core.com'])) {
                $menu->add([
                    'category' => 'HR',
                    'title' => __('Manage Payroll'),
                    'icon' => 'ti ti-settings',
                    'name' => 'manage-payroll',
                    'parent' => 'payroll-management',
                    'order' => 10,
                    'ignore_if' => [],
                    'depend_on' => [],
                    'route' => 'payroll.index',
                    'attributes' => ['target' => '_blank'],
                    'module' => $module,
                    'permission' => ''
                ]);
                // $menu->add([
                //     'category' => 'HR',
                //     'title' => __('Archive Employee'),
                //     'icon' => 'ti ti-archive',
                //     'name' => 'archive-employee',
                //     'parent' => 'payroll-management',
                //     'order' => 12,
                //     'ignore_if' => [],
                //     'depend_on' => [],
                //     'route' => 'payroll.archive',
                //     'attributes' => ['target' => '_blank'],
                //     'module' => $module,
                //     'permission' => ''
                // ]);
            }
            $menu->add([
                'category' => 'HR',
                'title' => __('Salary Slip'),
                'icon' => 'ti ti-file-text',
                'name' => 'salary-slip',
                'parent' => 'payroll-management',
                'order' => 15,
                'ignore_if' => [],
                'depend_on' => [],
                'route' => 'payroll.salary-slip',
                'module' => $module,
                'permission' => ''
            ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('HR Admin'),
            'icon' => '',
            'name' => 'hr-admin',
            'parent' => 'hrm',
            'order' => 45,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'sidebar hr admin manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Award'),
            'icon' => '',
            'name' => 'award',
            'parent' => 'hr-admin',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'award.index',
            'module' => $module,
            'permission' => 'award manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Transfer'),
            'icon' => '',
            'name' => 'transfer',
            'parent' => 'hr-admin',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'transfer.index',
            'module' => $module,
            'permission' => 'transfer manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Resignation'),
            'icon' => '',
            'name' => 'resignation',
            'parent' => 'hr-admin',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'resignation.index',
            'module' => $module,
            'permission' => 'resignation manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Trip'),
            'icon' => '',
            'name' => 'trip',
            'parent' => 'hr-admin',
            'order' => 25,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'trip.index',
            'module' => $module,
            'permission' => 'travel manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Promotion'),
            'icon' => '',
            'name' => 'promotion',
            'parent' => 'hr-admin',
            'order' => 30,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'promotion.index',
            'module' => $module,
            'permission' => 'promotion manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Complaints'),
            'icon' => '',
            'name' => 'complaints',
            'parent' => 'hr-admin',
            'order' => 35,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'complaint.index',
            'module' => $module,
            'permission' => 'complaint manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Warning'),
            'icon' => '',
            'name' => 'warning',
            'parent' => 'hr-admin',
            'order' => 40,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'warning.index',
            'module' => $module,
            'permission' => 'warning manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Termination'),
            'icon' => '',
            'name' => 'termination',
            'parent' => 'hr-admin',
            'order' => 45,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'termination.index',
            'module' => $module,
            'permission' => 'termination manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Announcement'),
            'icon' => '',
            'name' => 'announcement',
            'parent' => 'hr-admin',
            'order' => 50,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'announcement.index',
            'module' => $module,
            'permission' => 'announcement manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Holidays'),
            'icon' => '',
            'name' => 'holidays',
            'parent' => 'hr-admin',
            'order' => 55,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'holiday.index',
            'module' => $module,
            'permission' => 'holiday manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Event'),
            'icon' => '',
            'name' => 'event',
            'parent' => 'hrm',
            'order' => 50,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'event.index',
            'module' => $module,
            'permission' => 'event manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Document'),
            'icon' => '',
            'name' => 'document',
            'parent' => 'hrm',
            'order' => 55,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'document.index',
            'module' => $module,
            'permission' => 'document manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Company Policy'),
            'icon' => '',
            'name' => 'company-policy',
            'parent' => 'hrm',
            'order' => 60,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'company-policy.index',
            'module' => $module,
            'permission' => 'companypolicy manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('System Setup'),
            'icon' => '',
            'name' => 'system-setup',
            'parent' => 'hrm',
            'order' => 65,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'branch.index',
            'module' => $module,
            'permission' => 'branch manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Report'),
            'icon' => '',
            'name' => 'hrm-report',
            'parent' => 'hrm',
            'order' => 70,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'sidebar hrm report manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Monthly Attendance'),
            'icon' => '',
            'name' => 'monthly-attendance',
            'parent' => 'hrm-report',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'report.monthly.attendance',
            'module' => $module,
            'permission' => 'attendance report manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Leave'),
            'icon' => '',
            'name' => 'leave',
            'parent' => 'hrm-report',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'report.leave',
            'module' => $module,
            'permission' => 'leave report manage'
        ]);

    }
}
