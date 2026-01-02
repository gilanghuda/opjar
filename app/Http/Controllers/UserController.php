<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::with('roles')->paginate(25));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:6',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'active' => 'nullable|boolean',
        ]);

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        if (isset($data['name'])) $user->name = $data['name'];
        if (isset($data['email'])) $user->email = $data['email'];
        if (isset($data['active'])) $user->active = $data['active'];

        $user->save();

        if (! empty($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        return response()->json($user->load('roles'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['deleted' => true]);
    }
}
