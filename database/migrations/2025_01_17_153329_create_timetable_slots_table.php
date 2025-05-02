<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('timetable_slots', function (Blueprint $table) {
            $table->id();
            // Foreign keys
            $table->unsignedBigInteger('lecturer_id');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('timeframe_id');
            
            // Slot details
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['available', 'booked', 'unavailable'])->default('available');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('lecturer_id')
                ->references('id')
                ->on('lecturer')
                ->onDelete('cascade');
            
            $table->foreign('student_id')
                ->references('id')
                ->on('student')
                ->onDelete('set null');
                
            $table->foreign('timeframe_id')
                ->references('id')
                ->on('timeframe')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_slots');
    }
};
