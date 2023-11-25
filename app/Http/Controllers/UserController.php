<?php

namespace App\Http\Controllers;

use App\Models\LogError;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index(Request $request){
        if($request->ajax()){
            $users = User::orderBy('created_at', 'DESC')
                        ->get()
                        ->transform(function ($dt){
                            $dt->role = $dt->role == 'patient' ? 'Pasien' : ($dt->role == 'doctor' ? 'Dokter' : 'Admin');

                            return $dt;
                        });

            return DataTables::of($users)
                ->editColumn('created_at', function ($p) {
                    return [
                        'display' => date('d-m-Y H:i:s', strtotime($p->created_at)),
                        'timestamp' => date('Y-m-d H:i:s', strtotime($p->created_at))
                    ];
                })
                ->addColumn('actions', function ($p) {
                    $returnedValue = [];
                    
                    if($p->role != 'Admin'){
                        array_push($returnedValue, [
                            "route" => route('user.activate', ['user' => $p->id]),
                            "attr_id" => $p->id,
                            "icon" => 'fas fa-fw fa-user-tag',
                            "label" => 'Ganti Status',
                            "btnStyle" => 'danger',
                            "btnClass" => 'Status'
                        ]);
                    }

                    return $returnedValue;
                })
                ->make();
        }

        $success = session('success') ?? null;
        $error = session('error') ?? null;
        $route = 'user';

        return view('admin.user.index', compact('route', 'success', 'error'));
    }

    public function changeStatus(User $user){
        DB::beginTransaction();

        try{
            $user->is_active = $user->is_active == 'Active' ? 'Not Active' : 'Active';
            $user->save();    
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();
            LogError::insertLogError($e->getMessage());

            $error = 'Gagal mengganti status aktivasi, tolong coba lagi!';

            return redirect()->route('user')->with('error', $error);
        }

        $success = 'Berhasil mengganti status aktivasi!';

        return redirect()->route('user')->with('success', $success);;
    }
}
