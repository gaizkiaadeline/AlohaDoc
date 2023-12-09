<?php

namespace Database\Seeders;

use App\Models\Day;
use App\Models\Schedule;
use App\Models\Session;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Session::create([
            'name' => 'Sesi 1 Pagi',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
        ]);

        Session::create([
            'name' => 'Sesi 2 Pagi',
            'start_time' => '10:30:00',
            'end_time' => '11:30:00',
        ]);

        Session::create([
            'name' => 'Sesi 1 Siang',
            'start_time' => '13:00:00',
            'end_time' => '14:00:00',
        ]);

        Session::create([
            'name' => 'Sesi 2 Siang',
            'start_time' => '14:30:00',
            'end_time' => '15:30:00',
        ]);

        Session::create([
            'name' => 'Sesi 1 Sore',
            'start_time' => '16:00:00',
            'end_time' => '17:30:00',
        ]);

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        foreach($days as $day){
            Day::create([
                'name' => $day
            ]);
        }

        $sessions = Session::all();
        $days = Day::all();

        foreach($sessions as $session){
            foreach($days as $day){
                Schedule::create([
                    'day_id' => $day->id,
                    'session_id' => $session->id
                ]);
            }
        }
    }
}
