<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Project
            'project.view',
            'project.create',
            'project.update',
            'project.delete',
            'project.archive',
            // Task
            'task.view',
            'task.create',
            'task.update',
            'task.delete',
            'task.assign',
            'task.dependency',
            'task.resource',
            // Resource Master
            'resource.view',
            'resource.create',
            'resource.update',
            'resource.delete',
            // User Management
            'user.manage',
            // Report
            'report.view',
            // Gantt
            'gantt.view',
            // Audit & Log
            'audit.view',
            'login-history.view',
            // Notification
            'notification.view',
            'notification.manage',
            // Comment & Attachment
            'comment.view',
            'comment.create',
            'comment.delete',
            'attachment.view',
            'attachment.upload',
            'attachment.delete',
            // Presence
            'presence.view',
            // logo
            'setting.manage',
            // Programmer Tools
            'tools.cache',
            'tools.maintenance',
            'tools.log-viewer',
            'tools.migration',

        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ── CEO — Full akses ──────────────────────────────
        $ceo = Role::where('name', 'CEO')->first();
        $ceo->syncPermissions([
            'project.view',
            'project.create',
            'project.update',
            'project.delete',
            'project.archive',
            'task.view',
            'task.create',
            'task.update',
            'task.delete',
            'task.assign',
            'task.dependency',
            'task.resource',
            'resource.view',
            'resource.create',
            'resource.update',
            'resource.delete',
            'user.manage',
            'report.view',
            'gantt.view',
            'audit.view',
            'login-history.view',
            'notification.view',
            'notification.manage',
            'presence.view',
            'setting.manage',
        ]);

        // ── COO — Semua akses seperti CEO ──────────────────
        $coo = Role::where('name', 'COO')->first();
        $coo->syncPermissions($ceo->permissions->pluck('name')->toArray());

        // ── CTO — Monitoring + Tools ──────────────────────
        $cto = Role::where('name', 'CTO')->first();
        $cto->syncPermissions([
            'project.view',
            'task.view',
            'report.view',
            'gantt.view',
            'audit.view',
            'login-history.view',
            'notification.view',
            'notification.manage',
            'presence.view',
            'setting.manage',
        ]);

        // ── CFO — Monitoring Keuangan ────────────────────
        $cfo = Role::where('name', 'CFO')->first();
        $cfo->syncPermissions([
            'project.view',
            'report.view',
            'audit.view',
            'notification.view',
            'presence.view',
        ]);

        // ── VP — Portofolio: buat project, assign PM ──────
        $vp = Role::where('name', 'VP')->first();
        $vp->syncPermissions([
            'project.view',
            'project.create',
            'project.update',
            'project.delete',
            'project.archive',
            'task.view',
            'user.manage',
            'report.view',
            'gantt.view',
            'audit.view',
            'login-history.view',
            'notification.view',
            'notification.manage',
            'presence.view',
            'setting.manage',
        ]);

        // ── PM — Project: kelola task & team ─────────────
        $pm = Role::where('name', 'PM')->first();
        $pm->syncPermissions([
            'project.view',
            'task.view',
            'task.create',
            'task.update',
            'task.delete',
            'task.assign',
            'task.dependency',
            'task.resource',
            'resource.view',
            'resource.create',
            'resource.update',
            'gantt.view',
            'report.view',
            'notification.view',
            'notification.manage',
            'presence.view',
            'comment.view',
            'comment.create',
            'comment.delete',
            'attachment.view',
            'attachment.upload',
            'attachment.delete',
        ]);

        // ── Team Lead — View + assign developer ────────────
        $teamLead = Role::where('name', 'Team Lead')->first();
        $teamLead->syncPermissions([
            'project.view',
            'task.view',
            'task.create',    // <-- TAMBAHKAN INI
            'task.update',    // <-- TAMBAHKAN INI (Opsional tapi direkomendasikan)
            'task.assign',
            'notification.view',
            'notification.manage',
            'presence.view',
            'comment.view',
            'comment.create',
            'comment.delete',
            'attachment.view',
            'attachment.upload',
            'attachment.delete',
        ]);

        // ── Developer — Hanya kerjakan task ────────────────
        $dev = Role::where('name', 'Developer')->first();
        $dev->syncPermissions([
            'project.view',
            'task.view',
            'task.update',
            'notification.view',
            'notification.manage',
            'presence.view',
            'comment.view',
            'comment.create',
            'comment.delete',
            'attachment.view',
            'attachment.upload',
            'attachment.delete',
        ]);

        // ── Programmer — Invisible maintenance ─────────────
        $prog = Role::where('name', 'Programmer')->first();
        $prog->syncPermissions([
            'audit.view',
            'login-history.view',
            'tools.cache',
            'tools.maintenance',
            'tools.log-viewer',
            'tools.migration',
            'notification.view',
            'notification.manage',
            'presence.view',
            'setting.manage',
        ]);
    }
}
