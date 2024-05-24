<?php

use App\Http\Controllers\PlanController;
use App\Http\Middleware\Authorize;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\TodosController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UpdaterController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MeetingsController;
use App\Http\Controllers\PayslipsController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ContractsController;
use App\Http\Controllers\InstallerController;
use App\Http\Middleware\CustomRoleMiddleware;
use App\Http\Controllers\AllowancesController;
use App\Http\Controllers\DeductionsController;
use App\Http\Controllers\WorkspacesController;
use App\Http\Controllers\TimeTrackerController;
use App\Http\Controllers\LeaveRequestController;
use Spatie\Permission\Middlewares\RoleMiddleware;
use App\Http\Controllers\PaymentMethodsController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use Spatie\Permission\Middlewares\PermissionMiddleware;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//---------------------------------------------------------------

Route::get('/clear-cache', function () {

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    return "Cache cleared successfully.";
});

Route::get('/run-storage-command', function () {
    Artisan::call('storage:link');
    return 'Storage command executed successfully';
});

Route::get('/create-symlink', function () {
    if (config('constants.ALLOW_MODIFICATION') === 1) {
        $storageLinkPath = public_path('storage');
        if (is_dir($storageLinkPath)) {
            File::deleteDirectory($storageLinkPath);
        }
        Artisan::call('storage:link');
        echo 'Symbolik link created successfully.';
    } else {
        echo 'Not allowed in demo mode';
    }
});

Route::get('/install', [InstallerController::class, 'index'])->middleware('guest');

Route::post('/installer/config-db', [InstallerController::class, 'config_db'])->middleware('guest');

Route::post('/installer/install', [InstallerController::class, 'install'])->middleware('guest');


Route::middleware(['CheckInstallation'])->group(function () {

    Route::get('/', [UserController::class, 'login'])->name('login')->middleware('guest');
    Route::get('/register', [UserController::class, 'register'])->name('register')->middleware('guest');
    Route::post('/users/register', [UserController::class, 'clientRegister']);


    Route::post('/users/authenticate', [UserController::class, 'authenticate']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->middleware('guest');

    Route::post('/forgot-password-mail', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('guest');

    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->middleware('guest')->name('password.reset');

    Route::post('/reset-password', [ForgotPasswordController::class, 'ResetPassword'])->middleware('guest')->name('password.update');

    Route::get('/email/verify', [UserController::class, 'email_verification'])->name('verification.notice')->middleware(['auth:web,client']);

    Route::get('/email/verify/{id}/{hash}', [ClientController::class, 'verify_email'])->middleware(['auth:web,client', 'signed'])->name('verification.verify');

    Route::get('/email/verification-notification', [UserController::class, 'resend_verification_link'])->middleware(['auth:web,client', 'throttle:6,1'])->name('verification.send');

    Route::post('/logout', [UserController::class, 'logout'])->middleware(['multiguard']);


    Route::get('/planss', [UserController::class, 'showPlans'])->name('plan.index');
    Route::get('/planss/subscribe/{id}', [UserController::class, 'subscribePlans'])->name('plans.subscribe');


    // admin plans
    Route::get('/plans', [PlanController::class, 'showPlans'])->name('plans.index');
    Route::get('/plans/subscribe/{id}', [PlanController::class, 'subscribePlans'])->name('plan.subscribe');
    Route::get('/plans/edit/{id}', [PlanController::class, 'editPlan'])->name('plans.edit');
    Route::get('/plans/create', [PlanController::class, 'createPlan'])->name('plans.create');
    Route::post('/plans/store', [PlanController::class, 'storePlan'])->name('plans.store');
    Route::post('/plans/update', [PlanController::class, 'updatePlan'])->name('plans.update');
    Route::delete('/plans/destroy/{id}', [PlanController::class, 'destroy'])->name('plans.destroy');

    // plan requests
    Route::get('/request', [PlanController::class, 'showRequests'])->name('plans.request');
    Route::post('/request/store', [PlanController::class, 'storeRequest'])->name('plans.request.store');
    Route::post('/approve/{id}', [PlanController::class, 'approveRequest'])->name('plans.approve');
    Route::delete('/reject/{id}', [PlanController::class, 'rejectRequest'])->name('plans.reject');

    
    // ,'custom-verified'
    Route::middleware(['multiguard', 'custom-verified'])->group(function () {

        Route::get('/home', [HomeController::class, 'index'])->name('home');

        Route::get('/home/upcoming-birthdays', [HomeController::class, 'upcoming_birthdays']);

        Route::get('/home/upcoming-work-anniversaries', [HomeController::class, 'upcoming_work_anniversaries']);

        Route::get('/home/members-on-leave', [HomeController::class, 'members_on_leave']);

        //Projects--------------------------------------------------------

        Route::middleware(['customcan:manage_projects', 'has_workspace'])->group(function () {


            Route::get('/projects/{type?}', [ProjectsController::class, 'index'])->where('type', 'favorite');

            Route::get('/projects/list/{type?}', [ProjectsController::class, 'list_view'])->where('type', 'favorite');

            Route::get('/projects/information/{id}', [ProjectsController::class, 'show'])->middleware(['checkAccess:App\Models\Project,projects,id,projects']);

            Route::get('/projects/create', [ProjectsController::class, 'create'])->middleware(['customcan:create_projects']);

            Route::post('/projects/store', [ProjectsController::class, 'store'])->middleware(['customcan:create_projects']);

            Route::get('/projects/edit/{id}', [ProjectsController::class, 'edit'])->middleware(['customcan:edit_projects'])->middleware(['checkAccess:App\Models\Project,projects,id,projects']);

            Route::put('/projects/update/{id}', [ProjectsController::class, 'update'])->middleware(['customcan:edit_projects'])->middleware(['checkAccess:App\Models\Project,projects,id,projects']);

            Route::delete('/projects/destroy/{id}', [ProjectsController::class, 'destroy'])->middleware(['customcan:delete_projects', 'demo_restriction'])->middleware(['checkAccess:App\Models\Project,projects,id,projects']);

            Route::post('/projects/destroy_multiple', [ProjectsController::class, 'destroy_multiple'])->middleware(['customcan:delete_projects', 'demo_restriction']);

            Route::get('/projects/listing/{id?}', [ProjectsController::class, 'list']);

            Route::post('/projects/update-favorite/{id}', [ProjectsController::class, 'update_favorite']);

            Route::get('/projects/duplicate/{id}', [ProjectsController::class, 'duplicate'])->middleware(['customcan:create_projects'])->middleware(['checkAccess:App\Models\Project,projects,id,projects']);

            Route::get('/projects/tasks/create/{id}', [TasksController::class, 'create'])->middleware(['customcan:manage_tasks', 'customcan:create_tasks'])->middleware(['checkAccess:App\Models\Project,projects,id,projects']);

            Route::get('/projects/tasks/edit/{id}', [TasksController::class, 'edit'])->middleware(['customcan:manage_tasks', 'customcan:edit_tasks'])->middleware(['checkAccess:App\Models\Task,tasks,id,tasks']);

            Route::get('/projects/tasks/list/{id}', [TasksController::class, 'index'])->middleware(['customcan:manage_tasks']);

            Route::get('/projects/tasks/draggable/{id}', [TasksController::class, 'dragula'])->middleware(['customcan:manage_tasks'])->middleware(['checkAccess:App\Models\Project,projects,id,projects']);



            // task_chat
            Route::post('/projects/task-chat/{id}', [TasksController::class, 'task_chat'])->name('task_chat');
            Route::post('/projects/task-status/{id}', [TasksController::class, 'openStatus'])->name('open_status');
            Route::post('/projects/task-close/{id}', [TasksController::class, 'task_close'])->name('task_close');

            



            Route::get('/status/manage', [StatusController::class, 'index']);
            Route::post('/status/store', [StatusController::class, 'store'])->middleware(['demo_restriction']);
            Route::get('/status/list', [StatusController::class, 'list']);
            Route::post('/status/update', [StatusController::class, 'update'])->middleware(['demo_restriction']);
            Route::get('/status/get/{id}', [StatusController::class, 'get']);
            Route::delete('/status/destroy/{id}', [StatusController::class, 'destroy'])->middleware(['demo_restriction']);
            Route::post('/status/destroy_multiple', [StatusController::class, 'destroy_multiple']);

            Route::get('/tags/manage', [TagsController::class, 'index']);
            Route::post('/tags/store', [TagsController::class, 'store']);
            Route::get('/tags/list', [TagsController::class, 'list']);
            Route::get('/tags/get/{id}', [TagsController::class, 'get']);
            Route::post('/tags/update', [TagsController::class, 'update']);
            Route::get('/tags/get-suggestion', [TagsController::class, 'get_suggestions']);
            Route::post('/tags/get-ids', [TagsController::class, 'get_ids']);
            Route::delete('/tags/destroy/{id}', [TagsController::class, 'destroy'])->middleware(['demo_restriction']);
            Route::post('/tags/destroy_multiple', [TagsController::class, 'destroy_multiple'])->middleware(['demo_restriction']);
        });

        //Tasks-------------------------------------------------------------

        Route::middleware(['customcan:manage_tasks', 'has_workspace'])->group(function () {

            Route::get('/tasks', [TasksController::class, 'index']);

            Route::get('/tasks/information/{id}', [TasksController::class, 'show'])->middleware(['checkAccess:App\Models\Task,tasks,id,tasks']);
            
            Route::get('/tasks/notification/read/{id}', [TasksController::class, 'notificationRead']);

            Route::get('/tasks/create', [TasksController::class, 'create'])->middleware(['customcan:create_tasks']);

            Route::post('/tasks/store', [TasksController::class, 'store'])->middleware(['customcan:create_tasks']);

            Route::get('/tasks/duplicate/{id}', [TasksController::class, 'duplicate'])->middleware(['customcan:create_tasks'])->middleware(['checkAccess:App\Models\Task,tasks,id,tasks']);

            Route::get('/tasks/edit/{id}', [TasksController::class, 'edit'])->middleware(['customcan:edit_tasks'])->middleware(['checkAccess:App\Models\Task,tasks,id,tasks']);

            Route::put('/tasks/update/{id}', [TasksController::class, 'update'])->middleware(['customcan:edit_tasks'])->middleware(['checkAccess:App\Models\Task,tasks,id,tasks']);

            Route::delete('/tasks/destroy/{id}', [TasksController::class, 'destroy'])->middleware(['customcan:delete_tasks', 'demo_restriction'])->middleware(['checkAccess:App\Models\Task,tasks,id,tasks']);

            Route::post('/tasks/destroy_multiple', [TasksController::class, 'destroy_multiple'])->middleware(['customcan:delete_tasks', 'demo_restriction']);

            Route::get('/tasks/list/{id?}', [TasksController::class, 'list']);

            Route::get('/tasks/draggable', [TasksController::class, 'dragula']);

            Route::put('/tasks/{id}/update-status/{status}', [TasksController::class, 'updateStatus'])->middleware(['customcan:edit_tasks']);
        });

        //Meetings-------------------------------------------------------------
        Route::middleware(['customcan:manage_meetings', 'has_workspace'])->group(function () {

            Route::get('/meetings', [MeetingsController::class, 'index']);

            Route::get('/meetings/create', [MeetingsController::class, 'create'])->middleware(['customcan:create_meetings']);

            Route::post('/meetings/store', [MeetingsController::class, 'store'])->middleware(['customcan:create_meetings']);

            Route::get('/meetings/list', [MeetingsController::class, 'list']);

            Route::get('/meetings/edit/{id}', [MeetingsController::class, 'edit'])->middleware(['customcan:edit_meetings'])->middleware(['checkAccess:App\Models\Meeting,meetings,id,meetings']);

            Route::put('/meetings/update/{id}', [MeetingsController::class, 'update'])->middleware(['customcan:edit_meetings'])->middleware(['checkAccess:App\Models\Meeting,meetings,id,meetings']);

            Route::delete('/meetings/destroy/{id}', [MeetingsController::class, 'destroy'])->middleware(['customcan:delete_meetings', 'demo_restriction'])->middleware(['checkAccess:App\Models\Meeting,meetings,id,meetings']);

            Route::post('/meetings/destroy_multiple', [MeetingsController::class, 'destroy_multiple'])->middleware(['customcan:delete_meetings', 'demo_restriction']);

            Route::get('/meetings/join/{id}', [MeetingsController::class, 'join'])->middleware(['checkAccess:App\Models\Meeting,meetings,id,meetings']);

            Route::get('/meetings/duplicate/{id}', [MeetingsController::class, 'duplicate'])->middleware(['customcan:create_meetings'])->middleware(['checkAccess:App\Models\Meeting,meetings,id,meetings']);
        });

        //Workspaces-------------------------------------------------------------
        Route::middleware(['customcan:manage_workspaces'])->group(function () {

            Route::get('/workspaces', [WorkspacesController::class, 'index']);

            Route::get('/workspaces/create', [WorkspacesController::class, 'create'])->middleware(['customcan:create_workspaces']);

            Route::post('/workspaces/store', [WorkspacesController::class, 'store'])->middleware(['customcan:create_workspaces']);

            Route::get('/workspaces/duplicate/{id}', [WorkspacesController::class, 'duplicate'])->middleware(['customcan:create_workspaces'])->middleware(['checkAccess:App\Models\Workspace,workspaces,id,workspaces']);

            Route::get('/workspaces/list', [WorkspacesController::class, 'list']);

            Route::get('/workspaces/edit/{id}', [WorkspacesController::class, 'edit'])->middleware(['customcan:edit_workspaces'])->middleware(['checkAccess:App\Models\Workspace,workspaces,id,workspaces']);

            Route::put('/workspaces/update/{id}', [WorkspacesController::class, 'update'])->middleware(['customcan:edit_workspaces', 'demo_restriction'])->middleware(['checkAccess:App\Models\Workspace,workspaces,id,workspaces']);

            Route::delete('/workspaces/destroy/{id}', [WorkspacesController::class, 'destroy'])->middleware(['customcan:delete_workspaces', 'demo_restriction'])->middleware(['checkAccess:App\Models\Workspace,workspaces,id,workspaces']);

            Route::post('/workspaces/destroy_multiple', [WorkspacesController::class, 'destroy_multiple'])->middleware(['customcan:delete_workspaces', 'demo_restriction']);

            Route::get('/workspaces/switch/{id}', [WorkspacesController::class, 'switch'])->middleware(['checkAccess:App\Models\Workspace,workspaces,id,workspaces']);
        });
        Route::get('/workspaces/remove_participant', [WorkspacesController::class, 'remove_participant'])->middleware(['demo_restriction']);

        //Todos-------------------------------------------------------------
        Route::middleware(['has_workspace'])->group(function () {

            Route::get('/todos', [TodosController::class, 'index']);

            Route::get('/todos/create', [TodosController::class, 'create']);

            Route::post('/todos/store', [TodosController::class, 'store']);

            Route::patch('/todos/cross/{id}', [TodosController::class, 'update_checked']);

            Route::get('/todos/edit/{id}', [TodosController::class, 'edit']);

            Route::post('/todos/update', [TodosController::class, 'update'])->name('todos.update');

            Route::put('/todos/update_status', [TodosController::class, 'update_status']);

            Route::delete('/todos/destroy/{id}', [TodosController::class, 'destroy'])->middleware(['demo_restriction']);

            Route::get('/todos/get/{id}', [TodosController::class, 'get']);


            Route::get('/notes', [NotesController::class, 'index']);

            Route::post('/notes/store', [NotesController::class, 'store']);

            Route::post('/notes/update', [NotesController::class, 'update']);

            Route::get('/notes/get/{id}', [NotesController::class, 'get']);

            Route::delete('/notes/destroy/{id}', [NotesController::class, 'destroy'])->middleware(['demo_restriction']);
        });

        //Users-------------------------------------------------------------

        Route::get('account/{user}', [ProfileController::class, 'show'])->name('profile.show');

        Route::put('/profile/update_photo/{userOrClient}', [ProfileController::class, 'update_photo']);

        Route::put('profile/update/{userOrClient}', [ProfileController::class, 'update'])->name('profile.update')->middleware(['demo_restriction']);

        Route::delete('/account/destroy/{user}', [ProfileController::class, 'destroy'])->middleware(['demo_restriction']);

        Route::middleware(['customcan:manage_users', 'has_workspace'])->group(function () {

            Route::get('/users', [UserController::class, 'index']);

            Route::get('/users/create', [UserController::class, 'create'])->middleware(['customcan:create_users']);

            Route::post('/users/store', [UserController::class, 'store'])->middleware(['customcan:create_users']);

            Route::get('/users/profile/{id}', [UserController::class, 'show']);

            Route::get('/users/edit/{id}', [UserController::class, 'edit_user'])->middleware(['customcan:edit_users']);

            Route::put('/users/update_user/{user}', [UserController::class, 'update_user'])->middleware(['customcan:edit_users', 'demo_restriction']);

            Route::delete('/users/delete_user/{user}', [UserController::class, 'delete_user'])->middleware(['customcan:delete_users', 'demo_restriction']);

            Route::post('/users/delete_multiple_user', [UserController::class, 'delete_multiple_user'])->middleware(['customcan:delete_users', 'demo_restriction']);

            Route::get('/users/list', [UserController::class, 'list']);
        });

        //Clients-------------------------------------------------------------

        Route::middleware(['customcan:manage_clients', 'has_workspace'])->group(function () {

            Route::get('/clients', [ClientController::class, 'index']);

            Route::get('/clients/profile/{id}', [ClientController::class, 'show']);

            Route::get('/clients/create', [ClientController::class, 'create'])->middleware(['customcan:create_clients']);

            Route::post('/clients/store', [ClientController::class, 'store'])->middleware(['customcan:create_clients']);

            Route::get('/clients/edit/{id}', [ClientController::class, 'edit'])->middleware(['customcan:edit_clients']);

            Route::put('/clients/update/{id}', [ClientController::class, 'update'])->middleware(['customcan:edit_clients', 'demo_restriction']);

            Route::delete('/clients/destroy/{id}', [ClientController::class, 'destroy'])->middleware(['customcan:delete_clients', 'demo_restriction']);

            Route::post('/clients/destroy_multiple', [ClientController::class, 'destroy_multiple'])->middleware(['customcan:delete_clients', 'demo_restriction']);

            Route::get('/clients/list', [ClientController::class, 'list']);
        });

        //Settings-------------------------------------------------------------
        Route::get("settings/languages/switch/{code}", [LanguageController::class, 'switch']);

        Route::put("settings/languages/set-default", [LanguageController::class, 'set_default']);

        Route::middleware(['customRole:admin'])->group(function () {

            Route::get('/settings/permission/create', [RolesController::class, 'create_permission']);

            Route::get('/settings/permission', [RolesController::class, 'index']);

            Route::delete('/roles/destroy/{id}', [RolesController::class, 'destroy'])->middleware(['demo_restriction']);

            Route::get('/roles/create', [RolesController::class, 'create']);

            Route::post('/roles/store', [RolesController::class, 'store']);

            Route::get('/roles/edit/{id}', [RolesController::class, 'edit']);

            Route::put('/roles/update/{id}', [RolesController::class, 'update']);

            Route::get('/settings/general', [SettingsController::class, 'index']);

            Route::put('/settings/store_general', [SettingsController::class, 'store_general_settings'])->middleware(['demo_restriction']);

            Route::get('/settings/languages', [LanguageController::class, 'index']);

            Route::post('/settings/languages/store', [LanguageController::class, 'store']);

            Route::get("settings/languages/change/{code}", [LanguageController::class, 'change']);

            Route::put("/settings/languages/save_labels", [LanguageController::class, 'save_labels']);

            Route::get('/settings/email', [SettingsController::class, 'email']);

            Route::put('/settings/store_email', [SettingsController::class, 'store_email_settings'])->middleware(['demo_restriction']);

            Route::get('/settings/pusher', [SettingsController::class, 'pusher']);

            Route::put('/settings/store_pusher', [SettingsController::class, 'store_pusher_settings'])->middleware(['demo_restriction']);

            Route::get('/settings/system-updater', [UpdaterController::class, 'index']);

            Route::post('/settings/update-system', [UpdaterController::class, 'update'])->middleware(['demo_restriction']);
        });
        Route::middleware(['has_workspace'])->group(function () {
            Route::get('/search', [SearchController::class, 'search']);

            Route::middleware(['admin_or_user'])->group(function () {
                Route::get('/leave-requests', [LeaveRequestController::class, 'index']);
                Route::post('/leave-requests/store', [LeaveRequestController::class, 'store']);
                Route::get('/leave-requests/list', [LeaveRequestController::class, 'list']);
                Route::get('/leave-requests/get/{id}', [LeaveRequestController::class, 'get']);
                Route::post('/leave-requests/update', [LeaveRequestController::class, 'update'])->middleware(['admin_or_leave_editor']);
                Route::post('/leave-requests/update-editors', [LeaveRequestController::class, 'update_editors'])->middleware(['customRole:admin']);
                Route::delete('/leave-requests/destroy/{id}', [LeaveRequestController::class, 'destroy'])->middleware(['admin_or_leave_editor', 'demo_restriction']);
                Route::post('/leave-requests/destroy_multiple', [LeaveRequestController::class, 'destroy_multiple'])->middleware(['admin_or_leave_editor', 'demo_restriction']);
            });
            Route::middleware(['customcan:manage_contracts'])->group(function () {
                Route::get('/contracts', [ContractsController::class, 'index']);
                Route::post('/contracts/store', [ContractsController::class, 'store'])->middleware(['customcan:create_contracts']);
                Route::get('/contracts/list', [ContractsController::class, 'list']);
                Route::get('/contracts/get/{id}', [ContractsController::class, 'get'])->middleware(['checkAccess:App\Models\Contract,contracts,id']);
                Route::post('/contracts/update', [ContractsController::class, 'update'])->middleware(['customcan:edit_contracts']);
                Route::get('/contracts/sign/{id}', [ContractsController::class, 'sign'])->middleware(['checkAccess:App\Models\Contract,contracts,id,contracts']);
                Route::post('/contracts/create-sign', [ContractsController::class, 'create_sign']);
                Route::get('/contracts/duplicate/{id}', [ContractsController::class, 'duplicate'])->middleware(['customcan:create_contracts', 'checkAccess:App\Models\Contract,contracts,id,contracts']);
                Route::delete('/contracts/destroy/{id}', [ContractsController::class, 'destroy'])->middleware(['customcan:delete_contracts', 'demo_restriction', 'checkAccess:App\Models\Contract,contracts,id,contracts']);
                Route::post('/contracts/destroy_multiple', [ContractsController::class, 'destroy_multiple'])->middleware(['customcan:delete_contracts', 'demo_restriction']);
                Route::delete('/contracts/delete-sign/{id}', [ContractsController::class, 'delete_sign']);


                Route::get('/contracts/contract-types', [ContractsController::class, 'contract_types']);
                Route::post('/contracts/store-contract-type', [ContractsController::class, 'store_contract_type']);
                Route::get('/contracts/contract-types-list', [ContractsController::class, 'contract_types_list']);
                Route::get('/contracts/get-contract-type/{id}', [ContractsController::class, 'get_contract_type']);
                Route::post('/contracts/update-contract-type', [ContractsController::class, 'update_contract_type']);
                Route::delete('/contracts/delete-contract-type/{id}', [ContractsController::class, 'delete_contract_type'])->middleware(['demo_restriction']);
                Route::post('/contracts/delete-multiple-contract-type', [ContractsController::class, 'delete_multiple_contract_type'])->middleware(['demo_restriction']);
            });


            Route::middleware(['customcan:manage_payslips'])->group(function () {
                Route::get('/payslips', [PayslipsController::class, 'index']);
                Route::get('/payslips/create', [PayslipsController::class, 'create'])->middleware(['customcan:create_payslips']);
                Route::post('/payslips/store', [PayslipsController::class, 'store'])->middleware(['customcan:create_payslips']);
                Route::get('/payslips/list', [PayslipsController::class, 'list']);
                Route::delete('/payslips/destroy/{id}', [PayslipsController::class, 'destroy'])->middleware(['demo_restriction', 'customcan:delete_payslips', 'checkAccess:App\Models\Payslip,payslips,id,payslips']);
                Route::post('/payslips/destroy_multiple', [PayslipsController::class, 'destroy_multiple'])->middleware(['demo_restriction', 'customcan:delete_payslips']);
                Route::get('/payslips/duplicate/{id}', [PayslipsController::class, 'duplicate'])->middleware(['customcan:create_payslips', 'checkAccess:App\Models\Payslip,payslips,id,payslips']);
                Route::get('/payslips/edit/{id}', [PayslipsController::class, 'edit'])->middleware(['customcan:edit_payslips', 'checkAccess:App\Models\Payslip,payslips,id,payslips']);
                Route::post('/payslips/update', [PayslipsController::class, 'update'])->middleware(['customcan:edit_payslips', 'checkAccess:App\Models\Payslip,payslips,id,payslips']);
                Route::get('/payslips/view/{id}', [PayslipsController::class, 'view'])->middleware(['checkAccess:App\Models\Payslip,payslips,id,payslips']);


                Route::get('/payment-methods', [PaymentMethodsController::class, 'index']);
                Route::post('/payment-methods/store', [PaymentMethodsController::class, 'store']);
                Route::get('/payment-methods/list', [PaymentMethodsController::class, 'list']);
                Route::get('/payment-methods/get/{id}', [PaymentMethodsController::class, 'get']);
                Route::post('/payment-methods/update', [PaymentMethodsController::class, 'update']);
                Route::delete('/payment-methods/destroy/{id}', [PaymentMethodsController::class, 'destroy'])->middleware(['demo_restriction']);
                Route::post('/payment-methods/destroy_multiple', [PaymentMethodsController::class, 'destroy_multiple'])->middleware(['demo_restriction']);

                Route::get('/allowances', [AllowancesController::class, 'index']);
                Route::post('/allowances/store', [AllowancesController::class, 'store']);
                Route::get('/allowances/list', [AllowancesController::class, 'list']);
                Route::get('/allowances/get/{id}', [AllowancesController::class, 'get']);
                Route::post('/allowances/update', [AllowancesController::class, 'update']);
                Route::delete('/allowances/destroy/{id}', [AllowancesController::class, 'destroy'])->middleware(['demo_restriction']);
                Route::post('/allowances/destroy_multiple', [AllowancesController::class, 'destroy_multiple'])->middleware(['demo_restriction']);

                Route::get('/deductions', [DeductionsController::class, 'index']);
                Route::post('/deductions/store', [DeductionsController::class, 'store']);
                Route::get('/deductions/get/{id}', [DeductionsController::class, 'get']);
                Route::get('/deductions/list', [DeductionsController::class, 'list']);
                Route::post('/deductions/update', [DeductionsController::class, 'update']);
                Route::delete('/deductions/destroy/{id}', [DeductionsController::class, 'destroy'])->middleware(['demo_restriction']);
                Route::post('/deductions/destroy_multiple', [DeductionsController::class, 'destroy_multiple'])->middleware(['demo_restriction']);
            });
            Route::get('/time-tracker', [TimeTrackerController::class, 'index'])->middleware(['customcan:manage_timesheet']);
            Route::post('/time-tracker/store', [TimeTrackerController::class, 'store'])->middleware(['customcan:create_timesheet']);
            Route::post('/time-tracker/update', [TimeTrackerController::class, 'update']);
            Route::get('/time-tracker/list', [TimeTrackerController::class, 'list'])->middleware(['customcan:manage_timesheet']);
            Route::delete('/time-tracker/destroy/{id}', [TimeTrackerController::class, 'destroy'])->middleware(['customcan:delete_timesheet']);
        });
    });
});
