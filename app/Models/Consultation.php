<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $table = 'consultations';

    protected $fillable = [
        'patient_id',
        'doctor_schedule_id',
        'status',
        'recipe'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'patient_id', 'id');
    }

    public function doctor_schedule(){
        return $this->belongsTo(DoctorSchedule::class, 'doctor_schedule_id', 'id');
    }

    public function chats(){
        return $this->hasMany(Chat::class, 'consultation_id', 'id');
    }
}
