<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;

class StatusTransitionService
{
    /**
     * Tabel transisi yang valid per role
     * Format: 'status_asal' => ['status_tujuan_yang_boleh']
     */
    private array $transitions = [
        'not_started' => ['in_progress', 'cancelled'],
        'in_progress' => ['on_hold', 'completed', 'cancelled'],
        'on_hold'     => ['in_progress', 'cancelled'],
        'completed'   => ['in_progress'], // hanya Programmer/CEO yang boleh revert
        'cancelled'   => [],              // tidak bisa diubah lagi
    ];

    /**
     * Role yang boleh revert dari completed → in_progress
     */
    private array $revertAllowedRoles = ['Programmer', 'CEO'];

    public function isAllowed(Task $task, string $newStatus, User $user): bool
    {
        $currentStatus = $task->status;

        // Tidak ada perubahan status — selalu boleh
        if ($currentStatus === $newStatus) {
            return true;
        }

        $allowedTransitions = $this->transitions[$currentStatus] ?? [];

        // Cek apakah transisi valid
        if (!in_array($newStatus, $allowedTransitions)) {
            return false;
        }

        // Revert dari completed → in_progress hanya untuk Programmer/CEO
        if ($currentStatus === 'completed' && $newStatus === 'in_progress') {
            return $user->hasAnyRole($this->revertAllowedRoles);
        }

        return true;
    }

    public function getErrorMessage(string $currentStatus, string $newStatus): string
    {
        if ($currentStatus === 'cancelled') {
            return 'Task yang sudah dibatalkan tidak dapat diubah statusnya.';
        }

        if ($currentStatus === 'completed' && $newStatus === 'in_progress') {
            return 'Hanya Programmer atau CEO yang dapat mengembalikan task ke In Progress.';
        }

        return "Transisi status dari '{$currentStatus}' ke '{$newStatus}' tidak diizinkan.";
    }
}
