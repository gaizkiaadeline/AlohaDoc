<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Doctor;
use App\Models\LogError;
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

        $specialists = Doctor::all();

        return view('landing', compact('success', 'error', 'err', 'specialists'));
    }

    public function loginAdmin(){
        $success = session('success') ?? null;
        $error = session('error') ?? null;
        $err = session('err') ?? null;

        return view('admin.login', compact('success', 'error', 'err'));
    }

    public function loginProcess(LoginRequest $request){
        $user = User::where('email', $request->email)
                    ->where('role', $request->role)
                    ->first();

        if($user->is_active == 'Not Active'){
            return redirect('/')->with('err', 'Akun anda berada dalam status tidak aktif, mohon hubungi admin untuk informasi lebih lanjut!');
        }

        if($user && Auth::attempt(['email' => $request->email, 'password' => $request->password])){
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
            $specialistId = null;

            if($request->specialist != '-'){
                $specialistId = $request->specialist;
            }
            
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'jenis_kelamin' => $request->jenis_kelamin,
                'role' => $request->role,
                'telephone' => $request->telephone,
                'is_active' => $request->role == 'doctor' ? 'Not Active' : 'Active',
                'specialist_id' => $specialistId
            ]);

            DB::commit();
        } catch(\Exception $err){
            DB::rollBack();
            LogError::insertLogError($err->getMessage());
            return redirect('/')->with('err', 'Failed to register, please try again');
        }

        if($request->role == 'patient'){
            $success = 'Akun berhasil terdaftar! Mohon login untuk mengakses fitur!';
        }
        else{
            $success = 'Akun berhasil terdaftar! Mohon tunggu admin untuk mengaktifkan akun Anda!';
        }

        return redirect('/')->with('success', $success);
    }

    public function logout(Request $request){
        Auth::logout();
 
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect('/');
    }
}
