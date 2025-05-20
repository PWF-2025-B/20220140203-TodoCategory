<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // $users = User::where('id', '!=', 1)->orderBy('name')->paginate(10);
        // return view('user.index', compact('users'));
        $search = request('search');
        if ($search) {
            $users = User::where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
                ->orderBy('name')
                ->where('id', '!=', 1)
                ->paginate(20)
                ->withQueryString();
        } else {
            $users = User::where('id', '!=', 1)
                ->orderBy('name')
                ->paginate(20);
        }
        return view('user.index', compact('users'));
    }

    public function edit($id)
    {
        return view('user.edit');
    }

    public function makeadmin(User $user)
    {
        $user->timestamps = false;
        $user->is_admin = true;
        $user->save();
        return back()->with('success', 'User ' . $user->name . ' is now an admin.');
    }

    public function removeadmin(User $user)
    {
        if ($user->id != 1) {
            $user->timestamps = false;
            $user->is_admin = false;
            $user->save();
            return back()->with('success', 'User ' . $user->name . ' is no longer an admin.');
        } else {
            return redirect()->route('user.index')->with('error', 'You cannot remove admin privileges from the super admin.');
        }
    }

    public function destroy(User $user)
    {
        if ($user->id != 1) {
            $user->delete();
            return back()->with('success', 'User ' . $user->name . ' deleted successfully.');
        } else {
            return redirect()->route('user.index')->with('error', 'You cannot delete the super admin.');
        }
    }
}
