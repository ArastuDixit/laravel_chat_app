<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
class UserController extends Controller
{
    public function index()
    {
        // Check if the user is already authenticated
        if (Auth::check()) {
            return redirect()->route('messages');
        }

        return view('login');
    }


    public function userLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');


        if (Auth::attempt($credentials)) {
            return redirect()->route('messages')->with('login-success', 'Signed in Successfully!');

        }

        return redirect("login")->withSuccess('Login details are not valid');
    }



    public function registration()
    {
        return view('registration');
    }


    public function userRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $data = $request->all();
        $check = $this->create($data);

        return redirect("login")->with('success-registration','Congratulations! you are successfully registered.');
    }


    public function create(array $data)
    {
      return User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password'])
      ]);
    }


     public function messages()
    {
        if(Auth::check()){
            return view('messages');
        }

        return redirect("login")->withSuccess('are not allowed to access');
    }



    public function forgotPassword()
    {
                  return view('forgot-password');

    }

    public function signOut() {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }

}
