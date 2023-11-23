<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginRegisterController extends Controller
{
    public function landing(){
        $success = session('success') ?? null;
        $error = session('error') ?? null;
        $err = session('err') ?? null;

        return view('landing', compact('success', 'error', 'err'));
    }

    public function loginAdmin(){
        $success = session('success') ?? null;
        $error = session('error') ?? null;
        $err = session('err') ?? null;

        return view('admin.login', compact('success', 'error', 'err'));
    }

    public function loginProcess(LoginRequest $request){
        if(User::where('email', $request->email)->where('role', $request->role)->first() && Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            return redirect('/consultation');
        }
        else{
            if($request->role != 'admin'){
                return redirect('/')->with('err', 'Akun dengan role yang dipilih tidak terdaftar!');
            }
            else{
                return redirect('/admin')->with('err', 'Akun dengan role yang dipilih tidak terdaftar!');
            }
        }
    }

    public function registerProcess(RegisterRequest $request){
        DB::beginTransaction();
        try{
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'jenis_kelamin' => $request->jenis_kelamin,
                'role' => $request->role,
                'telephone' => $request->telephone
            ]);

            DB::commit();
        } catch(\Exception $err){
            return redirect('/')->with('err', 'Failed to register, please try again');
        }

        $success = 'Registered Succesfully';

        return redirect('/')->with('success', $success);
    }

    public function logout(Request $request){
        Auth::logout();
 
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect('/');
    }
}
