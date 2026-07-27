<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->string('search')->toString(), fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->orderBy('username')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $request->string('search')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'is_admin' => ['sometimes', 'boolean'],
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => $request->boolean('is_admin'),
            'email_verified_at' => now(),
        ]);

        NotificationSetting::create(['user_id' => $user->id]);

        return redirect()->route('admin.users.index')->with('status', 'user-created');
    }

    public function toggleAdmin(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot change your own admin status.');

        $user->update(['is_admin' => ! $user->is_admin]);

        return redirect()->route('admin.users.index')->with('status', 'user-updated');
    }
}
