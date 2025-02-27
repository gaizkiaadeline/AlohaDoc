<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'jenis_kelamin',
        'role',
        'telephone',
        'password',
        'is_active',
        'specialist_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function hasRole($role){
        if($this->role == $role){
            return true;
        }

        return false;
    }

    public function doctorSchedules(){
        return $this->hasMany(DoctorSchedule::class, 'doctor_id', 'id');
    }

    public function consultations(){
        return $this->hasMany(Consultation::class, 'patient_id', 'id');
    }

    public function chats(){
        return $this->hasMany(Chat::class, 'user_id', 'id');
    }

    public function specialist(){
        return $this->belongsTo(Doctor::class, 'specialist_id', 'id');
    }
}
