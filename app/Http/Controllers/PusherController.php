<?php

namespace App\Http\Controllers;

use App\Events\PusherBroadcast;
use App\Interfaces\StatusInterface;
use App\Models\Chat;
use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Schedule;
use App\Models\Session;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Broadcasting\Broadcasters\PusherBroadcaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;

class PusherController extends Controller implements StatusInterface
{
    public function index(Consultation $consultationId) 
    {
        if(Carbon::now() > $consultationId->doctor_schedule->schedule->session->end_time){
            $consultationId->status = self::STATUS_MENUNGGU_RESEP;
        }
        else{
            $consultationId->status = self::STATUS_KONSULTASI;
            $consultationId->update();
        }

        $messages = Chat::where('consultation_id', $consultationId->id)->get();

        $consultation = Consultation::findOrFail($consultationId->id);
        $doctorSchedule = DoctorSchedule::findOrFail($consultation->doctor_schedule_id);
        $schedule = Schedule::findOrFail($doctorSchedule->schedule_id);
        $session = Session::findOrFail($schedule->session_id);
        $doctor = User::findOrFail($doctorSchedule->doctor_id);
        $patient = User::findOrFail($consultation->patient_id);
        $specialist = Doctor::findOrFail($doctor->specialist_id);

        if (count($messages) == 0) {
            $firstPatientChat = Chat::create([
                'consultation_id' => $consultationId->id,
                'user_id' => $patient->id,
                'content' => 'Halo, saya ingin berkonsultasi.'
            ]);

            $firstDoctorChat = Chat::create([
                'consultation_id' => $consultationId->id,
                'user_id' => $doctor->id,
                'content' => 'Halo, selamat datang di Alohadoc. Saya dokter ' . $doctor->name . '.'
            ]);

            $messages = Chat::where('consultation_id', $consultationId->id)->get();
        }

        return view('chat', [
            'messages' => $messages,
            'consultation' => $consultation, 
            'doctor' => $doctor,
            'specialist' => $specialist,
            'patient' => $patient,
            'session' => $session,
            'schedule' => $schedule,
        ]);
    }

    public function broadcast(Request $request, $consultationId)
    {
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER')
            ]
        );

        $chat = Chat::create([
            'consultation_id' => $consultationId,
            'user_id' => Auth::user()->id,
            'content' => $request->get('message')
        ]);

        $broadcaster = new PusherBroadcaster($pusher);
        broadcast(new PusherBroadcast($consultationId, $request->get('message')))->toOthers();
        return view('broadcast', ['message' => $request->get('message')]);
    }

    public function receive(Request $request)
    {
        return view('receive', ['message' => $request->get('message')]);
    }
}
