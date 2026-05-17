<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CeoDashboardController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GanttController;
use App\Http\Controllers\LoginHistoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TaskAssignmentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskDependencyController;
use App\Http\Controllers\TaskResourceController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root ke login
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    /** @var \App\Models\User|null $user */
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    return match (true) {
        $user->hasRole('Programmer') => redirect()->route('programmer.dashboard'),
        $user->hasRole('CEO') => redirect()->route('ceo.dashboard'),
        $user->hasRole('COO') => redirect()->route('coo.dashboard'),
        $user->hasRole('CTO') => redirect()->route('cto.dashboard'),
        $user->hasRole('CFO') => redirect()->route('cfo.dashboard'),
        $user->hasRole('VP') => redirect()->route('vp.dashboard'),
        $user->hasRole('PM') => redirect()->route('pm.dashboard'),
        $user->hasRole('Team Lead') => redirect()->route('team-lead.dashboard'),
        $user->hasRole('Developer') => redirect()->route('developer.dashboard'),
        default => view('dashboard'),
    };
})->middleware('auth')->name('dashboard');


// Dashboard per role
Route::middleware(['auth'])->group(function () {
    Route::get('/programmer/dashboard', fn() => view('dashboards.programmer'))->name('programmer.dashboard')->middleware('role:Programmer');
    Route::get('/ceo/dashboard', [CeoDashboardController::class, 'index'])->name('ceo.dashboard')->middleware('role:CEO');
    Route::get('/coo/dashboard', fn() => view('dashboards.coo'))->name('coo.dashboard')->middleware('role:COO');
    Route::get('/cto/dashboard', fn() => view('dashboards.cto'))->name('cto.dashboard')->middleware('role:CTO');
    Route::get('/cfo/dashboard', fn() => view('dashboards.cfo'))->name('cfo.dashboard')->middleware('role:CFO');
    Route::get('/vp/dashboard', fn() => view('dashboards.vp'))->name('vp.dashboard')->middleware('role:VP');
    Route::get('/pm/dashboard', fn() => view('dashboards.pm'))->name('pm.dashboard')->middleware('role:PM');
    Route::get('/team-lead/dashboard', fn() => view('dashboards.team-lead'))->name('team-lead.dashboard')->middleware('role:Team Lead');
    Route::get('/developer/dashboard', fn() => view('dashboards.developer'))->name('developer.dashboard')->middleware('role:Developer');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Programmer Tools
Route::middleware(['auth', 'role:Programmer'])->prefix('tools')->name('tools.')->group(function () {
    Route::get('/', [ToolsController::class, 'index'])->name('index');
    Route::get('/database', [ToolsController::class, 'databaseView'])->name('database-view');
    Route::post('/database/download', [ToolsController::class, 'databaseDownload'])->name('database-download');
    Route::post('/database/backup-server', [ToolsController::class, 'databaseBackupServer'])->name('database-backup-server');
    Route::post('/database/restore', [ToolsController::class, 'databaseRestore'])->name('database-restore');
    Route::post('/database/restore-server', [ToolsController::class, 'databaseRestoreServer'])->name('database-restore-server');
    Route::get('/maintenance', [ToolsController::class, 'maintenanceView'])->name('maintenance-view');
    Route::post('/maintenance', [ToolsController::class, 'toggleMaintenance'])->name('maintenance');
    Route::get('/health', [ToolsController::class, 'systemHealth'])->name('health-view');
    Route::get('/error-logs', [ToolsController::class, 'errorLogs'])->name('error-logs');
    Route::post('/log-clear', [ToolsController::class, 'logClear'])->name('log-clear');
    Route::post('/migrate', [ToolsController::class, 'runMigrate'])->name('migrate');
    Route::get('/trashed-projects', [ToolsController::class, 'trashedProjects'])->name('trashed-projects');
    Route::delete('/trashed-projects/{project}', [ToolsController::class, 'hardDeleteProject'])->name('hard-delete-project')->where('project', '[0-9]+');
    Route::get('/download-audit-log', [ToolsController::class, 'downloadAuditLog'])->name('download-audit-log');
    Route::get('/audit-logs', [ToolsController::class, 'auditLogs'])->name('audit-logs');
    Route::post('/clear-audit-log',  [ToolsController::class, 'clearAuditLog'])->name('clear-audit-log');
    Route::get('/cache', [ToolsController::class, 'cacheView'])->name('cache-view');
    Route::post('/cache/clear', [ToolsController::class, 'clearSelectedCache'])->name('cache-clear-selected');
    Route::get('/login-history', [ToolsController::class, 'loginHistoryView'])->name('login-history-view');
    Route::post('/login-history/clear', [ToolsController::class, 'clearLoginHistory'])->name('login-history-clear');
    Route::get('/login-history/download', [ToolsController::class, 'downloadLoginHistory'])->name('login-history-download');
    Route::post('/trashed-projects/{id}/restore', [ProjectController::class, 'restoreTrashedProject'])->name('restore-trashed-project');
});

// Project Routes
Route::middleware(['auth'])->prefix('projects')->name('projects.')->group(function () {
    Route::get('/archived',         [ProjectController::class, 'archived'])->name('archived')->middleware('permission:project.view');
    Route::get('/',                  [ProjectController::class, 'index'])->name('index')->middleware('permission:project.view');
    Route::get('/create',            [ProjectController::class, 'create'])->name('create')->middleware('permission:project.create');
    Route::post('/',                 [ProjectController::class, 'store'])->name('store')->middleware('permission:project.create');
    Route::get('/{project}',         [ProjectController::class, 'show'])->name('show');
    Route::get('/{project}/edit',    [ProjectController::class, 'edit'])->name('edit')->middleware('permission:project.update');
    Route::put('/{project}',         [ProjectController::class, 'update'])->name('update')->middleware('permission:project.update');
    Route::delete('/{project}',      [ProjectController::class, 'destroy'])->name('destroy')->middleware('permission:project.delete');
    Route::post('/{project}/baseline',  [ProjectController::class, 'setBaseline'])->name('set-baseline')->middleware('permission:project.update');
    Route::post('/{project}/level-resources',  [ProjectController::class, 'levelResources'])->name('level-resources')->middleware('permission:project.update');
    Route::post('/{project}/archive',  [ProjectController::class, 'archive'])->name('archive')->middleware('permission:project.archive');
    Route::post('/{project}/restore',  [ProjectController::class, 'restore'])->name('restore')->middleware('permission:project.update');
});

// Upload Logo
Route::middleware(['auth', 'permission:setting.manage'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingController::class, 'index'])->name('index');
    Route::post('/', [SettingController::class, 'update'])->name('update');
    Route::post('/delete-logo', [SettingController::class, 'deleteLogo'])->name('delete-logo');
});

// Timesheet Routes
Route::middleware(['auth'])->prefix('projects/{project}/tasks/{task}/timesheets')->name('tasks.timesheets.')->group(function () {
    Route::get('/',               [TimesheetController::class, 'index'])->name('index');
    Route::post('/',              [TimesheetController::class, 'store'])->name('store');
    Route::delete('/{timesheet}', [TimesheetController::class, 'destroy'])->name('destroy');
});

// Task Routes
Route::middleware(['auth'])->prefix('projects/{project}/tasks')->name('tasks.')->group(function () {
    Route::get('/',               [TaskController::class, 'index'])->name('index')->middleware('permission:task.view');
    Route::get('/create',         [TaskController::class, 'create'])->name('create')->middleware('permission:task.create');
    Route::post('/',              [TaskController::class, 'store'])->name('store')->middleware('permission:task.create');
    Route::get('/{task}',         [TaskController::class, 'show'])->name('show')->middleware('permission:task.view');
    Route::get('/{task}/edit',    [TaskController::class, 'edit'])->name('edit')->middleware('permission:task.update');
    Route::put('/{task}',         [TaskController::class, 'update'])->name('update')->middleware('permission:task.update');
    Route::delete('/{task}',      [TaskController::class, 'destroy'])->name('destroy')->middleware('permission:task.delete');
});

// My Tasks
Route::get('/my-tasks', [TaskController::class, 'myTasks'])->name('tasks.my-tasks')->middleware(['auth', 'permission:task.view']);

// Task Comments
Route::middleware(['auth', 'permission:comment.view'])->prefix('projects/{project}/tasks/{task}/comments')->name('tasks.comments.')->group(function () {
    Route::get('/', [CommentController::class, 'index'])->name('index');
    Route::post('/', [CommentController::class, 'store'])->name('store')->middleware('permission:comment.create');
    Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('destroy')->middleware('permission:comment.delete');
});

// Task Attachments
Route::middleware(['auth', 'permission:attachment.view'])->prefix('projects/{project}/tasks/{task}/attachments')->name('tasks.attachments.')->group(function () {
    Route::get('/', [AttachmentController::class, 'index'])->name('index');
    Route::post('/', [AttachmentController::class, 'upload'])->name('upload')->middleware('permission:attachment.upload');
    Route::delete('/{attachment}', [AttachmentController::class, 'destroy'])->name('destroy')->middleware('permission:attachment.delete');
    Route::get('/{attachment}/download', [AttachmentController::class, 'download'])->name('download');
});

// Presence
Route::middleware(['auth', 'permission:presence.view'])->group(function () {
    Route::get('/presence/online', [PresenceController::class, 'online'])->name('presence.online');
});

// // My Tasks — PM
// Route::middleware(['auth', 'permission:task.view'])->get('/my-tasks', [TaskController::class, 'myTasks'])
//     ->name('tasks.my-tasks');

// Gantt Chart
Route::middleware(['auth', 'permission:gantt.view'])->prefix('projects/{project}/gantt')->name('projects.gantt.')->group(function () {
    Route::get('/',     [GanttController::class, 'index'])->name('index');
    Route::get('/data', [GanttController::class, 'data'])->name('data');
});

// Task Assignment Routes
Route::middleware(['auth', 'permission:task.assign'])->prefix('projects/{project}/tasks/{task}/assignments')->name('tasks.assignments.')->group(function () {
    Route::get('/',                [TaskAssignmentController::class, 'create'])->name('index');
    Route::post('/',               [TaskAssignmentController::class, 'store'])->name('store');
    Route::delete('/{assignment}', [TaskAssignmentController::class, 'destroy'])->name('destroy');
});

// Duplikat name untuk view backward compatibility
Route::get('projects/{project}/tasks/{task}/assignments', [TaskAssignmentController::class, 'create'])
    ->middleware(['auth', 'permission:task.assign'])
    ->name('tasks.assignments');

// Task Dependency Routes
Route::middleware(['auth', 'permission:task.dependency'])->prefix('projects/{project}/tasks/{task}/dependencies')->name('tasks.dependencies.')->group(function () {
    Route::get('/',                [TaskDependencyController::class, 'index'])->name('index');
    Route::post('/',               [TaskDependencyController::class, 'store'])->name('store');
    Route::delete('/{dependency}', [TaskDependencyController::class, 'destroy'])->name('destroy');
});

// Task Resource Routes
Route::middleware(['auth', 'permission:task.resource'])->prefix('projects/{project}/tasks/{task}/resources')->name('tasks.resources.')->group(function () {
    Route::get('/',              [TaskResourceController::class, 'index'])->name('index');
    Route::post('/',             [TaskResourceController::class, 'store'])->name('store');
    Route::delete('/{resource}', [TaskResourceController::class, 'destroy'])->name('destroy');
});

// Resource Master
Route::middleware(['auth'])->prefix('resources')->name('resources.')->group(function () {
    Route::get('/',                  [ResourceController::class, 'index'])->name('index')->middleware('permission:resource.view');
    Route::get('/create',            [ResourceController::class, 'create'])->name('create')->middleware('permission:resource.create');
    Route::post('/',                 [ResourceController::class, 'store'])->name('store')->middleware('permission:resource.create');
    Route::get('/{resource}/edit',   [ResourceController::class, 'edit'])->name('edit')->middleware('permission:resource.update');
    Route::put('/{resource}',        [ResourceController::class, 'update'])->name('update')->middleware('permission:resource.update');
    Route::delete('/{resource}',     [ResourceController::class, 'destroy'])->name('destroy')->middleware('permission:resource.delete');
});

// Notification Routes
Route::middleware(['auth', 'permission:notification.view'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/',              [NotificationController::class, 'index'])->name('index');
    Route::get('/unread-count',  [NotificationController::class, 'unreadCount'])->name('unread-count');

    // Route baru untuk mark as read satu notifikasi
    Route::post('/read/{notification}', [NotificationController::class, 'markAsRead'])->name('read');
    Route::post('/read-all',     [NotificationController::class, 'markAllAsRead'])->name('read-all');

    // Route baru untuk hapus notifikasi
    Route::delete('/delete-all', [NotificationController::class, 'deleteAll'])->name('delete-all'); // Ditaruh diatas agar tidak tertimpa {id}
    Route::delete('/{id}',       [NotificationController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth', 'permission:notification.manage'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
});

// User Management — CEO only
Route::middleware(['auth', 'permission:user.manage'])->prefix('users')->name('users.')->group(function () {
    Route::get('/',              [UserController::class, 'index'])->name('index');
    Route::get('/create',        [UserController::class, 'create'])->name('create');
    Route::post('/',             [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit',   [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}',        [UserController::class, 'update'])->name('update');
});

// Audit Log — CEO & Programmer
Route::middleware(['auth', 'permission:audit.view'])->prefix('audit-log')->name('audit-log.')->group(function () {
    Route::get('/', [AuditLogController::class, 'index'])->name('index');
});

// Login History — CEO & Programmer
Route::middleware(['auth', 'permission:login-history.view'])->prefix('login-history')->name('login-history.')->group(function () {
    Route::get('/', [LoginHistoryController::class, 'index'])->name('index');
});

// Reporting Dashboard — CEO
Route::middleware(['auth', 'permission:report.view'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/',                    [ReportController::class, 'index'])->name('index');
    Route::get('/project/{project}',   [ReportController::class, 'project'])->name('project');
    Route::get('/cost-budget',         [ReportController::class, 'costBudget'])->name('cost-budget');
    Route::get('/resource/{project}', [ReportController::class, 'resourceUsage'])->name('resource');
    Route::get('/evm-summary', [ReportController::class, 'evmSummary'])->name('evm-summary');
    Route::get('/late-tasks', [ReportController::class, 'lateTasksReport'])->name('late-tasks');
    Route::get('/resource-usage-summary', [ReportController::class, 'resourceUsageSummary'])->name('resource-usage-summary');
    Route::get('/portfolio-pdf',        [ReportController::class, 'exportPortfolioPdf'])->name('portfolio-pdf');
    Route::get('/project/{project}/wbs-pdf', [ReportController::class, 'exportProjectWbsPdf'])->name('project.wbs-pdf');
});

require __DIR__ . '/auth.php';
