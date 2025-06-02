<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

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

        // Forçar o user logado a aparecer primeiro
        $query->orderByRaw('id = ? DESC', [auth()->id()])
            ->orderBy('name');

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'gender' => 'required|in:M,F',
            'password' => 'required|min:8',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = new User($validated);
        $user->type = 'employee';
        $user->password = Hash::make($validated['password']);

        if ($request->hasFile('photo')) {
            $filename = $request->file('photo')->hashName(); // ex: abc123.jpg
            $request->file('photo')->storeAs('users', $filename, 'public'); // guarda em storage/app/public/users
            $user->photo = $filename; // só o nome na BD
        }


        $user->save();

        return redirect()->route('board.users.index')->with('success', 'Employee created successfully.');
    }

    public function create()
    {
        return view('userManagement.create');
    }


    public function edit(User $user)
    {
        return view('userManagement.edit', compact('user'));
    }


    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'gender' => 'required|in:M,F',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Atualiza dados base
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->gender = $validated['gender'];

        // Atualiza a foto, se foi enviada nova
        if ($request->hasFile('photo')) {
            $filename = $request->file('photo')->hashName();
            $request->file('photo')->storeAs('users', $filename, 'public');
            $user->photo = $filename;
        }

        $user->save();

        return redirect()->route('board.users.index')->with('success', 'Employee updated successfully.');
    }


    public function removePhoto(User $user)
    {
        if ($user->photo && $user->photo !== 'anonymous.png') {
            // Apagar do storage, se existir
            $path = storage_path('app/public/users/' . $user->photo);
            if (file_exists($path)) {
                unlink($path);
            }

            // Limpar o campo na BD
            $user->photo = null;
            $user->save();
        }

        return redirect()->back()->with('success', 'Profile photo removed successfully.');
    }


    public function destroy(User $user)
    {
        if ($user->type !== 'employee') {
            return back()->withErrors('Only employee accounts can be deleted.');
        }

        if ($user->id === auth()->id()) {
            return back()->withErrors('You cannot delete your own account.');
        }

        $user->delete(); // soft delete
        return redirect()->route('board.users.index')->with('success', 'Employee deleted successfully.');
    }


}
