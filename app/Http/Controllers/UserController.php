<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->whereNull('deleted_at');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('blocked')) {
            $query->where('blocked', $request->blocked);
        }

        $users = $query->paginate(15);
        return view('userManagement.index', compact('users'));
    }

    public function toggleBlock(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors('You cannot block yourself.');
        }

        $user->update(['blocked' => !$user->blocked]);
        return back()->with('success', 'User status updated.');
    }

    public function cancelMembership(User $user)
    {
        if ($user->type !== 'member' && $user->type !== 'board') return back();
        if ($user->id === auth()->id()) return back()->withErrors('Cannot cancel your own membership.');

        $user->delete(); // soft delete
        return back()->with('success', 'Membership cancelled.');
    }

    public function promote(User $user)
    {
        if ($user->type !== 'member') return back();
        $user->update(['type' => 'board']);
        return back()->with('success', 'User promoted.');
    }

    public function demote(User $user)
    {
        if ($user->type !== 'board') return back();
        if ($user->id === auth()->id()) return back()->withErrors('Cannot demote yourself.');
        $user->update(['type' => 'member']);
        return back()->with('success', 'User demoted.');
    }

}
