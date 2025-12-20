   <?php


use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BanktransferController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Company\SettingsController as CompanySettingsController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CustomDomainRequestController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\HelpdeskConversionController;
use App\Http\Controllers\HelpdeskTicketCategoryController;
use App\Http\Controllers\HelpdeskTicketController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseDebitNoteController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\DarController;
use App\Http\Controllers\IncentiveController;
use App\Http\Controllers\DeductionController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\SuperAdmin\SettingsController as SuperAdminSettingsController;
use App\Http\Controllers\WarehouseTransferController;
use App\Http\Controllers\WorkSpaceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReferralProgramController;
use App\Http\Controllers\DosController;
use App\Http\Controllers\DontsController;
use App\Http\Controllers\FlagRaiseController;
use App\Http\Controllers\TutorialController;
use App\Http\Controllers\TaskActivityReportController;
use App\Http\Controllers\DailyShippingChecklistController;
use App\Http\Controllers\PayroleController;
use App\Http\Controllers\PatchController;
use App\Http\Middleware\PayrollAdminMiddleware;
// use Illuminate\Support\Facades\Artisan;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Missed Task page

// Route::get('/make-storage-link', function () {
//     Artisan::call('storage:link');
//     return '✅ Storage link created successfully!';
// });
Route::get('projecttask/missed/list', [\Workdo\Taskly\Http\Controllers\MissedTaskController::class, 'index'])->name('projecttask.missed.list');

// Auth::routes();
require __DIR__ . '/auth.php';

// Google Authentication Routes - Adding directly to web.php to ensure they work
Route::get('auth/google/', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('/impersonate/{id}', [UserController::class, 'impersonate'])->name('impersonate');
Route::get('/stop-impersonate', [UserController::class, 'stopImpersonate'])->name('stop.impersonate');

// Debug route to test callback
Route::get('auth/google/callback-test', function() {
    return response()->json([
        'status' => 'Callback route is working',
        'request_data' => request()->all(),
        'time' => now()
    ]);
});

// Test Google route
Route::get('/test-google', function() {
    return response()->json(['status' => 'Google route working', 'time' => now()]);
});

// Test Google config
Route::get('/test-google-config', function() {
    return response()->json([
        'client_id' => config('services.google.client_id'),
        'redirect' => config('services.google.redirect'),
        'has_secret' => !empty(config('services.google.client_secret'))
    ]);
});

// custom domain code
Route::middleware('domain-check')->group(function () {
    Route::get('/register/{lang?}', [RegisteredUserController::class, 'create'])->name('register');
    Route::get('/login/{lang?}', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::get('/forgot-password/{lang?}', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::get('/verify-email/{lang?}', [EmailVerificationPromptController::class, '__invoke'])->name('verification.notice');

    // module page before login
    Route::get('add-on', [HomeController::class, 'Software'])->name('apps.software');
    Route::get('add-on/details/{slug}', [HomeController::class, 'SoftwareDetails'])->name('software.details');
    Route::get('pricing', [HomeController::class, 'Pricing'])->name('apps.pricing');
    Route::get('pricing/plans', [HomeController::class, 'PricingPlans'])->name('apps.pricing.plan');
    Route::get('pages', [HomeController::class, 'CustomPage'])->name('custompage');
    Route::get('/', [HomeController::class, 'index'])->name('start');
});
Route::middleware(['auth', 'verified'])->group(function () {

    //Role & Permission
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    //dashbord
    Route::get('/dashboard', [HomeController::class, 'Dashboard'])->name('dashboard');
    Route::get('/home', [HomeController::class, 'Dashboard'])->name('home');
    // Tutorial route
    Route::get('/tutorial', [App\Http\Controllers\TutorialController::class, 'index'])->name('tutorial'); 
    Route::post('/tutorials/store', [TutorialController::class, 'store'])->name('tutorials.store');
Route::get('/tutorials/by-category', [TutorialController::class, 'getByCategory'])->name('tutorials.byCategory');
Route::post('/tutorials/update/{id}', [TutorialController::class, 'update'])->name('tutorials.update');
Route::delete('/tutorials/delete/{id}', [TutorialController::class, 'destroy'])->name('tutorials.destroy');


    //ebay external link
    Route::get('redirect/lmp', function () {
    return redirect()->away('https://repricer.5coremanagement.com/dashboard');
})->name('redirect.lmp');

Route::get('redirect/aiinsight', function () {
    return redirect()->away('https://repricer.5coremanagement.com/automation/ebay');
})->name('redirect.aiinsight');

Route::get('redirect/listing_master', function () {
    return redirect()->away('https://listing.5coremanagement.com/');
})->name('redirect.listing_master');

    // Announcement routes
    Route::post('/announcement/store', [App\Http\Controllers\AnnouncementController::class, 'store'])->name('announcement.store');
    Route::get('/announcement/history', [App\Http\Controllers\AnnouncementController::class, 'history'])->name('announcement.history');

    // settings
    Route::resource('settings', SettingsController::class);
    Route::post('settings-save', [CompanySettingsController::class, 'store'])->name('settings.save');
    Route::post('company/settings-save', [CompanySettingsController::class, 'store'])->name('company.settings.save');
    Route::post('super-admin/settings-save', [SuperAdminSettingsController::class, 'store'])->name('super.admin.settings.save');
    Route::post('super-admin/system-settings-save', [SuperAdminSettingsController::class, 'SystemStore'])->name('super.admin.system.setting.store');
    Route::post('company/system-settings-save', [CompanySettingsController::class, 'SystemStore'])->name('company.system.setting.store');
    Route::post('company-setting-save', [CompanySettingsController::class, 'companySettingStore'])->name('company.setting.save');
    Route::post('comapny-currency-settings', [CompanySettingsController::class, 'saveCompanyCurrencySettings'])->name('company.setting.currency.settings');
    Route::post('company/update-note-value', [SuperAdminSettingsController::class, 'updateNoteValue'])->name('company.update.note.value');

    Route::post('email-settings-save', [SettingsController::class, 'mailStore'])->name('email.setting.store');
    Route::post('test-mail', [SettingsController::class, 'testMail'])->name('test.mail');
    Route::post('test-mail-send', [SettingsController::class, 'sendTestMail'])->name('test.mail.send');
    Route::post('email/getfields', [SettingsController::class, 'getfields'])->name('get.emailfields');
    Route::post('email-notification-settings-save', [SettingsController::class, 'mailNotificationStore'])->name('email.notification.setting.store');

    Route::post('cookie-settings-save', [SuperAdminSettingsController::class, 'CookieSetting'])->name('cookie.setting.store');
    Route::post('pusher-setting', [SuperAdminSettingsController::class, 'savePusherSettings'])->name('pusher.setting');
    Route::post('seo/setting/save', [SuperAdminSettingsController::class, 'seoSetting'])->name('seo.setting.save');
    Route::post('storage-settings-save', [SuperAdminSettingsController::class, 'storageStore'])->name('storage.setting.store');
    Route::post('ai/key/setting/save', [SuperAdminSettingsController::class, 'aiKeySettingSave'])->name('ai.key.setting.save');
    Route::post('currency-settings', [SuperAdminSettingsController::class, 'saveCurrencySettings'])->name('super.admin.currency.settings');
    Route::post('/update-note-value', [SuperAdminSettingsController::class, 'updateNoteValue'])->name('admin.update.note.value');

    Route::get('/setting/section/{module}/{method?}', [SettingsController::class, 'getSettingSection'])->name('setting.section.get');

    // bank-transfer
    Route::resource('bank-transfer-request', BanktransferController::class);
    Route::post('bank-transfer-setting', [BanktransferController::class, 'setting'])->name('bank.transfer.setting');
    Route::post('/bank/transfer/pay', [BanktransferController::class, 'planPayWithBank'])->name('plan.pay.with.bank');


    Route::get('invoice-bank-request/{id}', [BanktransferController::class, 'invoiceBankRequestEdit'])->name('invoice.bank.request.edit');
    Route::post('bank-transfer-request-edit/{id}', [BanktransferController::class, 'invoiceBankRequestupdate'])->name('invoice.bank.request.update');

    // domain Request Module
    Route::resource('custom_domain_request', CustomDomainRequestController::class);
    Route::get('custom_domain_request/{id}/{response}', [CustomDomainRequestController::class, 'acceptRequest'])->name('custom_domain_request.request');

    //users
    Route::resource('users', UserController::class);
    Route::get('users/list/view', [UserController::class, 'List'])->name('users.list.view');
    Route::get('profile', [UserController::class, 'profile'])->name('profile');
Route::get('/profile/edit', [UserController::class, 'editprofile'])->name('profile.edit');


    Route::post('edit-profile', [UserController::class, 'editprofile'])->name('edit.profile');
    Route::post('change-password', [UserController::class, 'updatePassword'])->name('update.password');
    Route::any('user-reset-password/{id}', [UserController::class, 'UserPassword'])->name('users.reset');
    Route::get('user-login/{id}', [UserController::class, 'LoginManage'])->name('users.login');
    Route::post('user-reset-password/{id}', [UserController::class, 'UserPasswordReset'])->name('user.password.update');
    Route::get('users/{id}/login-with-company', [UserController::class, 'LoginWithCompany'])->name('login.with.company');
    Route::get('company-info/{id}', [UserController::class, 'CompnayInfo'])->name('company.info');
    Route::post('user-unable', [UserController::class, 'UserUnable'])->name('user.unable');
    Route::get('user-verified/{id}', [UserController::class, 'verifeduser'])->name('user.verified');

    //User Log
    Route::get('users/logs/history', [UserController::class, 'UserLogHistory'])->name('users.userlog.history');
    Route::get('users/logs/{id}', [UserController::class, 'UserLogView'])->name('users.userlog.view');
    Route::delete('users/logs/destroy/{id}', [UserController::class, 'UserLogDestroy'])->name('users.userlog.destroy');

    // users import
    Route::get('users/import/export', [UserController::class, 'fileImportExport'])->name('users.file.import');
    Route::get('users/import/modal', [UserController::class, 'fileImportModal'])->name('users.import.modal');
    Route::post('users/import', [UserController::class, 'fileImport'])->name('users.import');
    Route::post('users/data/import/', [UserController::class, 'UserImportdata'])->name('users.import.data');


    // impersonating
    Route::get('login-with-company/exit', [UserController::class, 'ExitCompany'])->name('exit.company');

      //chatbot
    Route::get('chatbot', [ChatBotController::class, 'chatbot'])->name('chatbot');
    Route::get('Ai/chatbot/{chatbot_session_id}', [ChatBotController::class, 'Aichatbot'])->name('Ai.chatbot');
    Route::post('/chatbot/send', [ChatBotController::class, 'send'])->name('chatbot.send');
    Route::match(['get', 'post'], '/chatbot/upload-faq', [ChatBotController::class,'uploadAndTrainFAQ'])->name('chatbot.uploadFAQ');
    Route::post('/chatbot/clear', function () {
        session()->forget('chatbot_session_id');
        return response()->json(['status' => 'cleared']);
    })->name('chatbot.clear');
   Route::get('/load-more-chats', [ChatBotController::class, 'loadMoreChats']);
   Route::get('/users/lists/chatbot', [ChatBotController::class, 'userList'])->name('users.list');

    // Language
    Route::get('/lang/change/{lang}', [LanguageController::class, 'changeLang'])->name('lang.change');
    Route::get('langmanage/{lang?}/{module?}', [LanguageController::class, 'index'])->name('lang.index');
    Route::get('create-language', [LanguageController::class, 'create'])->name('create.language');
    Route::post('langs/{lang?}/{module?}', [LanguageController::class, 'storeData'])->name('lang.store.data');
    Route::post('disable-language', [LanguageController::class, 'disableLang'])->name('disablelanguage');
    Route::any('store-language', [LanguageController::class, 'store'])->name('store.language');
    Route::delete('/lang/{id}', [LanguageController::class, 'destroy'])->name('lang.destroy');
    // End Language
    // Payroll Management Routes
    Route::get('/payroll', [App\Http\Controllers\PayrollController::class, 'index'])->name('payroll.index')->middleware('payroll.admin');
    Route::get('/payroll/employees', [App\Http\Controllers\PayrollController::class, 'getEmployees'])->name('payroll.employees')->middleware('payroll.admin');
    Route::post('/payroll', [App\Http\Controllers\PayrollController::class, 'store'])->name('payroll.store')->middleware('payroll.admin');
    Route::put('/payroll/{id}', [App\Http\Controllers\PayrollController::class, 'update'])->name('payroll.update')->middleware('payroll.admin');
    Route::delete('/payroll/{id}', [App\Http\Controllers\PayrollController::class, 'destroy'])->name('payroll.destroy')->middleware('payroll.admin');
    Route::post('/payroll/{id}/mark-done', [App\Http\Controllers\PayrollController::class, 'markAsDone'])->name('payroll.mark-done')->middleware('payroll.admin');
    Route::post('/payroll/{id}/enable', [App\Http\Controllers\PayrollController::class, 'enablePayroll'])->name('payroll.enable')->middleware('payroll.admin');
    Route::post('/payroll/{id}/disable', [App\Http\Controllers\PayrollController::class, 'disablePayroll'])->name('payroll.disable')->middleware('payroll.admin');
    Route::post('/payroll/{id}/archive-contractual', [App\Http\Controllers\PayrollController::class, 'archiveAsContractual'])->name('payroll.archive-contractual')->middleware('payroll.admin');
    Route::post('/payroll/{id}/move-to-contractual', [App\Http\Controllers\PayrollController::class, 'moveToContractual'])->name('payroll.move-to-contractual')->middleware('payroll.admin');
    Route::post('/payroll/{id}/move-to-archive', [App\Http\Controllers\PayrollController::class, 'moveToArchive'])->name('payroll.move-to-archive')->middleware('payroll.admin');
    Route::get('/payroll/{id}/generate-pdf', [App\Http\Controllers\PayrollController::class, 'generatePDF'])->name('payroll.generate-pdf');
    Route::get('/payroll/{id}/test-pdf', [App\Http\Controllers\PayrollController::class, 'testPdfGeneration'])->name('payroll.test-pdf');
    Route::post('/payroll/{id}/send-email', [App\Http\Controllers\PayrollController::class, 'sendSalarySlipEmail'])->name('payroll.send-email')->middleware('payroll.admin');
    Route::post('/payroll/test-email', [App\Http\Controllers\PayrollController::class, 'testEmail'])->name('payroll.test-email')->middleware('payroll.admin');
    Route::get('/payroll/salary-slip', [App\Http\Controllers\PayrollController::class, 'salarySlip'])->name('payroll.salary-slip');
    Route::get('/payroll/archive', [App\Http\Controllers\PayrollController::class, 'archive'])->name('payroll.archive')->middleware('payroll.admin');
    // Route::post('/payroll/{id}/restore', [App\Http\Controllers\PayrollController::class, 'restoreFromArchive'])->name('payroll.restore')->middleware('payroll.admin');
    Route::post('/payroll/restore/{id}', [App\Http\Controllers\PayrollController::class, 'restore'])->name('payroll.restore');
    
    // API Routes for tabbed data
    Route::get('/payroll/api/active', [App\Http\Controllers\PayrollController::class, 'getActivePayrollData'])->name('payroll.api.active')->middleware('payroll.admin');
    Route::get('/payroll/api/archive', [App\Http\Controllers\PayrollController::class, 'getArchivePayrollData'])->name('payroll.api.archive')->middleware('payroll.admin');
    Route::get('/payroll/api/contractual', [App\Http\Controllers\PayrollController::class, 'getContractualPayrollData'])->name('payroll.api.contractual')->middleware('payroll.admin');
    Route::post('/payroll/update-approved-hours', [App\Http\Controllers\PayrollController::class, 'updateApprovedHours'])->name('payroll.update-approved-hours')->middleware('payroll.admin');

    Route::post('/payroll/fix-missing-sal-previous', [App\Http\Controllers\PayrollController::class, 'fixMissingSalPrevious'])->name('payroll.fix-missing-sal-previous')->middleware('payroll.admin');
    Route::post('/payroll/update-approval-status', [App\Http\Controllers\PayrollController::class, 'updateApprovalStatus'])->name('payroll.update-approval-status')->middleware('payroll.admin');
    Route::post('/payroll/update-extra', [App\Http\Controllers\PayrollController::class, 'updateExtra'])->name('payroll.update-extra')->middleware('payroll.admin');
    Route::post('/payroll/update-incentive', [App\Http\Controllers\PayrollController::class, 'updateIncentive'])->name('payroll.update-incentive')->middleware('payroll.admin');
    Route::post('/payroll/update-advance', [App\Http\Controllers\PayrollController::class, 'updateAdvance'])->name('payroll.update-advance')->middleware('payroll.admin');
    Route::post('/payroll/update-blogs-videos', [App\Http\Controllers\PayrollController::class, 'updateBlogsVideos'])->name('payroll.update-blogs-videos')->middleware('payroll.admin');
    Route::post('/payroll/update-rate', [App\Http\Controllers\PayrollController::class, 'updateRate'])->name('payroll.update-rate')->middleware('payroll.admin');
    Route::post('/payroll/update-salary-data', [App\Http\Controllers\PayrollController::class, 'updateSalaryData'])->name('payroll.update-salary-data')->middleware('payroll.admin');
    Route::post('/payroll/refresh-teamlogger-data', [App\Http\Controllers\PayrollController::class, 'refreshTeamLoggerData'])->name('payroll.refresh-teamlogger-data')->middleware('payroll.admin');
    Route::post('/payroll/fix-corrupted-data', [App\Http\Controllers\PayrollController::class, 'fixCorruptedData'])->name('payroll.fix-corrupted-data')->middleware('payroll.admin');
    Route::post('/payroll/update-bank1', [App\Http\Controllers\PayrollController::class, 'updateBank1'])->name('payroll.update-bank1')->middleware('payroll.admin');
    Route::post('/payroll/update-bank2', [App\Http\Controllers\PayrollController::class, 'updateBank2'])->name('payroll.update-bank2')->middleware('payroll.admin');
    Route::post('/payroll/update-up', [App\Http\Controllers\PayrollController::class, 'updateUp'])->name('payroll.update-up')->middleware('payroll.admin');
    Route::post('/payroll/copy-bank-details', [App\Http\Controllers\PayrollController::class, 'copyBankDetails'])->name('payroll.copy-bank-details')->middleware('payroll.admin');
    Route::post('/payroll/print-pdf', [App\Http\Controllers\PayrollController::class, 'printPayrollPDF'])->name('payroll.print-pdf')->middleware('payroll.admin');
        Route::post('/payroll/export-excel', [App\Http\Controllers\PayrollController::class, 'exportExcel'])->name('payroll.export-excel')->middleware('payroll.admin');

    // Debug route for testing ETC/ATC data
    Route::get('/payroll/debug-etc-atc', [App\Http\Controllers\PayrollController::class, 'debugETCATC'])->name('payroll.debug.etc.atc')->middleware('payroll.admin');
    Route::get('/payroll/debug-data', [App\Http\Controllers\PayrollController::class, 'debugPayrollData'])->name('payroll.debug.data')->middleware('payroll.admin');
    // Debug route to check payroll data
    Route::middleware([PayrollAdminMiddleware::class])
    ->prefix('payrole')
    ->group(function () {
        Route::get('/', [PayroleController::class, 'index']);
        });
    Route::get('/payroll/debug', function() {
        $workspaceId = session('workspace_id') ?? Auth::user()->workspace_id ?? 1;
        $payrolls = \App\Models\Payroll::where('workspace_id', $workspaceId)->get();
        return response()->json([
            'workspace_id' => $workspaceId,
            'payroll_count' => $payrolls->count(),
            'payrolls' => $payrolls
        ]);
    });
    // Workspace
    Route::resource('workspace', WorkSpaceController::class);
    Route::get('workspace/change/{id}', [WorkSpaceController::class, 'change'])->name('workspace.change');
    Route::post('workspace/check', [WorkSpaceController::class, 'workspaceCheck'])->name('workspace.check');

    // End Workspace

    // Plans
    Route::resource('plans', PlanController::class);

    Route::get('plan/list', [PlanController::class, 'PlanList'])->name('plan.list');
    Route::post('plan/store', [PlanController::class, 'PlanStore'])->name('plan.store');
    Route::get('plan/active', [PlanController::class, 'ActivePlans'])->name('active.plans');
    Route::get('upgrade-plan/{id}', [PlanController::class, 'upgradePlan'])->name('upgrade.plan');
    Route::get('plan/buy/{plan_id}/{user_id}', [PlanController::class, 'planDetail'])->name('plan.details');
    Route::get('modules/buy/{user_id}', [PlanController::class, 'moduleBuy'])->name('module.buy');
    Route::post('direct-assign-plan-to-user/{plan_id}/{user_id}', [PlanController::class, 'directAssignPlanToUser'])->name('assign.plan.user');
    Route::any('plan/package-data', [PlanController::class, 'PackageData'])->name('package.data');
    Route::get('plan/plan-buy/{id}', [PlanController::class, 'PlanBuy'])->name('plan.buy');
    Route::get('plan/plan-trial/{id}', [PlanController::class, 'PlanTrial'])->name('plan.trial');
    Route::get('plan/order', [PlanController::class, 'orders'])->name('plan.order.index');
    Route::get('add-one/detail/{id}', [PlanController::class, 'AddOneDetail'])->name('add-one.detail');
    Route::post('add-one/detail/save/{id}', [PlanController::class, 'AddOneDetailSave'])->name('add-one.detail.save');
    Route::post('update-plan-status', [PlanController::class, 'updateStatus'])->name('update.plan.status');
    Route::get('plan/refund/{id}/{user_id}', [PlanController::class, 'refund'])->name('order.refund');

    Route::post('company/settings-save', [CompanySettingsController::class, 'store'])->name('company.settings.save');
    Route::post('super-admin/settings-save', [SuperAdminSettingsController::class, 'store'])->name('super.admin.settings.save');
    Route::post('storage-settings-save', [SuperAdminSettingsController::class, 'storageStore'])->name('storage.setting.store');

    // Coupon
    Route::resource('coupons', CouponController::class);
    Route::get('/apply-coupon', [CouponController::class, 'applyCoupon'])->name('apply.coupon');
    // end Coupon

    // Module Install
    Route::get('modules/list', [ModuleController::class, 'index'])->name('module.index');
    Route::get('modules/add', [ModuleController::class, 'add'])->name('module.add');
    Route::post('install-modules', [ModuleController::class, 'install'])->name('module.install');
    Route::post('modules-enable', [ModuleController::class, 'enable'])->name('module.enable');
    Route::get('cancel/add-on/{name}/{user_id?}', [ModuleController::class, 'CancelAddOn'])->name('cancel.add.on');
    // End Module Install

    // Email Templates
    Route::resource('email-templates', EmailTemplateController::class);
    Route::get('email_template_lang/{id}/{lang?}', [EmailTemplateController::class, 'show'])->name('manage.email.language');
    Route::put('email_template_store/{pid}', [EmailTemplateController::class, 'storeEmailLang'])->name('store.email.language');
    Route::put('email_template_status/{id}', [EmailTemplateController::class, 'updateStatus'])->name('status.email.language');
    Route::resource('email_template', EmailTemplateController::class);
    // End Email Templates

    // helpdesk
    Route::resource('helpdesk', HelpdeskTicketController::class);
    Route::resource('helpdeskticket-category', HelpdeskTicketCategoryController::class);
    Route::get('helpdesk-tickets/search/{status?}', [HelpdeskTicketController::class, 'index'])->name('helpdesk-tickets.search');
    Route::post('helpdesk-ticket/getUser', [HelpdeskTicketController::class, 'getUser'])->name('helpdesk-tickets.getuser');
    Route::post('helpdesk-ticket/{id}/conversion', [HelpdeskConversionController::class, 'store'])->name('helpdesk-ticket.conversion.store');
    Route::post('helpdesk-ticket/{id}/note', [HelpdeskTicketController::class, 'storeNote'])->name('helpdesk-ticket.note.store');
    Route::delete('helpdesk-ticket-attachment/{tid}/destroy/{id}', [HelpdeskTicketController::class, 'attachmentDestroy'])->name('helpdesk-ticket.attachment.destroy');
    // End helpdesk


    Route::group(['middleware' => 'PlanModuleCheck:Account-Taskly'], function () {
        // invoice
        Route::post('invoice/customer', [InvoiceController::class, 'customer'])->name('invoice.customer');
        Route::post('invoice-attechment/{id}', [InvoiceController::class, 'invoiceAttechment'])->name('invoice.file.upload');
        Route::delete('invoice-attechment/destroy/{id}', [InvoiceController::class, 'invoiceAttechmentDestroy'])->name('invoice.attachment.destroy');
        Route::post('invoice/product', [InvoiceController::class, 'product'])->name('invoice.product');
        Route::get('invoice/{id}/duplicate', [InvoiceController::class, 'duplicate'])->name('invoice.duplicate');
        Route::get('invoice/{id}/recurring', [InvoiceController::class, 'recurring'])->name('invoice.recurring');
        Route::get('invoice/items', [InvoiceController::class, 'items'])->name('invoice.items');
        Route::post('invoice/product/destroy', [InvoiceController::class, 'productDestroy'])->name('invoice.product.destroy');
        Route::get('invoice/grid/view', [InvoiceController::class, 'Grid'])->name('invoice.grid.view');
        Route::resource('invoice', InvoiceController::class)->except(['create']);
        Route::get('invoice/create/{cid}', [InvoiceController::class, 'create'])->name('invoice.create');
        Route::get('/invoice/pay/{invoice}', [InvoiceController::class, 'payinvoice'])->name('pay.invoice');
        Route::get('invoice/{id}/sent', [InvoiceController::class, 'sent'])->name('invoice.sent');
        Route::get('invoice/{id}/resent', [InvoiceController::class, 'resent'])->name('invoice.resent');
        Route::get('invoice/{id}/payment/reminder', [InvoiceController::class, 'paymentReminder'])->name('invoice.payment.reminder');
        Route::get('invoice/pdf/{id}', [InvoiceController::class, 'invoice'])->name('invoice.pdf');
        Route::get('invoice/{id}/payment', [InvoiceController::class, 'payment'])->name('invoice.payment');
        Route::post('invoice/{id}/payment/store', [InvoiceController::class, 'createPayment'])->name('invoice.payment.store');
        Route::post('invoice/{id}/payment/{pid}/', [InvoiceController::class, 'paymentDestroy'])->name('invoice.payment.destroy');
        Route::get('invoice/{id}/send', [InvoiceController::class, 'customerInvoiceSend'])->name('invoice.send');
        Route::post('invoice/{id}/send/mail', [InvoiceController::class, 'customerInvoiceSendMail'])->name('invoice.send.mail');
        Route::post('invoice/section/type', [InvoiceController::class, 'InvoiceSectionGet'])->name('invoice.section.type');
        Route::get('delivery-form/pdf/{id}', [InvoiceController::class, 'pdf'])->name('delivery-form.pdf');

        Route::post('/get-invoice-customers', [InvoiceController::class, 'getInvoiceCustomers'])->name('invoice.customers');

        Route::post('invoice-item-detail', [InvoiceController::class, 'getInvoicItemeDetail'])->name('newspaper.invoice.item.details');

        Route::post('invoice/course', [InvoiceController::class, 'course'])->name('invoice.course');
        Route::get('invoice/status/view', [InvoiceController::class, 'InvocieStatus'])->name('invoice.status.view');

        // Proposal
        Route::post('proposal-attechment/{id}', [ProposalController::class, 'proposalAttechment'])->name('proposal.file.upload');
        Route::delete('proposal-attechment/destroy/{id}', [ProposalController::class, 'proposalAttechmentDestroy'])->name('proposal.attachment.destroy');
        Route::post('proposal/customer', [ProposalController::class, 'customer'])->name('proposal.customer');
        Route::post('proposal/product', [ProposalController::class, 'product'])->name('proposal.product');
        Route::get('proposal/{id}/convert', [ProposalController::class, 'convert'])->name('proposal.convert');
        Route::get('proposal/{id}/duplicate', [ProposalController::class, 'duplicate'])->name('proposal.duplicate');
        Route::get('proposal/items', [ProposalController::class, 'items'])->name('proposal.items');
        Route::post('proposal/product/destroy', [ProposalController::class, 'productDestroy'])->name('proposal.product.destroy');
        Route::resource('proposal', ProposalController::class)->except(['create']);
        Route::get('proposal/grid/view', [ProposalController::class, 'Grid'])->name('proposal.grid.view');
        Route::get('proposal/create/{cid}', [ProposalController::class, 'create'])->name('proposal.create');
        Route::get('proposal/{id}/status/change', [ProposalController::class, 'statusChange'])->name('proposal.status.change');
        Route::get('proposal/{id}/resent', [ProposalController::class, 'resent'])->name('proposal.resent');
        Route::post('proposal/section/type', [ProposalController::class, 'ProposalSectionGet'])->name('proposal.section.type');
        Route::get('proposal/{id}/sent', [ProposalController::class, 'sent'])->name('proposal.sent');
        Route::get('proposal/stats/view', [ProposalController::class, 'ProposalQuickStats'])->name('proposal.stats.view');

        // purchase
        Route::resource('purchases', PurchaseController::class)->except(['create']);
        Route::get('purchases-grid', [PurchaseController::class, 'grid'])->name('purchases.grid');
        Route::post('purchases/items', [PurchaseController::class, 'items'])->name('purchases.items');
        Route::get('purchases/{id}/payment', [PurchaseController::class, 'payment'])->name('purchases.payment');
        Route::post('purchases/{id}/payment/store', [PurchaseController::class, 'createPayment'])->name('purchases.payment.store');
        Route::post('purchases/{id}/payment/{pid}/destroy', [PurchaseController::class, 'paymentDestroy'])->name('purchases.payment.destroy');

        Route::post('purchases/product/destroy', [PurchaseController::class, 'productDestroy'])->name('purchases.product.destroy');
        Route::post('purchases/vender', [PurchaseController::class, 'vender'])->name('purchases.vender');
        Route::post('purchases/product', [PurchaseController::class, 'product'])->name('purchases.product');
        Route::get('purchases/create/{cid}', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::get('purchases/{id}/sent', [PurchaseController::class, 'sent'])->name('purchases.sent');
        Route::get('purchases/{id}/resent', [PurchaseController::class, 'resent'])->name('purchases.resent');


        Route::get('purchases/{id}/debit-note', [PurchaseDebitNoteController::class, 'create'])->name('purchases.debit.note')->middleware(
            [
                'auth',
            ]
        );
        Route::post('purchases/{id}/debit-note/store', [PurchaseDebitNoteController::class, 'store'])->name('purchases.debit.note.store')->middleware(
            [
                'auth',
            ]
        );
        Route::get('purchases/{id}/debit-note/edit/{cn_id}', [PurchaseDebitNoteController::class, 'edit'])->name('purchases.edit.debit.note')->middleware(
            [
                'auth',
            ]
        );
        Route::post('purchases/{id}/debit-note/update/{cn_id}', [PurchaseDebitNoteController::class, 'update'])->name('purchases.update.debit.note')->middleware(
            [
                'auth',
            ]
        );
        Route::delete('purchases/{id}/debit-note/delete/{cn_id}', [PurchaseDebitNoteController::class, 'destroy'])->name('purchases.delete.debit.note')->middleware(
            [
                'auth',
            ]
        );

        Route::post('purchase/{id}/file', [PurchaseController::class, 'fileUpload'])->name('purchases.files.upload')->middleware(['auth']);
        Route::delete("purchase/{id}/destroy", [PurchaseController::class, 'fileUploadDestroy'])->name("purchases.attachment.destroy")->middleware(['auth']);
        //warehouse

        Route::resource('warehouses', WarehouseController::class)->middleware(['auth',]);

        //warehouse import
        Route::get('warehouses/import/export', [WarehouseController::class, 'fileImportExport'])->name('warehouses.file.import')->middleware(['auth']);
        Route::post('warehouses/import', [WarehouseController::class, 'fileImport'])->name('warehouses.import')->middleware(['auth']);
        Route::get('warehouses/import/modal', [WarehouseController::class, 'fileImportModal'])->name('warehouses.import.modal')->middleware(['auth']);
        Route::post('warehouses/data/import/', [WarehouseController::class, 'warehouseImportdata'])->name('warehouses.import.data')->middleware(['auth']);

        Route::get('productservice/{id}/detail', [WarehouseController::class, 'warehouseDetail'])->name('productservices.detail');

        //warehouse-transfer
        Route::resource('warehouses-transfer', WarehouseTransferController::class)->middleware(['auth']);
        Route::post('warehouses-transfer/getproduct', [WarehouseTransferController::class, 'getproduct'])->name('warehouses-transfer.getproduct')->middleware(['auth']);
        Route::post('warehouses-transfer/getquantity', [WarehouseTransferController::class, 'getquantity'])->name('warehouses-transfer.getquantity')->middleware(['auth']);

        //Reports
        Route::get('reports-warehouses', [ReportController::class, 'warehouseReport'])->name('reports.warehouse')->middleware(['auth']);
        Route::get('reports-daily-purchases', [ReportController::class, 'purchaseDailyReport'])->name('reports.daily.purchase')->middleware(['auth']);
        Route::get('reports-monthly-purchases', [ReportController::class, 'purchaseMonthlyReport'])->name('reports.monthly.purchase')->middleware(['auth']);
    });
    // invoices template setting save
    Route::post('/invoices/template/setting', [InvoiceController::class, 'saveTemplateSettings'])->name('invoice.template.setting');
    Route::get('/invoices/preview/{template}/{color}', [InvoiceController::class, 'previewInvoice'])->name('invoice.preview');

    // proposal template setting save
    Route::get('/proposal/preview/{template}/{color}', [ProposalController::class, 'previewInvoice'])->name('proposal.preview');
    Route::post('/proposal/template/setting', [ProposalController::class, 'saveTemplateSettings'])->name('proposal.template.setting');

    // purchase template setting save
    Route::get('purchases/preview/{template}/{color}', [PurchaseController::class, 'previewPurchase'])->name('purchases.preview');
    Route::post('/purchase/template/setting', [PurchaseController::class, 'savePurchaseTemplateSettings'])->name('purchases.template.setting');


    //notification
    Route::resource('notification-template', NotificationController::class);
    Route::get('notification-template/{id}/{lang?}', [NotificationController::class, 'show'])->name('manage.notification.language');
    Route::post('notification-template/{pid}', [NotificationController::class, 'storeNotificationLang'])->name('store.notification.language');

    // Referral Program
    Route::resource('referral-program', ReferralProgramController::class);
    Route::get('referral-program-company', [ReferralProgramController::class, 'companyIndex'])->name('referral-program.company');
    Route::get('request-amount-sent/{id}', [ReferralProgramController::class, 'requestedAmountSent'])->name('request.amount.sent');
    Route::post('request-amount-store/{id}', [ReferralProgramController::class, 'requestedAmountStore'])->name('request.amount.store');
    Route::get('request-amount-cancel/{id}', [ReferralProgramController::class, 'requestCancel'])->name('request.amount.cancel');
    Route::get('request-amount/{id}/{status}', [ReferralProgramController::class, 'requestedAmount'])->name('amount.request');

    // language import & export
    Route::get('export/lang/json',[LanguageController::class,'exportLangJson'])->name('export.lang.json');
    Route::get('import/lang/json/upload',[LanguageController::class,'importLangJsonUpload'])->name('import.lang.json.upload');
    Route::post('import/lang/json',[LanguageController::class,'importLangJson'])->name('import.lang.json');
    
    // Salary Management Routes
    Route::get('/salary/incentive', [App\Http\Controllers\SalaryController::class, 'incentive'])->name('salary.incentive');
    Route::get('/salary/increment', [App\Http\Controllers\SalaryController::class, 'increment'])->name('salary.increment');
    
    // Salary Form Submission Routes
    Route::post('/salary/incentive', [App\Http\Controllers\SalaryController::class, 'submitIncentive'])->name('incentive.submit');
    Route::post('/salary/increment', [App\Http\Controllers\SalaryController::class, 'storeProposal'])->name('salary-proposal.store');

    // Salary Records Pages
    Route::get('/salary/incentive-records', [App\Http\Controllers\SalaryController::class, 'incentiveRecords'])->name('salary.incentive-records');
    Route::get('/salary/increment-records', [App\Http\Controllers\SalaryController::class, 'incrementRecords'])->name('salary.increment-records');
    Route::get('/salary/board', [App\Http\Controllers\SalaryController::class, 'salaryBoard'])->name('salary.board');

    // Incentive CRUD routes
    Route::put('/salary/incentive/{id}/update', [App\Http\Controllers\SalaryController::class, 'updateIncentive'])->name('salary.incentive.update');
    Route::put('/salary/incentive/{id}/status', [App\Http\Controllers\SalaryController::class, 'updateIncentiveStatus'])->name('salary.incentive.status');
    Route::delete('/salary/incentive/{id}/delete', [App\Http\Controllers\SalaryController::class, 'deleteIncentive'])->name('salary.incentive.delete');

    // Increment CRUD routes
    Route::put('/salary/increment/{id}/update', [App\Http\Controllers\SalaryController::class, 'updateIncrement'])->name('salary.increment.update');
    Route::put('/salary/increment/{id}/status', [App\Http\Controllers\SalaryController::class, 'updateIncrementStatus'])->name('salary.increment.status');
    Route::delete('/salary/increment/{id}/delete', [App\Http\Controllers\SalaryController::class, 'deleteIncrement'])->name('salary.increment.delete');

    // Salary Board Payment Routes
    Route::put('/salary/board/{id}/payment', [App\Http\Controllers\SalaryController::class, 'markPaymentDone'])->name('salary.board.payment');

    Route::get('/salary/get-employee-data/{id}', [App\Http\Controllers\SalaryController::class, 'getEmployeeData'])->name('salary.get-employee-data');
});

// Simple AJAX Route for Employee Data (no middleware)
// Working API route for employee data
Route::get('/api/employee/{id}', function($id) {
    $employee = \Workdo\Hrm\Entities\Employee::find($id);
    
    if (!$employee) {
        return response()->json(['success' => false, 'message' => 'Employee not found']);
    }
    
    $departmentName = 'No Department';
    if ($employee->department_id) {
        $department = \Workdo\Hrm\Entities\Department::find($employee->department_id);
        if ($department) {
            $departmentName = $department->name;
        }
    }
    
    return response()->json([
        'success' => true,
        'name' => $employee->name,
        'department' => $departmentName
    ]);
});

// Employee Data API Route (public access for AJAX)
Route::get('/api/get-employee-data/{id}', function($id) {
    try {
        $employee = \Workdo\Hrm\Entities\Employee::find($id);
        $department = null;
        
        if ($employee && $employee->department_id) {
            $dept = \Workdo\Hrm\Entities\Department::find($employee->department_id);
            $department = $dept ? $dept->name : 'No Department';
        } else {
            $department = 'No Department';
        }
        
        return response()->json([
            'success' => true,
            'department' => $department
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'department' => 'Error loading'
        ]);
    }
});

// AJAX Route for Employee Data (outside middleware)
Route::get('/salary/get-employee-data/{id}', [App\Http\Controllers\SalaryController::class, 'getEmployeeData'])->name('salary.get-employee-data');
// Review routes
Route::middleware(['auth'])->group(function () {
    Route::get('/reviews', [App\Http\Controllers\ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/employees', [App\Http\Controllers\ReviewController::class, 'getEmployees'])->name('reviews.employees');
    Route::delete('/reviews/{id}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Exit Interview routes
Route::middleware(['auth'])->group(function () {
    Route::get('/exitinterview', [App\Http\Controllers\ExitInterviewController::class, 'index'])->name('exitinterview.index');
    Route::post('/exitinterview', [App\Http\Controllers\ExitInterviewController::class, 'store'])->name('exitinterview.store');
});

// DO's and DON'T Routes (WORKING VERSION)
Route::middleware(['auth'])->group(function () {
    Route::post('/dos', [DosController::class, 'store'])->name('dos.store');
    Route::get('/dos', [DosController::class, 'index'])->name('dos.index');
    Route::delete('/dos/{id}', [DosController::class, 'destroy'])->name('dos.destroy');
    
    Route::post('/donts', [DontsController::class, 'store'])->name('donts.store');
    Route::get('/donts', [DontsController::class, 'index'])->name('donts.index');
    Route::delete('/donts/{id}', [DontsController::class, 'destroy'])->name('donts.destroy');
    
    // DO's and DON'T Report Page
    Route::get('/dos-donts/report', [DosController::class, 'report'])->name('dos-donts.report');
});

// ADDED: Incentive routes
Route::middleware(['auth'])->group(function () {
    Route::get('/incentives', [IncentiveController::class, 'index'])->name('incentives.index');
    Route::post('/incentives', [IncentiveController::class, 'store'])->name('incentives.store');
    Route::get('/incentives/{id}', [IncentiveController::class, 'show'])->name('incentives.show');
    Route::delete('/incentives/{id}', [IncentiveController::class, 'destroy'])->name('incentives.destroy');
    Route::get('/check-incentives', [IncentiveController::class, 'checkIncentives'])->name('check.incentives');
    Route::post('/mark-notification-read', [IncentiveController::class, 'markNotificationRead'])->name('mark.notification.read');
});

// ADDED: Deduction routes
Route::middleware(['auth'])->group(function () {
    Route::get('/deductions', [DeductionController::class, 'index'])->name('deductions.index');
    Route::post('/deductions/store', [DeductionController::class, 'store'])->name('deductions.store');
    Route::get('/deductions/{id}', [DeductionController::class, 'show'])->name('deductions.show');
    Route::delete('/deductions/{id}', [DeductionController::class, 'destroy'])->name('deductions.destroy');
    Route::get('/check-deductions', [DeductionController::class, 'checkDeductions'])->name('check.deductions');
    Route::post('/mark-deduction-notification-read', [DeductionController::class, 'markNotificationRead'])->name('mark.deduction.notification.read');
});

// DAR (Daily Activity Report) routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dar', [DarController::class, 'index'])->name('dar.index');
    Route::post('/dar', [DarController::class, 'store'])->name('dar.store');
    Route::get('/dar/{id}', [DarController::class, 'show'])->name('dar.show');
    Route::delete('/dar/{id}', [DarController::class, 'destroy'])->name('dar.destroy');
    
    // DAR Reports - Only for admin and specific users
    Route::get('/dar/reports/view', [DarController::class, 'reports'])->name('dar.reports');
    Route::post('/dar/reports/data', [DarController::class, 'getReportData'])->name('dar.reports.data');
    Route::post('dar/reports/summary', [DarController::class, 'reportsSummary'])->name('dar.reports.summary');
});

// Performance Management routes
Route::middleware(['auth'])->group(function () {
    Route::get('/performance-management', [App\Http\Controllers\PerformanceManagementController::class, 'index'])->name('performance.index');
    Route::post('/performance-management/generate', [App\Http\Controllers\PerformanceManagementController::class, 'generatePerformance'])->name('performance.generate');
    Route::get('/performance-management/report/{id}', [App\Http\Controllers\PerformanceManagementController::class, 'showReport'])->name('performance.report');
    Route::post('/performance-management/feedback', [App\Http\Controllers\PerformanceManagementController::class, 'storeFeedback'])->name('performance.feedback.store');
    Route::get('/performance-management/chart-data/{id}', [App\Http\Controllers\PerformanceManagementController::class, 'getChartData'])->name('performance.chart.data');
    
    // Temporary route to clear menu cache
    Route::get('/clear-menu-cache', function() {
        if (Auth::check()) {
            $userId = Auth::id();
            \Illuminate\Support\Facades\Cache::forget('sidebar_menu_' . $userId);
            // Also clear for company users
            if (Auth::user()->type == 'company') {
                $userIds = \App\Models\User::where('created_by', Auth::id())->pluck('id');
                foreach ($userIds as $id) {
                    \Illuminate\Support\Facades\Cache::forget('sidebar_menu_' . $id);
                }
            }
            return redirect()->back()->with('success', 'Menu cache cleared! Please refresh the page.');
        }
        return redirect()->route('login');
    })->name('clear.menu.cache');
});
Route::get('module/reset', [ModuleController::class, 'ModuleReset'])->name('module.reset');
Route::post('guest/module/selection', [ModuleController::class, 'GuestModuleSelection'])->name('guest.module.selection');

// cookie
Route::get('cookie/consent', [SuperAdminSettingsController::class, 'CookieConsent'])->name('cookie.consent');

// cache
Route::get('/config-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    return redirect()->back()->with('success', 'Cache Clear Successfully');
})->name('config.cache');

//helpdesk
Route::post('helpdesk-ticket/{id}', [HelpdeskTicketController::class, 'reply'])->name('helpdesk-ticket.reply');
Route::get('helpdesk-ticket-show/{id}', [HelpdeskTicketController::class, 'show'])->name('helpdesk.view');

// invoice
Route::get('/invoice/pay/{invoice}', [InvoiceController::class, 'payinvoice'])->name('pay.invoice');
Route::get('invoice/pdf/{id}', [InvoiceController::class, 'invoice'])->name('invoice.pdf');
Route::post('/bank/transfer/invoice', [BanktransferController::class, 'invoicePayWithBank'])->name('invoice.pay.with.bank');

// proposal
Route::get('/proposal/pay/{proposal}', [ProposalController::class, 'payproposal'])->name('pay.proposalpay');
Route::get('proposal/pdf/{id}', [ProposalController::class, 'proposal'])->name('proposal.pdf');


// purchase
Route::get('/vendor/purchases/{id}/', [PurchaseController::class, 'purchaseLink'])->name('purchases.link.copy');
Route::get('/vend0r/bill/{id}/', [PurchaseController::class, 'invoiceLink'])->name('bill.link.copy')->middleware(['auth']);
Route::get('purchases/pdf/{id}', [PurchaseController::class, 'purchase'])->name('purchases.pdf');


Route::get('composer/json',function(){
    $path = base_path('packages/workdo');
    $modules = \Illuminate\Support\Facades\File::directories($path);

    $moduleNames = array_map(function($dir) {
        return basename($dir);
    }, $modules);

    $require = '';
    $repo = '';
    foreach($moduleNames as $module){
        $packageName = preg_replace('/([a-z])([A-Z])/', '$1-$2', $module);
        $require .= '"workdo/'.strtolower($packageName).'": "dev-testing",';
        $repo .= '{
            "type": "path",
            "url": "packages/workdo/'.$module.'"
        },';
    }
    return $require . '<br><br><br>' . $repo;
});

// redirection routes all 

// routes/web.php
Route::get('/redirect/5-Core-Masters', function () {
    return redirect()->away('https://inventory.5coremanagement.com');
})->name('redirect.5-Core-Masters');

// routes/web.php
Route::get('/redirect/Channel-Masters', function () {
    return redirect()->away('https://inventory.5coremanagement.com/channel/channels/channel-masters');
})->name('redirect.Channel-Masters');

Route::get('/redirect/Product-Masters', function () {
    return redirect()->away('http://inventory.5coremanagement.com/product/productmaster');
})->name('redirect.Product-Masters');

Route::get('/redirect/Speedtest', function () {
    return redirect()->away('http://speedtest.5coremanagement.com/');
})->name('redirect.Speedtest');

Route::get('/redirect/Shipping', function () {
    return redirect()->away('https://ship-hub-production.5coremanagement.com/admin/login');
})->name('redirect.Shipping');

Route::get('/redirect/Helpdesk', function () {
    return redirect()->away('https://helpdesk.5coremanagement.com/login');
})->name('redirect.Helpdesk');

Route::get('/redirect/Warehouse', function () {
    return redirect()->away('http://warehouse.5coremanagement.com/');
})->name('redirect.Warehouse');

Route::get('/redirect/Socialmedia', function () {
    return redirect()->away('http://socialmedia.5coremanagement.com/admin');
})->name('redirect.Socialmedia');


Route::get('/redirect/CRM', function () {
    return redirect()->away('https://crm.5coremanagement.com/login');
})->name('redirect.CRM');

Route::get('/redirect/AI', function () {
    return redirect()->away('https://ai.5coremanagement.com');
})->name('redirect.AI');

// Route::get('/redirect/Recruitment', function () {
//     return redirect()->away('https://recruitment.5coremanagement.com/public/login');
// })->name('redirect.Recruitment');

Route::get('/redirect/Drive', function () {
    return redirect()->away('https://drive.5coremanagement.com/login');
})->name('redirect.Drive');

Route::get('/redirect/seo', function () {
    return redirect()->away('https://seo.5coremanagement.com/admin');
})->name('redirect.seo');

Route::get('/redirect/Master', function () {
    return redirect()->away('https://masterlisting.5coremanagement.com/');
})->name('redirect.Master');

Route::get('/redirect/Purchase', function () {
    return redirect()->away('https://purchase.5coremanagement.com/');
})->name('redirect.Purchase');


Route::get('/redirect/analysis', function () {
    return redirect()->away('https://pricing-analysis.5coremanagement.com/');
})->name('redirect.analysis');


Route::get('/redirect/forms', function () {
    return redirect()->away('https://5coremanagement.com/report-view');
})->name('redirect.forms');

Route::get('/redirect/resources', function () {
    return redirect()->away('https://drive.5coremanagement.com/drive');
})->name('redirect.resources');

Route::get('/redirect/link', function () {
    return redirect()->away('https://link-drive.5coremanagement.com');
})->name('redirect.link');


Route::get('/redirect/require', function () {
    return redirect()->away('https://mandatory-requirements.5coremanagement.com/');
})->name('redirect.require');

Route::get('/redirect/inventory', function () {
    return redirect()->away('http://inventory.5coremanagement.com/verification-adjustment-view');
})->name('redirect.inventory');

Route::get('/redirect/listing', function () {
    return redirect()->away('http://listing-mirror.5coremanagement.com/');
})->name('redirect.listing');

Route::get('/redirect/crm', function () {
    return redirect()->away('https://crm.5coremanagement.com/login');
})->name('redirect.crm');

Route::get('/redirect/policy', function () {
    return redirect()->away('https://5coremanagement.com/leave-policy');
})->name('redirect.policy');

Route::get('/redirect/5policy', function () {
    return redirect()->away('https://5coremanagement.com/our-policy');
})->name('redirect.5policy');

Route::get('/redirect/Training', function () {
    return redirect()->away('https://training.5coremanagement.com/');
})->name('redirect.Training');

Route::get('/redirect/Recruitment', function () {
    return redirect()->away('https://recruitment.5coremanagement.com/public/login');
})->name('redirect.Recruitment');
// MOM (Minutes of Meeting) routes
Route::middleware(['auth'])->group(function () {
    Route::get('/mom', [App\Http\Controllers\MomController::class, 'index'])->name('mom.index');
    Route::get('/mom/create', [App\Http\Controllers\MomController::class, 'create'])->name('mom.create');
    Route::post('/mom', [App\Http\Controllers\MomController::class, 'store'])->name('mom.store');
    Route::get('/mom/{id}', [App\Http\Controllers\MomController::class, 'show'])->name('mom.show');
    Route::delete('/mom/{id}', [App\Http\Controllers\MomController::class, 'destroy'])->name('mom.destroy');
});
// Daily Shipping Checklist routes
Route::middleware(['auth'])->group(function () {
    Route::get('/shipping-checklist', [App\Http\Controllers\DailyShippingChecklistController::class, 'index'])->name('shipping-checklist.index');
    Route::post('/shipping-checklist', [App\Http\Controllers\DailyShippingChecklistController::class, 'store'])->name('shipping-checklist.store');
    Route::delete('/shipping-checklist/{id}', [App\Http\Controllers\DailyShippingChecklistController::class, 'destroy'])->name('shipping-checklist.destroy');
});
Route::get('/redirect/software', function () {
    return redirect()->away('https://software-report.5coremanagement.com/');
})->name('redirect.software');

// --- FLAG RAISE ROUTE (for modal AJAX) ---
Route::middleware(['auth'])->group(function () {
    Route::post('/flag-raise/store', [App\Http\Controllers\FlagRaiseController::class, 'store'])->name('flag-raise.store');
    Route::get('/flag-raise/history', [App\Http\Controllers\FlagRaiseController::class, 'history'])->name('flag-raise.history');
    Route::delete('/flag-raise/{id}', [App\Http\Controllers\FlagRaiseController::class, 'destroy'])->name('flag-raise.destroy');
});

// Task Activity Report Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/task-activity-report', [App\Http\Controllers\TaskActivityReportController::class, 'index'])->name('task.activity.report')->middleware(App\Http\Middleware\CheckTaskActivityReportAccess::class);
    Route::post('/task-activity-report/{id}/restore', [App\Http\Controllers\TaskActivityReportController::class, 'restore'])->name('task.activity.restore')->middleware(App\Http\Middleware\CheckTaskActivityReportAccess::class);
    Route::delete('/task-activity-report/{id}', [App\Http\Controllers\TaskActivityReportController::class, 'destroy'])->name('task.activity.destroy')->middleware(App\Http\Middleware\CheckTaskActivityReportAccess::class);
});


// Done Clear Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/done-clear', [Workdo\Taskly\Http\Controllers\DoneClearController::class, 'index'])->name('done-clear.index');
    Route::post('/done-clear', [Workdo\Taskly\Http\Controllers\DoneClearController::class, 'store'])->name('done-clear.store');
    Route::get('/done-clear/users', [Workdo\Taskly\Http\Controllers\DoneClearController::class, 'getUsers'])->name('done-clear.users');
    Route::delete('/done-clear/{id}', [Workdo\Taskly\Http\Controllers\DoneClearController::class, 'destroy'])->name('done-clear.destroy');
});

// EMERGENCY FIX - Remove after use
Route::get('/emergency-fix-august', function() {
    echo "CHECKING CURRENT DATA...<br>";
    
    // Check current state
    $aug = DB::table('payrolls')->where('employee_id', 44)->where('month', 'August')->where('year', 2025)->first();
    $sep = DB::table('payrolls')->where('employee_id', 44)->where('month', 'September')->where('year', 2025)->first();
    
    echo "AUGUST BEFORE: ";
    if ($aug) {
        echo "ID: {$aug->id} | Sal Previous: {$aug->sal_previous} | Sal Current: {$aug->salary_current} | Approved Hrs: {$aug->approved_hrs}<br>";
    } else {
        echo "No August record found<br>";
    }
    
    echo "SEPTEMBER BEFORE: ";
    if ($sep) {
        echo "ID: {$sep->id} | Sal Previous: {$sep->sal_previous} | Sal Current: {$sep->salary_current} | Approved Hrs: {$sep->approved_hrs}<br>";
    } else {
        echo "No September record found<br>";
    }
    
    // Fix August data immediately
    echo "<br>FIXING AUGUST DATA...<br>";
    $fixedAug = DB::table('payrolls')
        ->where('employee_id', 44)
        ->where('month', 'August')
        ->where('year', 2025)
        ->update([
            'sal_previous' => 44000,
            'salary_current' => 44000,
            'increment' => 0,
            'approved_hrs' => 199,
            'approval_status' => 'pending'
        ]);
    
    echo "Fixed August data: {$fixedAug} row(s) updated<br>";
    
    // Ensure September has correct data
    echo "FIXING SEPTEMBER DATA...<br>";
    $fixedSep = DB::table('payrolls')
        ->where('employee_id', 44)
        ->where('month', 'September')
        ->where('year', 2025)
        ->update([
            'sal_previous' => 44000,  // Previous month salary
            'salary_current' => 45000, // Current salary with increment
            'increment' => 1000,      // The increment that was added
            'approved_hrs' => 72,     // TeamLogger value
            'approval_status' => 'pending'
        ]);
    
    echo "Fixed September data: {$fixedSep} row(s) updated<br>";
    
    // Verify the fix
    echo "<br>VERIFYING FIXES...<br>";
    $aug = DB::table('payrolls')->where('employee_id', 44)->where('month', 'August')->where('year', 2025)->first();
    $sep = DB::table('payrolls')->where('employee_id', 44)->where('month', 'September')->where('year', 2025)->first();
    
    echo "AUGUST AFTER: ";
    if ($aug) {
        echo "ID: {$aug->id} | Sal Previous: {$aug->sal_previous} | Sal Current: {$aug->salary_current} | Approved Hrs: {$aug->approved_hrs}<br>";
    }
    
    echo "SEPTEMBER AFTER: ";
    if ($sep) {
        echo "ID: {$sep->id} | Sal Previous: {$sep->sal_previous} | Sal Current: {$sep->salary_current} | Approved Hrs: {$sep->approved_hrs}<br>";
    }
    
    echo "<br><strong>DATA RESTORATION COMPLETE!</strong><br>";
    echo "<a href='/zoom-tm/payroll?month=August%202025'>Check August Payroll</a><br>";
    echo "<a href='/zoom-tm/payroll?month=September%202025'>Check September Payroll</a>";
});

// EMERGENCY FIX for sal_previous - Use this to fix missing sal_previous values
Route::get('/emergency-fix-sal-previous', function() {
    echo "FIXING MISSING SAL_PREVIOUS VALUES...<br><br>";
    
    // Get all payroll records where sal_previous is missing or 0
    $payrolls = DB::table('payrolls')
        ->where('month', 'September 2025')
        ->where(function($query) {
            $query->whereNull('sal_previous')
                  ->orWhere('sal_previous', 0);
        })
        ->get();
    
    $fixedCount = 0;
    
    foreach ($payrolls as $payroll) {
        echo "Checking Employee ID: {$payroll->employee_id} ({$payroll->name})<br>";
        echo "Current sal_previous: " . ($payroll->sal_previous ?? 'NULL') . "<br>";
        
        // Find August record for this employee
        $august = DB::table('payrolls')
            ->where('employee_id', $payroll->employee_id)
            ->where('month', 'August 2025')
            ->first();
        
        if ($august && $august->salary_current > 0) {
            echo "Found August salary_current: {$august->salary_current}<br>";
            
            // Update September record
            $updated = DB::table('payrolls')
                ->where('id', $payroll->id)
                ->update([
                    'sal_previous' => $august->salary_current,
                    'salary_current' => $august->salary_current + ($payroll->increment ?? 0)
                ]);
            
            if ($updated) {
                echo "<strong>FIXED!</strong> Set sal_previous to {$august->salary_current}<br>";
                $fixedCount++;
            }
        } else {
            echo "No August record found or salary_current is 0<br>";
        }
        echo "<br>";
    }
    
    echo "<strong>SUMMARY: Fixed {$fixedCount} records with missing sal_previous values</strong><br>";
    echo "<a href='/zoom-tm/payroll?month=September%202025'>Check September Payroll</a>";
});

// Patch code
Route::get('/updatel30tl/{id}', [PatchController::class, 'addTeamloggerTime'])->name('addTeamloggerTime');