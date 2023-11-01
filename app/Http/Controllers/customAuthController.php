<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class customAuthController extends Controller
{
    public function loginuser(Request $req)
    {
        $user = User::where('email', '=', $req->email)->first();
        $req->validate([
            'email' => 'email|required',
            'password' => 'required|min:4|max:10'
        ]);
        if ($user) {
            if (($user->password) == ($req->password)) {
                $req->session()->put('loginID', $user->id);
                return redirect('/dashboard');
            } else {
                return back()->with('fail', 'Password Incorrect. Try again !!');
            }
        } else {
            return back()->with('fail', 'Email not Registered !!');
        }
        //  return $user;
    }
    public function logout()
    {
        if (Session::has('loginID')) {
            Session::pull('loginID');
            return redirect('login');
        }
    }

    public function dashboard()
    {
        $datas = DB::table('form_data')->orderBy('id', 'desc')->paginate(5);
        $userData = DB::table('users')->where('id', '=', Session::get('loginID'))->get()->first();
        //return Session::get('loginID');
        //   return $userData;
        return view('dashboard', ['datas' => $datas, 'userData' => $userData]);
    }


    public function redirectToGitHub()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleGitHubCallback()
    {
        // Retrieve the user's GitHub data
        $githubUser = Socialite::driver('github')->user();

        // Check if a user with this GitHub ID already exists in your database
        $user = User::where('social_id', $githubUser->getId())->first();

        if ($user) {
            // User already exists, log them in
            // Store the user's ID in the session
            Session::put('loginID', $user->id);
        } else {
            // User doesn't exist, create a new user
            $newUser = new User;
            // return $newUser->name;
            if (!($githubUser->getName()==NULL))
                $newUser->name = $githubUser->getName();
            $newUser->email = $githubUser->getEmail();
            $newUser->social_id = $githubUser->getId();
            $newUser->avatar = $githubUser->getAvatar();
            $newUser->save();

            // Log in the new user
            Session::put('loginID', $newUser->id);
        }

        return redirect('/dashboard');
    }
}
