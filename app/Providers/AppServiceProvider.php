<?php

namespace App\Providers;

use App\Models\Project;
use App\Policies\ProjectPolicy;
use App\Models\Task;
use App\Policies\TaskPolicy;
use App\Services\CostService;
use App\Services\DependencyService;
use App\Services\NotificationService;
use App\Services\PresenceService;
use App\Services\ProjectService;
use App\Services\ReportService;
use App\Services\SchedulingService;
use App\Services\TaskService;
use App\Services\WorkloadService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProjectService::class);
        $this->app->singleton(TaskService::class);
        $this->app->singleton(DependencyService::class);
        $this->app->singleton(WorkloadService::class);
        $this->app->singleton(PresenceService::class);
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(CostService::class);

        // SchedulingService butuh inject DependencyService & TaskService
        $this->app->singleton(SchedulingService::class, function ($app) {
            return new SchedulingService(
                $app->make(DependencyService::class),
                $app->make(TaskService::class),
            );
        });

        // ReportService butuh inject CostService & WorkloadService
        $this->app->singleton(ReportService::class, function ($app) {
            return new ReportService(
                $app->make(CostService::class),
                $app->make(WorkloadService::class),
            );
        });
    }

    public function boot(): void
    {
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);

        // Login redirect per role
        $redirectMap = [
            'CEO'       => 'ceo.dashboard',
            'COO'       => 'coo.dashboard',
            'CTO'       => 'cto.dashboard',
            'CFO'       => 'cfo.dashboard',
            'VP'        => 'vp.dashboard',
            'PM'        => 'pm.dashboard',
            'Team Lead' => 'team-lead.dashboard',
            'Developer' => 'developer.dashboard',
            'Programmer' => 'programmer.dashboard',
        ];

        $role = auth::user()?->roles->first()?->name ?? null;

        if ($role && isset($redirectMap[$role])) {
            \Illuminate\Support\Facades\Redirect::to($redirectMap[$role]);
        }

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\RecordLoginHistory::class
        );
    }
}
