<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->references('id')->on('users');
            $table->foreignId('doctor_schedule_id')->references('id')->on('doctor_schedules');
            $table->enum('status', ['Request Baru', 'Ditolak', 'Dibatalkan', 'Diterima', 'Proses', 'Selesai']);
            $table->text('recipe');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consultations');
    }
};
