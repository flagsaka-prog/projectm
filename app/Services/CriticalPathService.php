<?php

namespace App\Services;

use App\Models\Task;

class CriticalPathService
{
    public function getCriticalTaskIds(int $projectId): array
    {
        $tasks = Task::where('project_id', $projectId)
            ->whereNull('deleted_at')
            ->with('dependencies')
            ->get()
            ->keyBy('id');

        if ($tasks->isEmpty()) return [];

        $cpm = [];
        foreach ($tasks as $id => $task) {
            $cpm[$id] = [
                'ES' => 0,
                'EF' => 0,
                'LS' => 0,
                'LF' => 0,
                'duration' => $task->duration ?? 0,
            ];
        }

        // 1. Forward Pass (Finish-to-Start)
        $calculated = [];
        $changed = true;
        while ($changed) {
            $changed = false;
            foreach ($tasks as $id => $task) {
                if (in_array($id, $calculated)) continue;

                $maxES = 0;
                $ready = true;

                foreach ($task->dependencies as $dep) {
                    $depId = $dep->depends_on_task_id;
                    if (!isset($cpm[$depId]) || !in_array($depId, $calculated)) {
                        $ready = false;
                        break;
                    }
                    $maxES = max($maxES, $cpm[$depId]['EF']);
                }

                if ($ready) {
                    $cpm[$id]['ES'] = $maxES;
                    $cpm[$id]['EF'] = $maxES + $cpm[$id]['duration'];
                    $calculated[] = $id;
                    $changed = true;
                }
            }
        }

        // 2. Cari Durasi Project
        $projectEnd = 0;
        foreach ($cpm as $c) $projectEnd = max($projectEnd, $c['EF']);

        // 3. Backward Pass
        foreach ($tasks as $id => $task) {
            $hasSuccessor = false;
            foreach ($tasks as $sid => $stask) {
                if ($stask->dependencies->contains('depends_on_task_id', $id)) {
                    $hasSuccessor = true;
                    break;
                }
            }
            if (!$hasSuccessor) {
                $cpm[$id]['LF'] = $projectEnd;
                $cpm[$id]['LS'] = $projectEnd - $cpm[$id]['duration'];
            }
        }

        $calculatedRev = [];
        $changed = true;
        while ($changed) {
            $changed = false;
            foreach ($tasks as $id => $task) {
                if (in_array($id, $calculatedRev)) continue;
                if ($cpm[$id]['LF'] == 0 && $cpm[$id]['EF'] != $projectEnd) continue;

                $minLF = PHP_INT_MAX;
                $ready = true;

                foreach ($tasks as $sid => $stask) {
                    if ($stask->dependencies->contains('depends_on_task_id', $id)) {
                        if (!in_array($sid, $calculatedRev)) {
                            $ready = false;
                            break;
                        }
                        $minLF = min($minLF, $cpm[$sid]['LS']);
                    }
                }

                if ($ready && $minLF != PHP_INT_MAX) {
                    $cpm[$id]['LF'] = $minLF;
                    $cpm[$id]['LS'] = $minLF - $cpm[$id]['duration'];
                    $calculatedRev[] = $id;
                    $changed = true;
                }
            }
        }

        // 4. Tentukan Critical (Float = 0)
        $criticalIds = [];
        foreach ($cpm as $id => $c) {
            if ($c['LS'] == $c['ES']) $criticalIds[] = $id;
        }

        return $criticalIds;
    }
}
