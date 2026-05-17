<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Timesheet;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimesheetController extends Controller
{
    use AuthorizesRequests;

    public function index(Project $project, Task $task)
    {
        $this->authorize('view', $task);

        $timesheets = Timesheet::where('task_id', $task->id)
            ->with('user')
            ->orderBy('work_date', 'desc')
            ->get();

        return view('timesheets.index', compact('project', 'task', 'timesheets'));
    }

    public function store(Request $request, Project $project, Task $task)
    {
        $validated = $request->validate([
            'work_date'    => 'required|date',
            'hours_worked' => 'required|numeric|min:0.5|max:24',
            'description'  => 'nullable|string|max:500',
        ]);

        Timesheet::updateOrCreate(
            [
                'task_id'   => $task->id,
                'user_id'   => auth::id(),
                'work_date' => $validated['work_date'],
            ],
            [
                'hours_worked' => $validated['hours_worked'],
                'description'  => $validated['description'],
            ]
        );

        // Hitung ulang actual cost
        (new \App\Services\CostService())->recalculateTaskActualCostFromTimesheet($task);

        return back()->with('success', 'Jam kerja berhasil dicatat.');
    }

    public function destroy(Project $project, Task $task, Timesheet $timesheet)
    {
        $timesheet->delete();

        // Hitung ulang actual cost setelah data dihapus
        (new \App\Services\CostService())->recalculateTaskActualCostFromTimesheet($task);

        return back()->with('success', 'Data timesheet berhasil dihapus.');
    }
}
