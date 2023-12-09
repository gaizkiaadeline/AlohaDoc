<?php

namespace App\Http\Controllers;

use App\Models\DoctorSchedule;
use App\Models\LogError;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index(Request $request){
        if($request->ajax()){
            $schedules = Schedule::get()
                    ->transform(function ($dt){
                        $theSchedule = DoctorSchedule::where('doctor_id', Auth::user()->id)
                                                ->where('schedule_id', $dt->id)
                                                ->first();
                        return collect([
                            'id' => $dt->id,
                            'day' => $dt->day->name,
                            'session' => $dt->session->name . ' (' . $dt->session->start_time . ' - ' . $dt->session->end_time . ')',
                            'status' => $theSchedule && $theSchedule->isActive == 1 ? 'Terjadwal' : 'Tidak Aktif',
                            'active' => $theSchedule && $theSchedule->isActive == 1
                        ]);
                    });

            return DataTables::of($schedules)
                ->addColumn('actions', function ($p) {
                    $returnedValue = [];

                    array_push($returnedValue, [
                        "route" => route('schedule.activate', ['schedule' => $p['id'], 'user' => Auth::user()->id, 'setActive' => $p['active'] ? 'Matikan' : 'Aktifkan']),
                        "attr_id" => $p['id'],
                        "icon" => 'fas fa-fw fa-user-tag',
                        "label" => $p['active'] ? 'Nonaktif' : 'Aktifkan',
                        "btnStyle" => !$p['active'] ? 'success' : 'danger',
                        "btnClass" => 'Activate'
                    ]);

                    return $returnedValue;
                })
                ->make();
        }

        $success = session('success') ?? null;
        $error = session('error') ?? null;
        $route = 'schedule';

        return view('schedule.index', compact('route', 'success', 'error'));
    }

    public function activateSchedule(Request $request, Schedule $schedule, User $user, $setActive){
        DB::beginTransaction();
        try{
            if($setActive == 'Aktifkan'){
                $theSchedule = DoctorSchedule::where('schedule_id', $schedule->id)
                                            ->where('doctor_id', $user->id)
                                            ->first();

                if($theSchedule){
                    $theSchedule->isActive = 1;
                    $theSchedule->save();
                }
                else{
                    DoctorSchedule::create([
                        'schedule_id' => $schedule->id,
                        'doctor_id' => $user->id,
                        'isActive' => 1
                    ]);
                }
            }
            else{
                DoctorSchedule::where('schedule_id', $schedule->id)
                                ->where('doctor_id', $user->id)
                                ->update([
                                    'isActive' => 0
                                ]);
            }

            DB::commit();
        } catch(\Exception $err){
            DB::rollBack();
            LogError::insertLogError($err->getMessage());
            return redirect()->route('schedule')->with('error', 'Gagal mengganti status, mohon coba lagi!');
        }

        if($setActive == 'Aktifkan'){
            $success = 'Akun berhasil mengaktifkan jadwal!';
        }
        else{
            $success = 'Akun berhasil menonaktifkan jadwal!';
        }

        return redirect()->route('schedule')->with('success', $success);
    }
}
