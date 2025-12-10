<?php

namespace App\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'Base';
        $menu = $event->menu;
        $menu->add([
            'category' => 'General',
            'title' => __('My Dashboard'),
            'icon' => 'home',
            'name' => 'dashboard',
            'parent' => null,
            'order' => 1,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'taskly.dashboard',
            'module' => $module,
            'permission' => 'taskly dashboard manage'
        ]);

        $menu->add([
            'category' => 'General',
            'title' => __('User Management'),
            'icon' => 'users',
            'name' => 'user-management',
            'parent' => null,
            'order' => 50,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'user manage'
        ]);
        $menu->add([
            'category' => 'General',
            'title' => __('User'),
            'icon' => '',
            'name' => 'user',
            'parent' => 'user-management',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'users.index',
            'module' => $module,
            'permission' => 'user manage'
        ]);
        $menu->add([
            'category' => 'General',
            'title' => __('Role'),
            'icon' => '',
            'name' => 'role',
            'parent' => 'user-management',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'roles.index',
            'module' => $module,
            'permission' => 'roles manage'
        ]);
        // $menu->add([
        //     'category' => 'Finance',
        //     'title' => __('Amazon'),
        //     'icon' => 'replace',
        //     'name' => 'Amazon',
        //     'parent' => '',
        //     'order' => 150,
        //     'ignore_if' => [],
        //     'depend_on' => ['Account','Taskly'],
        //     'route' => 'proposal.index',
        //     'module' => $module,
        //     'permission' => 'proposal manage'
        // ]);
        // $menu->add([
        //     'category' => 'Finance',
        //     'title' => __('Invoice'),
        //     'icon' => 'file-invoice',
        //     'name' => 'invoice',
        //     'parent' => '',
        //     'order' => 200,
        //     'ignore_if' => [],
        //     'depend_on' => ['Account','Taskly'],
        //     'route' => 'invoice.index',
        //     'module' => $module,
        // 'permission' => 'invoice manage'
        // ]);



        // $menu->add([
        //     'category' => 'Finance',
        //     'title' => __('Purchases'),
        //     'icon' => 'shopping-cart',
        //     'name' => 'purchases',
        //     'parent' => null,
        //     'order' => 250,
        //     'ignore_if' => [],
        //     'depend_on' => ['Account','Taskly'],
        //     'route' => '',
        //     'module' => $module,
        //     'permission' => 'purchase manage'
        // ]);
        //   $menu->add([
        //     'category' => 'Finance',
        //     'title' => __('Purchase'),
        //     'icon' => '',
        //     'name' => 'purchase',
        //     'parent' => 'purchases',
        //     'order' => 10,
        //     'ignore_if' => [],
        //     'depend_on' => ['Account','Taskly'],
        //     'route' => 'purchases.index',
        //     'module' => $module,
        //     'permission' => 'purchase manage'
        // ]);

        // $menu->add([
        //     'category' => 'Finance',
        //     'title' => __('Warehouse'),
        //     'icon' => '',
        //     'name' => 'warehouse',
        //     'parent' => 'purchases',
        //     'order' => 15,
        //     'ignore_if' => [],
        //     'depend_on' => ['Account','Taskly'],
        //     'route' => 'warehouses.index',
        //     'module' => $module,
        //     'permission' => 'warehouse manage'
        // ]);

        // $menu->add([
        //     'category' => 'Finance',
        //     'title' => __('Transfer'),
        //     'icon' => '',
        //     'name' => 'transfer',
        //     'parent' => 'purchases',
        //     'order' => 20,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'warehouses-transfer.index',
        //     'module' => $module,
        //     'permission' => 'warehouse manage'
        // ]);

        // $menu->add([
        //     'category' => 'Finance',
        //     'title' => __('Report'),
        //     'icon' => '',
        //     'name' => 'reports',
        //     'parent' => 'purchases',
        //     'order' => 25,
        //     'ignore_if' => [],
        //     'depend_on' => ['Account','Taskly'],
        //     'route' => '',
        //     'module' => $module,
        //     'permission' => 'report purchase'
        // ]);

        // $menu->add([
        //     'category' => 'Finance',
        //     'title' => __('Purchase Daily/Monthly Report'),
        //     'icon' => '',
        //     'name' => 'purchase-monthly',
        //     'parent' => 'reports',
        //     'order' => 10,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'reports.daily.purchase',
        //     'module' => $module,
        //     'permission' => 'report purchase'
        // ]);

        // $menu->add([
        //     'category' => 'Finance',
        //     'title' => __('Warehouse Report'),
        //     'icon' => '',
        //     'name' => 'warehouse-report',
        //     'parent' => 'reports',
        //     'order' => 20,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'reports.warehouse',
        //     'module' => $module,
        //     'permission' => 'report warehouse'
        // ]);
        // $menu->add([
        //     'category' => 'Settings',
        //     'title' => __('CRM'),
        //      'icon' => 'ti ti-table',
        //     'name' => 'CRM',
        //     'parent' => null,
        //     'order' => 1900,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'redirect.crm',
        //     'attributes' => ['target' => '_blank'],
        //     'module' => $module,
        //     'permission' => 'helpdesk ticket manage'
        // ]);


            $menu->add([
                'category' => 'Reports',
                'title' => __('DAR Reports'),
                'icon' => 'ti ti-report',
                'name' => 'dar-reports',
                'parent' => null,
                'order' => 1850,
                'ignore_if' => [],
                'depend_on' => [],
                'route' => 'dar.reports',
                'module' => $module,
                'permission' => ''
            ]);
                $menu->add([
            'category' => 'Reports',
            'title' => __('Activity'),
            'icon' => 'ti ti-activity',
            'name' => 'task-activity-report',
            'parent' => null,
            'order' => 1890,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'task.activity.report',
            'module' => $module,
            'permission' => ''
        ]);
        $menu->add([
            'category' => 'Settings',
            'title' => __('Content AI'),
            'icon' => 'ti ti-settings',
            'name' => 'Content AI',
            'parent' => null,
            'order' => 1900,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.AI',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'helpdesk ticket manage'
        ]);
                // MOM (Minutes of Meeting) Menu
        $menu->add([
            'category' => 'Communication',
            'title' => __('MOM'),
            'icon' => 'ti ti-calendar-event',
            'name' => 'mom',
            'parent' => null,
            'order' => 1350,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => ''
        ]);
        
        $menu->add([
            'category' => 'Communication',
            'title' => __('Create MOM'),
            'icon' => '',
            'name' => 'create-mom',
            'parent' => 'mom',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'mom.create',
            'module' => $module,
            'permission' => ''
        ]);
        
        $menu->add([
            'category' => 'Communication',
            'title' => __('Show MOM'),
            'icon' => '',
            'name' => 'show-mom',
            'parent' => 'mom',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'mom.index',
            'module' => $module,
            'permission' => ''
        ]);
                // Salary Management Menu
        // $menu->add([
        //     'category' => 'Communication',
        //     'title' => __('Salary'),
        //     'icon' => 'ti ti-currency-dollar',
        //     'name' => 'salary',
        //     'parent' => null,
        //     'order' => 1400,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => '',
        //     'module' => $module,
        //     'permission' => ''
        // ]);
        
        // $menu->add([
        //     'category' => 'Communication',
        //     'title' => __('Incentive'),
        //     'icon' => '',
        //     'name' => 'incentive',
        //     'parent' => 'salary',
        //     'order' => 10,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'salary.incentive',
        //     'module' => $module,
        //     'permission' => ''
        // ]);
        
        // $menu->add([
        //     'category' => 'Communication',
        //     'title' => __('Increment'),
        //     'icon' => '',
        //     'name' => 'increment',
        //     'parent' => 'salary',
        //     'order' => 20,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'salary.increment',
        //     'module' => $module,
        //     'permission' => ''
        // ]);
        
        // $menu->add([
        //     'category' => 'Communication',
        //     'title' => __('Incentive Records'),
        //     'icon' => '',
        //     'name' => 'incentive-records',
        //     'parent' => 'salary',
        //     'order' => 30,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'salary.incentive-records',
        //     'module' => $module,
        //     'permission' => ''
        // ]);
        
        // $menu->add([
        //     'category' => 'Communication',
        //     'title' => __('Salary Board'),
        //     'icon' => '',
        //     'name' => 'salary-board',
        //     'parent' => 'salary',
        //     'order' => 35,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'salary.board',
        //     'module' => $module,
        //     'permission' => ''
        // ]);
        
        // $menu->add([
        //     'category' => 'Communication',
        //     'title' => __('Increment Records'),
        //     'icon' => '',
        //     'name' => 'increment-records',
        //     'parent' => 'salary',
        //     'order' => 40,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'salary.increment-records',
        //     'module' => $module,
        //     'permission' => ''
        // ]);
         $menu->add([
            'category' => 'Settings',
            'title' => __('Customer Care'),
            'icon' => 'ti ti-headset',
            'name' => 'Customer Care',
            'parent' => null,
            'order' => 1900,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.Helpdesk',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'helpdesk ticket manage'
        ]);
         $menu->add([
            'category' => 'Settings',
            'title' => __('Forms'),
            'icon' => 'ti ti-forms', // ✏️ updated to forms icon
            'name' => 'Forms',
            'parent' => null,
            'order' => 1900,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.forms',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'helpdesk ticket manage'
        ]);
        $menu->add([
            'category' => 'Settings',
            'title' => __('Inventory Management'),
            'icon' => 'ti ti-clipboard-list',
            'name' => 'Inventory Management',
            'parent' => null,
            'order' => 1900,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.inventory',
            'module' => $module,
            'permission' => 'helpdesk ticket manage'
        ]);
    //   $menu->add([
    //         'category' => 'Settings',
    //         'title' => __('Listing Mirror'),
    //         'icon' => 'ti ti-table',
    //         'name' => 'Listing Mirror',
    //         'parent' => null,
    //         'order' => 1900,
    //         'ignore_if' => [],
    //         'depend_on' => [],
    //         'route' => 'redirect.listing',
    //         'module' => $module,
    //         'permission' => 'helpdesk ticket manage'
    //     ]);
        // $menu->add([
        //     'category' => 'Settings',
        //     'title' => __('Mandatory Requirements'),
        //     'icon' => 'ti ti-clipboard-list',
        //     'name' => 'Mandatory Requirements',
        //     'parent' => null,
        //     'order' => 1900,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'redirect.require',
        //     'module' => $module,
        //     'permission' => 'helpdesk ticket manage'
        // ]);
        $menu->add([
            'category' => 'Settings',
            'title' => __('Masters'),
            'icon' => 'ti ti-clipboard-list',
            'name' => 'Masters',
            'parent' => null,
            'order' => 1900,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.5-Core-Masters',
            'module' => $module,
            'permission' => 'helpdesk ticket manage'
        ]);
        
         $menu->add([
            'category' => 'Settings',
            'title' => __('LMP'),
            'icon' => 'ti ti-link',
            'name' => 'lmp-link',
            'parent' => null,
            'order' => 1900,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.lmp',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'helpdesk ticket manage'
        ]);
        
        $menu->add([
            'category' => 'Settings',
            'title' => __('AI Insight Ebay'),
            'icon' => 'ti ti-link',
            'name' => 'ai-insight-ebay',
            'parent' => null,
            'order' => 1900,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.aiinsight',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'helpdesk ticket manage'
        ]);
        
        $menu->add([
            'category' => 'Settings',
            'title' => __('Listing Master'),
            'icon' => 'ti ti-link',
            'name' => 'ai-insight-ebay',
            'parent' => null,
            'order' => 1900,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.listing_master',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'helpdesk ticket manage'
        ]);
        // $menu->add([
        //     'category' => 'Settings',
        //     'title' => __('Master Listing'),
        //     'icon' => 'ti ti-table',
        //     'name' => 'Master Listing',
        //     'parent' => null,
        //     'order' => 1900,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'redirect.Master',
        //     'attributes' => ['target' => '_blank'],
        //     'module' => $module,
        //     'permission' => 'helpdesk ticket manage'
        // ]);
        $menu->add([
            'category' => 'Settings',
            'title' => __('Marketing'),
            'icon' => 'ti ti-hierarchy',
            'name' => 'Marketing',
            'parent' => null,
            'order' => 1900,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.CRM',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'helpdesk ticket manage'
        ]);
// Add this after the Marketing menu item
$menu->add([
    'category' => 'Communication',
    'title' => __('Messenger'),
    'icon' => 'ti ti-messages', // Using a better icon
    'name' => 'messenger',
    'parent' => null,
    'order' => 1901, // This ensures it comes after Marketing (which is 1900)
    'ignore_if' => [],
    'depend_on' => [],
    'route' => 'chatify',
    'module' => $module,
    'permission' => 'user chat manage'
]);
        // $menu->add([
        //     'category' => 'Settings',
        //     'title' => __('Pricing analysis'),
        //     'icon' => 'ti ti-chart-bar', 
        //     'name' => 'Pricing analysis',
        //     'parent' => null,
        //     'order' => 1900,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'redirect.analysis',
        //     'attributes' => ['target' => '_blank'],
        //     'module' => $module,
        //     'permission' => 'helpdesk ticket manage'
        // ]);

         $menu->add([
            'category' => 'Settings',
            'title' => __('Purchase'),
            'icon' => 'ti ti-shopping-cart',
            'name' => 'Purchase',
            'parent' => null,
            'order' => 1900,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.Purchase',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'helpdesk ticket manage'
        ]);
        $menu->add([
            'category' => 'Communication',
            'title' => __('Team View'),
            'icon' => 'ti ti-users', // <- Add your icon class here
            'name' => 'Team View',
            'parent' => '',
            'order' => 1500,
            'ignore_if' => [],
            'depend_on' => [],
             'route' => 'employee.index',
            'module' => $module,
            'permission' => 'user chat manage'
        ]);

        
        // $menu->add([
        //     'category' => 'Settings',
        //     'title' => __('Corrective Actions'),
        //     'icon' => 'headphones',
        //     'name' => 'Corrective Actions',
        //     'parent' => null,
        //     'order' => 1900,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'helpdesk.index',
        //     'module' => $module,
        //     'permission' => 'helpdesk ticket manage'
        // ]);
        //  $menu->add([
        //     'category' => 'Settings',
        //     'title' => __('Google Meet'),
        //     'icon' => 'ti ti-device-computer-camera',
        //     'name' => 'Google Meet',
        //     'parent' => null,
        //     'order' => 1900,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => '',
        //     'module' => $module,
        //     'permission' => 'helpdesk ticket manage'
        // ]);
      $menu->add([
    'category' => 'Settings',
    'title' => __('Resources'),
    'icon' => 'ti ti-folder', // Parent icon
    'name' => 'Resources', // This is the key for the parent
    'parent' => null,
    'order' => 1900,
    'ignore_if' => [],
    'depend_on' => [],
    'route' => null, // No route for dropdown parent
    'module' => $module,
    'permission' => 'helpdesk ticket manage'
]);

$menu->add([
    'category' => 'Settings',
    'title' => __('Resources File'),
    'icon' => 'ti ti-world',
    'name' => 'Resources File',
    'parent' => 'Resources', 
    'order' => 1901,
    'ignore_if' => [],
    'depend_on' => [],
    'route' => 'redirect.resources',
    'attributes' => ['target' => '_blank'],
    'module' => $module,
    'permission' => 'helpdesk ticket manage'
]);

$menu->add([
    'category' => 'Settings',
    'title' => __('Resource Link'),
    'icon' => 'ti ti-link',
    'name' => 'Resource Link',
    'parent' => 'Resources', // Nested under 'Resources'
    'order' => 1902,
    'ignore_if' => [],
    'depend_on' => [],
    'route' => 'redirect.link',
    'module' => $module,
    'permission' => 'helpdesk ticket manage'
]);


        // $menu->add([
        //     'category' => 'Settings',
        //     'title' => __('Channel Masters'),
        //     'icon' => 'ti ti-list',
        //     'name' => 'Channel Masters',
        //     'parent' => null,
        //     'order' => 1900,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'redirect.Channel-Masters',
        //     'module' => $module,
        //     'permission' => 'helpdesk ticket manage'
        // ]);
        // $menu->add([
        //     'category' => 'Settings',
        //     'title' => __('Product Masters'),
        //     'icon' => 'ti ti-device-computer-camera',
        //     'name' => 'Product Masters',
        //     'parent' => null,
        //     'order' => 1900,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'redirect.Product-Masters',
        //     'attributes' => ['target' => '_blank'],
        //     'module' => $module,
        //     'permission' => 'helpdesk ticket manage'
        // ]);

        //  $menu->add([
        //     'category' => 'Settings',
        //     'title' => __('Resources'),
        //     'icon' => 'ti ti-link',
        //     'name' => 'Resources',
        //     'parent' => null,
        //     'order' => 1900,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'redirect.link',
        //     'attributes' => ['target' => '_blank'],
        //     'module' => $module,
        //     'permission' => 'helpdesk ticket manage'
        // ]);
        $menu->add([
            'category' => 'Settings',
            'title' => __('Shipping'),
            'icon' => 'ti ti-world',
            'name' => 'Shipping',
            'parent' => null,
            'order' => 1900,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.Shipping',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'helpdesk ticket manage'
        ]);
         $menu->add([
            'category' => 'Settings',
            'title' => __('Software Feedback'),
            'icon' => 'ti ti-world',
            'name' => 'Software Feedback',
            'parent' => null,
            'order' => 1900,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.software',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'helpdesk ticket manage'
        ]);
        

         $menu->add([
            'category' => 'Settings',
            'title' => __('Speed Test'),
            'icon' => 'ti ti-world',
            'name' => 'Speed Test',
            'parent' => null,
            'order' => 1900,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.Speedtest',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'helpdesk ticket manage'
        ]);
        
        $menu->add([
            'category' => 'Settings',
            'title' => __('Socialmedia'),
            'icon' => 'ti ti-brand-facebook',
            'name' => 'Socialmedia',
            'parent' => null,
            'order' => 1900,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.Socialmedia',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'helpdesk ticket manage'
        ]);
       
        
        
        // $menu->add([
        //     'category' => 'Settings',
        //     'title' => __('Recruitment'),
        //      'icon' => 'ti ti-user-plus', // updated icon
        //     'name' => 'Recruitment',
        //     'parent' => null,
        //     'order' => 1900,
        //     'ignore_if' => [],
        //     'depend_on' => [],
        //     'route' => 'redirect.Recruitment',
        //     'attributes' => ['target' => '_blank'],
        //     'module' => $module,
        //     'permission' => 'helpdesk ticket manage'
        // ]);

         $menu->add([
            'category' => 'Settings',
            'title' => __('Seo'),
            'icon' => 'ti ti-chart-bar', // new icon for SEO
            'name' => 'Seo',
            'parent' => null,
            'order' => 1900,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.seo',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'helpdesk ticket manage'
        ]);
                $menu->add([
            'category' => 'Settings',
            'title' => __('Warehouse'),
            'icon' => 'ti ti-building-warehouse',
            'name' => 'Warehouse',
            'parent' => null,
            'order' => 1900,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'redirect.Warehouse',
            'attributes' => ['target' => '_blank'],
            'module' => $module,
            'permission' => 'helpdesk ticket manage'
        ]);
        $menu->add([
            'category' => 'Settings',
            'title' => __('Settings'),
            'icon' => 'settings',
            'name' => 'settings',
            'parent' => null,
            'order' => 2000,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'setting manage'
        ]);
        $menu->add([
            'category' => 'Settings',
            'title' => __('System Settings'),
            'icon' => '',
            'name' => 'system-settings',
            'parent' => 'settings',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'settings.index',
            'module' => $module,
            'permission' => 'setting manage'
        ]);
        $menu->add([
            'category' => 'Settings',
            'title' => __('Setup Subscription Plan'),
            'icon' => '',
            'name' => 'setup-subscription-plan',
            'parent' => 'settings',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'plans.index',
            'module' => $module,
            'permission' => 'plan manage'
        ]);
        $menu->add([
            'category' => 'Settings',
            'title' => __('Referral Program'),
            'icon' => '',
            'name' => 'referral-program',
            'parent' => 'settings',
            'order' => 25,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'referral-program.company',
            'module' => $module,
            'permission' => 'referral program manage'
        ]);
        $menu->add([
            'category' => 'Settings',
            'title' => __('Order'),
            'icon' => '',
            'name' => 'order',
            'parent' => 'settings',
            'order' => 30,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'plan.order.index',
            'module' => $module,
            'permission' => 'plan orders'
        ]);
    }
}
