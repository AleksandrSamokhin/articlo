<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'username')->paginate(10);

        return view('users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $posts = $user->posts()->paginate(10);

        return view('users.show', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }

    public function loginApi(Request $request)
    {
        $incomingFields = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (auth()->attempt($incomingFields)) {
            $user = User::where('email', $incomingFields['email'])->first();
            $token = $user->createToken('ourapptoken')->plainTextToken;

            return $token;
        }

        abort(403, 'access denied');
    }
}
