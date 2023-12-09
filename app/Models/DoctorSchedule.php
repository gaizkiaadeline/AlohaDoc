<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $table = 'doctor_schedules';

    protected $fillable = [
        'doctor_id',
        'schedule_id',
        'isActive'
    ];

    public function doctor(){
        return $this->belongsTo(User::class, 'doctor_id', 'id');
    }

    public function schedule(){
        return $this->belongsTo(Schedule::class, 'schedule_id', 'id');
    }

    public function consultations(){
        return $this->hasMany(Consultation::class, 'doctor_schedule_id', 'id');
    }
}
