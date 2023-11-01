<?php

use App\Mail\Thanks;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\formDataController;
use App\Http\Controllers\customAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    return view('welcome');
});
Route::post('/form', [formDataController::class, 'addData'])->name('form');
Route::get('/deleterow/{id}', [formDataController::class, 'deleterow'])->name('deleter')->Middleware('islogin');
Route::post('/form2', [formDataController::class, 'addData2'])->name('form2');
Route::view('/login', 'login')->name('login')->Middleware('islogout');
Route::post('/dashboards', [customAuthController::class, 'loginuser'])->name('loginuserss');
Route::get('/dashboard', [customAuthController::class, 'dashboard'])->Middleware('islogin');
Route::get('/logout', [customAuthController::class, 'logout'])->name('Logout');





Route::get('/auth/github', [customAuthController::class, 'redirectToGitHub']);
Route::get('/auth/github/callback', [customAuthController::class, 'handleGitHubCallback']);








Route::get('/login/google', function () {
    return Socialite::driver('google')->redirect();
});


Route::get('/login/google/callback', function () {
    $socialUser = Socialite::driver('google')->user();

    // Access the user's profile information
    $id = $socialUser->getId(); // User ID
    $name = $socialUser->getName(); // Full name
    $email = $socialUser->getEmail(); // Email address
    $avatar = $socialUser->getAvatar(); // Profile picture URL


    $user = User::where('social_id', $id)->first();
    if ($user) {
        Session::put('loginID', $user->id);
    } else {
        // User doesn't exist, create a new user
        $newUser = new User;
        // return $newUser->name;
        if (!($name == NULL))
            $newUser->name = $name;
        $newUser->email = $email;
        $newUser->social_id = $id;
        $newUser->avatar = $avatar;
        $newUser->save();

        // Log in the new user
        Session::put('loginID', $newUser->id);
    }
    return redirect('/dashboard');
});



Route::get('dataa', function () {
    $response = Http::get('https://fakerapi.it/api/v1/texts', [
        '_quantity' => 2,
        '_characters' => 500,
    ]);
    $data = $response['data'];
    return view('Sample')->with(["data"=>$data]);
});
