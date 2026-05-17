<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;

class EvmService
{
    public function calculate(Project $project): array
    {
        $today = Carbon::today();
        $projectStart = $project->start_date ? Carbon::parse($project->start_date) : null;
        $projectEnd = $project->end_date ? Carbon::parse($project->end_date) : null;

        // Jika tidak ada tanggal, kembalikan default
        if (!$projectStart || !$projectEnd) {
            return $this->defaultResult();
        }

        $totalDays = $projectStart->diffInDays($projectEnd) + 1;
        if ($totalDays <= 0) return $this->defaultResult();

        // Hitung hari yang sudah lewat
        $elapsedDays = $today->lt($projectStart) ? 0 : ($today->gt($projectEnd) ? $totalDays : $projectStart->diffInDays($today) + 1);
        $plannedProgress = ($elapsedDays / $totalDays) * 100;

        // PV = Planned Value (Biaya yang seharusnya dikerjakan sampai hari ini)
        $baselineCost = $project->baselines->sum('estimated_cost');
        $pv = ($baselineCost > 0) ? ($baselineCost * $plannedProgress / 100) : ($project->estimated_cost * $plannedProgress / 100);

        // EV = Earned Value (Biaya yang benar-benar dikerjakan berdasarkan progress aktual)
        $actualProgress = $project->tasks()->whereNull('deleted_at')->avg('progress') ?? 0;
        $ev = ($baselineCost > 0) ? ($baselineCost * $actualProgress / 100) : ($project->estimated_cost * $actualProgress / 100);

        // AC = Actual Cost (Biaya yang benar-benar dikeluarkan via timesheet)
        $ac = $project->actual_cost ?? 0;

        // Variances
        $sv = $ev - $pv;
        $cv = $ev - $ac;

        // Indices
        $spi = ($pv > 0 && $ev > 0) ? round($ev / $pv, 2) : null;
        $cpi = ($ac > 0 && $ev > 0) ? round($ev / $ac, 2) : null;

        return [
            'pv'              => round($pv, 2),
            'ev'              => round($ev, 2),
            'ac'              => round($ac, 2),
            'sv'              => round($sv, 2),
            'cv'              => round($cv, 2),
            'spi'             => $spi,
            'cpi'             => $cpi,
            'planned_progress' => round($plannedProgress, 1),
            'actual_progress'  => round($actualProgress, 1),
            'status'          => $this->getStatus($spi, $cpi),
        ];
    }

    private function getStatus($spi, $cpi): string
    {
        if ($spi === null || $cpi === null) return 'Belum Mulai';
        if ($spi >= 0.9 && $cpi >= 0.9) return 'Sehat';
        if ($spi < 0.8 || $cpi < 0.8) return 'Bermasalah';
        return 'Perlu Perhatian';
    }

    private function defaultResult(): array
    {
        return [
            'pv' => 0,
            'ev' => 0,
            'ac' => 0,
            'sv' => 0,
            'cv' => 0,
            'spi' => 0,
            'cpi' => 0,
            'planned_progress' => 0,
            'actual_progress' => 0,
            'status' => 'Tidak Ada Data',
        ];
    }
}
