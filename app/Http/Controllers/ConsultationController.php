<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ConsultationController extends Controller
{
    public function index(Request $request) {
        if($request->ajax()){
            $users = Consultation::orderBy('created_at', 'DESC')
                        ->get()
                        ->transform(function ($dt){
                            $dt->recipe_avail = $dt->recipe ? 'Available' : 'Not Available';

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
                    
                    if($p->role != 'patient'){
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

        return view('consultation.index', compact('route', 'success', 'error'));
    }
}
