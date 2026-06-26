<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        // Only admin can access this page
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    /**
     * Update user permissions.
     */
    public function updatePermissions(Request $request, User $user)
    {
        // Only admin can update permissions
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'is_admin' => 'nullable|boolean',
            'linked_to' => 'nullable|boolean',
        ]);

        $isAdmin = $request->has('is_admin');
        $isLinked = $request->has('linked_to');

        // If making admin, remove linked_to. Admins get master admin data via is_admin flag.
        // If linking, remove is_admin (linked non-admin user).
        $user->update([
            'is_admin'  => $isAdmin,
            'linked_to' => (!$isAdmin && $isLinked) ? (\App\Models\User::getMasterAdminIds()[0] ?? auth()->id()) : null,
        ]);

        return redirect()->back()->with('success', 'Permissions updated successfully for ' . $user->name);
    }
}
