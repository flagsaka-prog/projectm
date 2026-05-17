<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityService;

class ProjectController extends Controller
{

    use AuthorizesRequests;
    public function index()
    {
        $projects = Project::visibleTo(Auth::user())
            ->with(['Manager'])
            ->where('status', '!=', 'archived')
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $this->authorize('create', Project::class);

        $managers = User::whereHas('roles', fn($q) => $q->where('name', 'PM'))
            ->where('is_active', true)
            ->get();

        return view('projects.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'description'         => 'nullable|string',
            'assigned_manager_id' => 'required|exists:users,id',
            'start_date'          => 'required|date',
            'end_date'            => 'required|date|after_or_equal:start_date',
            'budget'              => 'required|numeric|min:0',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status']     = 'planning';

        $project = Project::create($validated);

        // Tambahkan CEO & PM ke project_user
        $project->members()->attach(Auth::id(), ['role_in_project' => 'CEO']);
        $project->members()->attach($validated['assigned_manager_id'], ['role_in_project' => 'PM']);

        // Kirim notifikasi ke PM
        $pm = User::find($validated['assigned_manager_id']);
        (new \App\Services\NotificationService())->projectAssigned($project, $pm);

        (new ActivityService())->log("Membuat project \"{$validated['name']}\"", ['project_id' => $project->id]);

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil dibuat.');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);
        $project->load(['Manager', 'tasks', 'members', 'baselines']);

        $evm = (new \App\Services\EvmService())->calculate($project);

        return view('projects.show', compact('project', 'evm'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        $managers = User::whereHas('roles', fn($q) => $q->where('name', 'PM'))
            ->where('is_active', true)
            ->get();

        return view('projects.edit', compact('project', 'managers'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'description'         => 'nullable|string',
            'assigned_manager_id' => 'required|exists:users,id',
            'start_date'          => 'required|date',
            'end_date'            => 'required|date|after_or_equal:start_date',
            'budget'              => 'required|numeric|min:0',
            'status'              => 'required|in:planning,active,on_hold,completed,archived',
        ]);

        $project->update($validated);

        (new ActivityService())->log("Mengupdate project \"{$project->name}\"", ['project_id' => $project->id]);

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil diupdate.');
    }

    public function archive(Project $project)
    {
        $this->authorize('update', $project);

        $project->update(['status' => 'archived']);

        (new ActivityService())->log("Mengarsipkan project \"{$project->name}\"", ['project_id' => $project->id]);

        return redirect()->route('projects.index')  // BENAR
            ->with('success', 'Project berhasil diarsipkan.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();

        (new ActivityService())->log("Menghapus project \"{$project->name}\"", ['project_id' => $project->id, 'deleted' => true]);

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil dihapus.');
    }

    public function restoreTrashedProject($id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);
        $project->restore();

        return back()->with('success', 'Project berhasil dipulihkan.');
    }

    public function setBaseline(Project $project)
    {
        $this->authorize('update', $project);

        (new \App\Services\BaselineService())->setBaseline($project);

        (new ActivityService())->log("Menetapkan baseline untuk project \"{$project->name}\"", ['project_id' => $project->id]);

        return back()->with('success', 'Baseline berhasil disimpan.');
    }

    public function levelResources(Project $project)
    {
        $this->authorize('update', $project);

        $shiftedTasks = (new \App\Services\ResourceLevelingService())->level($project);

        if (empty($shiftedTasks)) {
            return back()->with('info', 'Tidak ada bentrok jadwal. Resource sudah optimal.');
        }

        $logMessage = "Resource leveling: " . count($shiftedTasks) . " task digeser.";
        (new ActivityService())->log($logMessage, ['project_id' => $project->id]);

        return back()->with('success', count($shiftedTasks) . " task berhasil digeser otomatis untuk mengatasi bentrok resource.");
    }

    public function archived()
    {
        $projects = Project::visibleTo(Auth::user())
            ->with(['Manager'])
            ->where('status', 'archived')
            ->latest()
            ->paginate(10);

        return view('projects.archived', compact('projects'));
    }

    public function restore(Project $project)
    {
        $this->authorize('update', $project);

        $project->update(['status' => 'active']);

        (new ActivityService())->log("Memulihkan project \"{$project->name}\" dari arsip", ['project_id' => $project->id]);

        return back()->with('success', 'Project berhasil dipulihkan.');
    }
}
