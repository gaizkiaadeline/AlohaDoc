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
        
    // protected $fillable = [
    //     'id', 'user_agent', 'message', 'created_at', 'updated_at'
    // ];
        Schema::create('log_errors', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('user_agent');
            $table->text('message');
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
        Schema::dropIfExists('log_errors');
    }
};
