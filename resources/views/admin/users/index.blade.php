@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Admin</p>
                <h2 class="mt-2 font-bold font-[Fredoka] text-4xl">Manage Users</h2>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex h-11 items-center rounded-xl border px-5 text-sm font-semibold uppercase tracking-[0.04em] transition-all duration-300" style="border-color: var(--theme-accent); color: var(--theme-accent);">+ New User</a>
        </div>

        @if (session('status') === 'user-updated')
            <p class="rounded-xl border px-4 py-3 text-sm" style="border-color: var(--theme-border); color: var(--theme-muted);">User updated.</p>
        @elseif (session('status') === 'user-created')
            <p class="rounded-xl border px-4 py-3 text-sm" style="border-color: var(--theme-border); color: var(--theme-muted);">User created.</p>
        @elseif (session('status') === 'user-deleted')
            <p class="rounded-xl border px-4 py-3 text-sm" style="border-color: var(--theme-border); color: var(--theme-muted);">User deleted.</p>
        @endif

        <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search username or email" class="w-full max-w-sm rounded-xl border px-4 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);" />
            <button type="submit" class="rounded-xl border px-4 py-2 text-sm font-semibold" style="border-color: var(--theme-border);">Search</button>
        </form>

        <div class="overflow-x-auto rounded-tl-[1.75rem] rounded-tr-[1.75rem] rounded-br-[1.75rem] rounded-bl-md border" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b text-xs uppercase tracking-[0.15em]" style="border-color: var(--theme-border); color: var(--theme-muted);">
                        <th class="px-5 py-4">Username</th>
                        <th class="px-5 py-4">Email</th>
                        <th class="px-5 py-4">Role</th>
                        <th class="px-5 py-4">Joined</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-b" style="border-color: color-mix(in srgb, var(--theme-border) 60%, transparent);">
                            <td class="px-5 py-4 font-semibold">
                                <a href="{{ route('profile.show', $user) }}" class="hover:underline">{{ $user->username }}</a>
                            </td>
                            <td class="px-5 py-4" style="color: var(--theme-muted);">{{ $user->email }}</td>
                            <td class="px-5 py-4">
                                @if ($user->is_master_admin)
                                    <span class="inline-flex items-center gap-1 rounded-full border px-3 py-1 text-xs font-semibold uppercase" style="border-color: var(--theme-accent); color: var(--theme-accent);"><x-icon name="crown" class="h-3 w-3" /> Master Admin</span>
                                @elseif ($user->is_admin)
                                    <span class="rounded-full border px-3 py-1 text-xs font-semibold uppercase" style="border-color: var(--theme-accent); color: var(--theme-accent);">Admin</span>
                                @else
                                    <span class="rounded-full border px-3 py-1 text-xs font-semibold uppercase" style="border-color: var(--theme-border); color: var(--theme-muted);">User</span>
                                @endif
                            </td>
                            <td class="px-5 py-4" style="color: var(--theme-muted);">{{ $user->created_at->format('M j, Y') }}</td>
                            <td class="px-5 py-4 text-right">
                                @if ($user->is_master_admin)
                                    <span class="text-xs" style="color: var(--theme-muted);">Protected</span>
                                @elseif ($user->id !== auth()->id())
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.04em] transition-all duration-300" style="border-color: var(--theme-border);">
                                                {{ $user->is_admin ? 'Revoke admin' : 'Make admin' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Permanently delete {{ $user->username }}? This removes their account, reviews, lists, and all other data. This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.04em] transition-all duration-300" style="border-color: #ef4444; color: #ef4444;">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs" style="color: var(--theme-muted);">(you)</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $users->links() }}
    </div>
@endsection
