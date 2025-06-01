@extends('layouts.app')

@section('content')
@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)"
        class="mb-4 p-3 bg-green-100 dark:bg-green-200 text-green-700 rounded-md shadow-sm">
        {{ session('success') }}
    </div>
    <meta http-equiv="refresh" content="5">
@endif

<div class="max-w-6xl mx-auto mt-16 bg-white dark:bg-gray-800 p-5 rounded-2xl shadow">
    <a href="{{ route('catalog') }}"
        class="inline-block mb-6 ml-4 text-sm text-lime-700 dark:text-lime-400 hover:underline">
        ← Back to Home
    </a>

    <div class="mb-14 relative flex items-center justify-center">
        <h1 class="text-3xl font-bold text-yellow-700 dark:text-yellow-700">User Management</h1>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-wrap justify-center gap-4 mb-6">
        <select name="type"
            class="px-9 py-2 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
            <option value="">All Types</option>
            <option value="member">Member</option>
            <option value="board">Board</option>
            <option value="employee">Employee</option>
            <option value="pending_member">Pending</option>
        </select>

        <select name="blocked"
            class="px-9 py-2 rounded-md border-gray-300 shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-700 dark:text-white">
            <option value="">All Status</option>
            <option value="0">Active</option>
            <option value="1">Blocked</option>
        </select>

        <button type="submit"
            class="bg-lime-600 text-white py-2 px-8 rounded-md hover:bg-lime-700 transition text-md">
            Apply Filters
        </button>
    </form>

    <div class="overflow-x-auto ml-6 mr-6">
        <table class="w-full text-sm text-left text-gray-800 dark:text-gray-200 border-separate border-spacing-y-3">
            <thead>
                <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Type</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="bg-white dark:bg-gray-700 shadow rounded-lg">
                        <td class="px-4 py-2 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2 capitalize">{{ $user->type }}</td>
                        <td class="px-4 py-2">
                            @if($user->blocked)
                                <span class="text-red-600 font-semibold">Blocked</span>
                            @else
                                <span class="text-green-600 font-medium">Active</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 space-y-1">
                            <!-- Block/Unblock -->
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('board.users.block', $user) }}">
                                    @csrf @method('PATCH')
                                    <button class="text-yellow-500 hover:underline">
                                        {{ $user->blocked ? 'Unblock' : 'Block' }}
                                    </button>
                                </form>
                            @endif

                            {{-- Promote/Demote --}}
                            @if($user->type === 'member')
                                <form method="POST" action="{{ route('board.users.promote', $user) }}">
                                    @csrf @method('PATCH')
                                    <button class="text-blue-600 hover:underline">Promote to Board</button>
                                </form>
                            @elseif($user->type === 'board' && $user->id !== auth()->id())
                                <form method="POST" action="{{ route('board.users.demote', $user) }}">
                                    @csrf @method('PATCH')
                                    <button class="text-orange-600 hover:underline">Demote to Member</button>
                                </form>
                            @endif

                            {{-- Cancel Membership --}}
                            @if(in_array($user->type, ['member', 'board']) && $user->id !== auth()->id())
                                <form method="POST" action="{{ route('board.users.cancel', $user) }}">
                                    @csrf @method('PATCH')
                                    <button class="text-red-600 hover:underline">Cancel Membership</button>
                                </form>
                            @endif

                            {{-- Employees only: edit/delete --}}
                            @if($user->type === 'employee')
                                <a href="{{ route('board.users.edit', $user) }}"
                                    class="text-indigo-500 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('board.users.destroy', $user) }}">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Remove</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500 dark:text-gray-400">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-8 flex justify-center">
        <div class="pagination w-full flex justify-center">
            {{ $users->links('vendor.pagination.tailwind-dark') }}
        </div>
    </div>
</div>
@endsection
