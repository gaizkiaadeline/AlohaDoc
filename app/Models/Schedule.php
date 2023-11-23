<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';

    protected $fillable = [
        'day_id',
        'session_id',
    ];

    public function day(){
        return $this->belongsTo(Day::class, 'day_id', 'id');
    }

    public function session(){
        return $this->belongsTo(Session::class, 'session_id', 'id');
    }

    public function doctorSchedules(){
        return $this->hasMany(DoctorSchedule::class, 'schedule_id', 'id');
    }
}
