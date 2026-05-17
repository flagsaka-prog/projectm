<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityService;


class UserController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $roleName = auth::user()->roles->first()->name ?? null;

        $users = User::with('roles');

        if ($roleName === 'VP') {
            $users->whereHas('roles', fn($q) => $q->whereIn('name', ['PM', 'Team Lead', 'Developer']));
        } else {
            $users->whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['Programmer', 'CEO']));
        }

        $users = $users->latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roleName = auth::user()->roles->first()->name ?? null;

        if ($roleName === 'VP') {
            $roles = Role::whereIn('name', ['PM', 'Team Lead', 'Developer'])->get();
        } else {
            $roles = Role::whereNotIn('name', ['Programmer', 'CEO'])->get();
        }

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        if ($this->isHigherRole([$request->role])) {
            abort(403, 'Tidak boleh membuat user dengan level yang sama atau lebih tinggi.');
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|exists:roles,name',
            'is_active' => 'nullable|in:0,1',
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'is_active' => $validated['is_active'] ?? 1,
        ]);

        $user->assignRole($validated['role']);

        (new ActivityService())->log("Menambahkan user \"{$validated['name']}\" dengan role {$validated['role']}", ['user_id' => $user->id]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        if ($this->isHigherRole($user->roles->pluck('name')->toArray())) {
            abort(403, 'Tidak boleh mengedit user dengan level yang sama atau lebih tinggi.');
        }

        $roleName = auth::user()->roles->first()->name ?? null;

        if ($roleName === 'VP') {
            $roles = Role::whereIn('name', ['PM', 'Team Lead', 'Developer'])->get();
        } else {
            $roles = Role::whereNotIn('name', ['Programmer', 'CEO'])->get();
        }

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if ($this->isHigherRole($user->roles->pluck('name')->toArray())) {
            abort(403, 'Tidak boleh mengupdate user dengan level yang sama atau lebih tinggi.');
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|exists:roles,name',
            'is_active' => 'nullable|in:0,1',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'is_active' => $validated['is_active'] ?? 0,
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        (new ActivityService())->log("Mengupdate user \"{$user->name}\" role menjadi {$validated['role']}", ['user_id' => $user->id]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    private function isHigherRole(array $targetRoleNames): bool
    {
        $myLevel = auth::user()->roles->first()->level ?? 0;

        $targetLevel = \Spatie\Permission\Models\Role::whereIn('name', $targetRoleNames)
            ->max('level') ?? 0;

        return $targetLevel >= $myLevel;
    }
}
