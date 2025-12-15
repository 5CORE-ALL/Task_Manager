<?php

/**
 * Script to run on live server after deploying code changes
 * This will:
 * 1. Remove 'task create' permission from staff roles (to match original state)
 * 2. Ensure 'task create' permission exists for Ecommerce Manager role
 * 3. Clear cache
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Role;
use App\Models\Permission;

try {
    echo "=== Deploying Permission Changes ===\n\n";
    
    // Step 1: Remove 'task create' from staff roles
    echo "1. Removing 'task create' permission from staff roles...\n";
    $staffRoles = Role::where('name', 'staff')->get();
    $taskCreatePermission = Permission::where('name', 'task create')->first();
    
    if ($taskCreatePermission) {
        $removedCount = 0;
        foreach ($staffRoles as $staffRole) {
            if ($staffRole->hasPermission('task create')) {
                $staffRole->removePermission($taskCreatePermission);
                echo "   ✓ Removed from staff role (ID: {$staffRole->id}, created_by: {$staffRole->created_by})\n";
                $removedCount++;
            }
        }
        if ($removedCount > 0) {
            echo "   ✓ Removed 'task create' from {$removedCount} staff role(s)\n";
        } else {
            echo "   - No staff roles had 'task create' permission\n";
        }
    } else {
        echo "   ⚠ 'task create' permission not found in database\n";
    }
    
    // Step 2: Ensure Ecommerce Manager has 'task create' permission
    echo "\n2. Ensuring 'Ecommerce Manager' role has 'task create' permission...\n";
    $ecommerceManagerRole = Role::where('name', 'Ecommerce Manager')->orWhere('name', 'ecommerce manager')->first();
    
    if ($ecommerceManagerRole) {
        if (!$ecommerceManagerRole->hasPermission('task create')) {
            if ($taskCreatePermission) {
                $ecommerceManagerRole->givePermission($taskCreatePermission);
                echo "   ✓ Added 'task create' permission to Ecommerce Manager role\n";
            } else {
                echo "   ⚠ Cannot add permission - 'task create' permission not found\n";
            }
        } else {
            echo "   ✓ Ecommerce Manager role already has 'task create' permission\n";
        }
    } else {
        echo "   - Ecommerce Manager role not found (this is OK if not using this role)\n";
    }
    
    // Step 3: Clear cache
    echo "\n3. Clearing cache...\n";
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "   ✓ Cache cleared successfully\n";
    
    echo "\n=== Deployment Complete ===\n";
    echo "✓ All permission changes have been applied.\n";
    echo "\nNote: Users may need to log out and log back in for changes to take effect.\n";
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

