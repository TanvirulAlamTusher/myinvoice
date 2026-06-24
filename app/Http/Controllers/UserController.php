<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
public function index()
{
    $users = User::where('id', '!=', auth()->id())->get();

    return view('users.index', compact('users'));
}

   public function create()
{
    $roles = Role::all();

    return view('users.create', compact('roles'));
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'phone' => 'required|unique:users',
        'email' => 'nullable|unique:users',
        'password' => 'required|min:4',
        'role' => 'required|exists:roles,name',
    ]);

    $user = User::create([
        'name' => $request->name,
        'phone' => $request->phone,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    // Assign Role
    $user->assignRole($request->role);

    return redirect('/users')->with('success', 'User created');
}

  public function edit($id)
{
    $user = User::findOrFail($id);

    $roles = Role::all();

    return view('users.edit', compact('user', 'roles'));
}
public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',

        'phone' => [
            'required',
            Rule::unique('users', 'phone')->ignore($id),
        ],

        'email' => [
            'nullable',
            'email',
            Rule::unique('users', 'email')->ignore($id),
        ],

        'password' => 'nullable|min:4',

        'role' => 'required|exists:roles,name',
    ]);

    $data = [
        'name' => $request->name,
        'phone' => $request->phone,
        'email' => $request->email,
    ];

    if ($request->filled('password')) {
        $data['password'] = bcrypt($request->password);
    }

    $user->update($data);

    // Replace old role
    $user->syncRoles([$request->role]);

    return redirect('/users')->with('success', 'User updated successfully');
}
    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return back()->with('success', 'User deleted');
    }
}
