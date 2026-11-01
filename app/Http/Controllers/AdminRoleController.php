<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminRoleController extends Controller
{
    /**
     * Display a listing of all users and administrators with their roles.
     */
    public function index(Request $request): View
    {
        Gate::authorize('manage-roles');

        $users = User::query()
            ->filter($request->only(['search', 'role']))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $roleCounts = User::roleCounts();

        return view('admin.roles.index', compact('users', 'roleCounts'));
    }

    /**
     * Create a new administrator / user account directly from the Super Admin panel.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-roles');

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', 'string', Rule::in(User::ROLES)],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = new User([
            'name'     => trim($validated['name']),
            'email'    => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
        ]);

        // Explicitly set role through server-side logic
        $user->forceFill([
            'role'              => $validated['role'],
            'email_verified_at' => now(),
        ])->save();

        return redirect()->route('admin.roles.index')
            ->with('success', "New {$user->roleLabel()} account for '{$user->name}' ({$user->email}) created successfully.");
    }

    /**
     * Update the assigned role for a user / sub-admin.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('manage-roles');

        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(User::ROLES)],
        ]);

        $newRole = $validated['role'];

        // Safety check: Prevent Super Admin from demoting their own account
        if ($user->id === auth()->id() && $newRole !== User::ROLE_SUPER_ADMIN) {
            return back()->withErrors([
                'role' => 'Security restriction: You cannot demote your own Super Admin account.',
            ]);
        }

        $oldRoleLabel = $user->roleLabel();
        $user->forceFill(['role' => $newRole])->save();
        $newRoleLabel = $user->roleLabel();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role for {$user->name} has been updated from '{$oldRoleLabel}' to '{$newRoleLabel}'.");
    }
}
