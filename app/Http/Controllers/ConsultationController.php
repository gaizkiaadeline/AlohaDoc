<?php

namespace App\Http\Controllers;

use App\Interfaces\StatusInterface;
use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\LogError;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ConsultationController extends Controller implements StatusInterface
{
    public function index(Request $request) {
        if($request->ajax()){
            $consultations = Consultation::selectRaw('consultations.*')
                        ->join('doctor_schedules', 'doctor_schedules.id', '=', 'consultations.doctor_schedule_id')
                        ->where(function ($query){
                            if(Auth::user()->role == 'patient'){
                                $query->where('patient_id', Auth::user()->id);
                            }
                            else if(Auth::user()->role == 'doctor'){
                                $query->where('doctor_schedules.doctor_id', Auth::user()->id);
                            }
                        })
                        ->orderBy('created_at', 'DESC')
                        ->get()
                        ->transform(function ($dt){
                            $doctorSchedule = DoctorSchedule::find($dt->doctor_schedule_id);

                            $dt->recipe_avail = $dt->recipe ? 'Available' : 'Not Available';
                            
                            if(in_array(Auth::user()->role, ['patient', 'admin'])){
                                $dt->name = $doctorSchedule->doctor->name;
                            }
                            else if(Auth::user()->role == 'doctor'){
                                $dt->name = $dt->user->name;
                            }

                            $dt->specialist_id = $doctorSchedule->doctor->specialist->specialist;
                            $dt->consultation_start = $dt->doctor_schedule->schedule->session->start_time;
                            $dt->consultation_end = $dt->doctor_schedule->schedule->session->end_time;
                            $dt->session = $dt->doctor_schedule->schedule->session->name . ' (' . $dt->doctor_schedule->schedule->session->start_time . ' - ' . $dt->doctor_schedule->schedule->session->end_time . ')';

                            $dt->status = $this->getStatusText($dt->status);

                            return $dt;
                        });
            
            $currentDate = Carbon::today('Asia/Jakarta');
            $currentDateTime = Carbon::now('Asia/Jakarta');

            $newConsultations = [];

            foreach($consultations as $consultation){
                // Change Status menjadi batal/ditolak jika masih request/booked tapi sudah melewati masa
                if($currentDate->diffInDays($consultation['consultation_date']) == 0){
                    if($currentDateTime->addHours(3) > $consultation['consultation_start'] && $consultation['status'] == self::STATUS_REQUEST_TEXT){
                        try{
                            Consultation::find($consultation['id'])
                                        ->update([
                                            'status' => self::STATUS_DITOLAK
                                        ]);
                                        
                            $consultation['status'] = self::STATUS_DITOLAK_TEXT;

                            DB::commit();
                        } catch(\Exception $err){
                            DB::rollBack();
                            LogError::insertLogError($err->getMessage());
                        }
                    }

                    if($currentDateTime > $consultation['consultation_end'] && $consultation['status'] == self::STATUS_BOOKED_TEXT){
                        try{
                            Consultation::find($consultation['id'])
                                        ->update([
                                            'status' => self::STATUS_CANCEL
                                        ]);
                            
                            $p['status'] = self::STATUS_CANCEL_TEXT;

                            DB::commit();
                        } catch(\Exception $err){
                            DB::rollBack();
                            LogError::insertLogError($err->getMessage());
                        }
                    }
                }

                $newConsultations[] = $consultation;
            }

            return DataTables::of($newConsultations)
                ->editColumn('consultation_date', function ($p) {
                    return [
                        'display' => date('d F Y', strtotime($p->consultation_date)),
                        'timestamp' => date('Y F d', strtotime($p->consultation_date))
                    ];
                })
                ->addColumn('actions', function ($p) use ($currentDate, $currentDateTime){
                    $returnedValue = [];

                    if(Auth::user()->role == 'patient'){
                        if($p['status'] == self::STATUS_REQUEST_TEXT && ($currentDate->diffInDays($p['consultation_date']) > 0 || ($currentDate->diffInDays($p['consultation_date']) == 0 && $currentDateTime->addHours(6) <= $p['consultation_start']))){
                            array_push($returnedValue, [
                                "route" => route('consultation.cancel', ['consultation' => $p['id']]),
                                "attr_id" => $p['id'],
                                "icon" => 'fas fa-fw fa-trash',
                                "label" => 'Cancel',
                                "btnStyle" => 'danger',
                                "btnClass" => 'Cancel'
                            ]);
                        }
                    }
                    
                    if(in_array(Auth::user()->role, ['patient', 'doctor']) && $currentDate->diffInDays($p['consultation_date']) <= 0 && $currentDateTime >= $p['consultation_start'] && in_array($p['status'], [self::STATUS_BOOKED_TEXT, self::STATUS_KONSULTASI_TEXT])){
                        array_push($returnedValue, [
                            "route" => route('consultation.do', ['consultation' => $p['id']]),
                            "attr_id" => $p['id'],
                            "icon" => 'fas fa-fw fa-stethoscope',
                            "label" => 'Konsultasi',
                            "btnStyle" => 'success',
                            "btnClass" => 'Consultation'
                        ]);
                    }

                    if(Auth::user()->role == 'admin' && $p['status'] == self::STATUS_REQUEST_TEXT){
                        array_push($returnedValue, [
                            "route" => route('consultation.activate', ['consultation' => $p['id'], 'setActive' => 'Terima']),
                            "attr_id" => $p['id'],
                            "icon" => 'fas fa-fw fa-check',
                            "label" => 'Terima Request',
                            "btnStyle" => 'success',
                            "btnClass" => 'Activate'
                        ]);

                        array_push($returnedValue, [
                            "route" => route('consultation.activate', ['consultation' => $p['id'], 'setActive' => 'Tolak']),
                            "attr_id" => $p['id'],
                            "icon" => 'fas fa-fw fa-xmark',
                            "label" => 'Tolak Request',
                            "btnStyle" => 'danger',
                            "btnClass" => 'Activate'
                        ]);
                    }

                    return $returnedValue;
                })
                ->make();
        }

        $success = session('success') ?? null;
        $error = session('error') ?? null;
        $route = 'consultation';

        $specialists = Doctor::all();

        return view('consultation.index', compact('route', 'success', 'error', 'specialists'));
    }

    public function store(Request $request) {
        $specialistId = $request->specialist;
        $date = $request->date;
        $sessionId = $request->session;

        $nameOfDay = Carbon::parse($request->date)->format('l');

        // Localize Indonesia
        $nameOfDay = $this->localizeNameOfDay($nameOfDay);

        DB::beginTransaction();

        try{
            // Ambil seluruh dokter yang memiliki spesialis id inputan user
            $doctorsSpecialistIds = User::select('id')
                                        ->where('specialist_id', $specialistId)
                                        ->get()
                                        ->pluck('id')
                                        ->values()
                                        ->toArray();

            // Ambil seluruh id doctor_schedule di mana sudah ada konsultasi terbooked pada hari inputan user
            $bookedDoctorScheduleIds = Consultation::select('doctor_schedule_id')
                                                    ->where('consultation_date', $date)
                                                    ->where('status', 'Booked')
                                                    ->get()
                                                    ->pluck('doctor_schedule_id')
                                                    ->values()
                                                    ->toArray();

            // Ambil seluruh doctor schedule yang sudah user request
            $alreadyRequestedDoctorScheduleIds = Consultation::select('doctor_schedule_id')
                                                                ->where('consultation_date', $date)
                                                                ->where('patient_id', Auth::user()->id)
                                                                ->where('status', '!=', self::STATUS_CANCEL)
                                                                ->get()
                                                                ->pluck('doctor_schedule_id')
                                                                ->values()
                                                                ->toArray();

            // Ambil doctor schedule yang tersedia secara random
            $doctorSchedules = DoctorSchedule::select('doctor_schedules.id')
                                            ->join('schedules', 'schedules.id', '=', 'doctor_schedules.schedule_id')
                                            ->join('days', 'days.id', '=', 'schedules.day_id')
                                            ->join('sessions', 'sessions.id', '=', 'schedules.session_id')
                                            ->whereNotIn('doctor_schedules.id', array_merge($bookedDoctorScheduleIds, $alreadyRequestedDoctorScheduleIds))
                                            ->whereIn('doctor_id', $doctorsSpecialistIds)
                                            ->where('days.name', $nameOfDay)
                                            ->where('schedules.session_id', $sessionId)
                                            ->get();

            if(count($doctorSchedules) > 0){
                $doctorSchedules = $doctorSchedules->random();

                Consultation::create([
                    'patient_id' => Auth::user()->id,
                    'doctor_schedule_id' => $doctorSchedules->id,
                    'status' => self::STATUS_REQUEST,
                    'recipe' => null,
                    'consultation_date' => $date
                ]);
            }
            else{
                DB::rollBack();
                return redirect()->route('consultation')->with('error', 'Tanggal dan sesi sudah pernah anda pilih atau telah terisi penuh, mohon pilih sesi atau tanggal lain!');
            }

            DB::commit();
        } catch(\Exception $err){
            DB::rollBack();
            LogError::insertLogError($err->getMessage() . '| Line: ' . $err->getTraceAsString());
            return redirect()->route('consultation')->with('error', 'Terjadi kesalahan saat membuat request konsultasi baru, mohon coba lagi!');
        }

        return redirect()->route('consultation')->with('success', 'Request konsultasi baru berhasil dibuat! Mohon tunggu konfirmasi dari admin!');
    }

    public function cancel(Consultation $consultation) {
        try{
            $consultation->status = self::STATUS_CANCEL;
            $consultation->update();

            DB::commit();
        } catch(\Exception $err){
            DB::rollBack();
            LogError::insertLogError($err->getMessage());
            return redirect()->route('consultation')->with('error', 'Terjadi kesalahan saat membatalkan request konsultasi baru, mohon coba lagi!');
        }

        return redirect()->route('consultation')->with('success', 'Request konsultasi berhasil dibatalkan!');
    }

    public function do(Consultation $consultation) {
        if(Carbon::now() > $consultation->doctor_schedule->schedule->session->end_time){
            $consultation->status = self::STATUS_MENUNGGU_RESEP;
        }
        else{
            $consultation->status = self::STATUS_KONSULTASI;
        }
        
        $consultation->update();

        return view('consultation.chat', compact('consultation'));
    }

    public function activateRequest(Request $request, Consultation $consultation, $setActive){
        DB::beginTransaction();
        try{
            if($setActive == 'Terima'){
                $consultation->status = self::STATUS_BOOKED;
            }
            else{
                $consultation->status = self::STATUS_DITOLAK;
            }

            $consultation->save();

            DB::commit();
        } catch(\Exception $err){
            DB::rollBack();
            LogError::insertLogError($err->getMessage());
            return redirect()->route('consultation')->with('error', 'Gagal mengganti status, mohon coba lagi!');
        }

        if($setActive == 'Terima'){
            $success = 'Request konsultasi berhasil diterima!';
        }
        else{
            $success = 'Request konsultasi berhasil ditolak!';
        }

        return redirect()->route('consultation')->with('success', $success);
    }

    public function getSession(Request $request) {
        $specialistId = $request->specialist;
        $chosenDate = $request->date;
        $nameOfDay = Carbon::parse($request->date)->format('l');

        // Localize Indonesia
        $nameOfDay = $this->localizeNameOfDay($nameOfDay);

        // Ambil seluruh id doctor_schedule di mana sudah ada konsultasi terbooked pada hari inputan user
        $bookedDoctorScheduleIds = Consultation::select('doctor_schedule_id')
                                                ->where('consultation_date', $chosenDate)
                                                ->where('status', 'Booked')
                                                ->get()
                                                ->pluck('doctor_schedule_id')
                                                ->values()
                                                ->toArray();

        $sessions = User::selectRaw('sessions.*')
                        ->join('doctor_schedules', 'doctor_schedules.doctor_id', '=', 'users.id')
                        ->join('schedules', 'schedules.id', '=', 'doctor_schedules.schedule_id')
                        ->join('days', 'days.id', '=', 'schedules.day_id')
                        ->join('sessions', 'sessions.id', '=', 'schedules.session_id')
                        ->where('specialist_id', $specialistId)
                        ->where('days.name', $nameOfDay)
                        ->where(function ($query) use($chosenDate){
                            $currentDateTime = Carbon::now();

                            if($currentDateTime->isSameDay(Carbon::parse($chosenDate))){
                                $query->where('sessions.start_time', '>=', $currentDateTime->addHours(6));
                            }
                        })
                        ->whereNotIn('doctor_schedules.id', $bookedDoctorScheduleIds)
                        ->get()
                        ->transform(function ($dt){
                            return [
                                'id' => $dt->id,
                                'text' => $dt->name . ' (' . $dt->start_time . ' - ' . $dt->end_time . ')'
                            ];
                        })->toArray();

        return response()->json([
            "results" => $sessions
        ]);
    }

    public function getStatusText($statusId){
        switch($statusId){
            case '1':
                return self::STATUS_REQUEST_TEXT;
                break;
            case '2':
                return self::STATUS_BOOKED_TEXT;
                break;
            case '3':
                return self::STATUS_CANCEL_TEXT;
                break;
            case '4':
                return self::STATUS_DITOLAK_TEXT;
                break;
            case '5':
                return self::STATUS_KONSULTASI_TEXT;
                break;
            case '6':
                return self::STATUS_MENUNGGU_RESEP_TEXT;
                break;
            case '7':
                return self::STATUS_SELESAI_TEXT;
                break;
            default:
                return 'Tidak Diketahui';
        }
    }

    public function localizeNameOfDay($nameOfDay){
        switch ($nameOfDay){
            case 'Monday': {
                return 'Senin';
                break;
            }
            case 'Tuesday': {
                return 'Selasa';
                break;
            }
            case 'Wednesday': {
                return 'Rabu';
                break;
            }
            case 'Thursday': {
                return 'Kamis';
                break;
            }
            case 'Friday': {
                return 'Jumat';
                break;
            }
            case 'Saturday': {
                return 'Sabtu';
                break;
            }
            case 'Sunday': {
                return 'Minggu';
                break;
            }
            default: {
                return 'Not Found';
                break;
            }
        }
    }
}
