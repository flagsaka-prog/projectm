<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AttachmentController extends Controller
{
    use AuthorizesRequests;

    public function index(Project $project, Task $task)
    {
        $this->authorize('view', $project);

        $attachments = Attachment::where('task_id', $task->id)
            ->with('user')
            ->latest()
            ->get();

        return view('attachments.index', compact('project', 'task', 'attachments'));
    }

    public function upload(Request $request, Project $project, Task $task)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // max 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments/' . $task->id, 'public');

        Attachment::create([
            'task_id'  => $task->id,
            'user_id'  => auth::id(),
            'filename' => $file->getClientOriginalName(),
            'filepath' => $path,
            'size'     => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        return back()->with('success', 'File berhasil diupload.');
    }

    public function download(Project $project, Task $task, Attachment $attachment)
    {
        return Storage::download($attachment->filepath, $attachment->filename);
    }

    public function destroy(Project $project, Task $task, Attachment $attachment)
    {
        Storage::delete($attachment->filepath);
        $attachment->delete();

        return back()->with('success', 'File berhasil dihapus.');
    }
}
