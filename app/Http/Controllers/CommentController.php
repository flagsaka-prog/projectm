<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function index(Project $project, Task $task)
    {
        $this->authorize('view', $project);

        $comments = Comment::where('task_id', $task->id)
            ->root()
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        return view('comments.index', compact('project', 'task', 'comments'));
    }

    public function store(Request $request, Project $project, Task $task)
    {
        $validated = $request->validate([
            'body'      => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        Comment::create([
            'task_id'   => $task->id,
            'user_id'   => auth::id(),
            'body'      => $validated['body'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return back()->with('success', 'Komentar ditambahkan.');
    }

    public function destroy(Project $project, Task $task, Comment $comment)
    {
        $comment->delete();

        return back()->with('success', 'Komentar dihapus.');
    }
}
