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
        Schema::create('appointment', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('lecturer_id')
                ->references('user_id')
                ->on('lecturer')
                ->onDelete('cascade');
            $table->foreignId('student_id')
                ->nullable()
                ->references('user_id')
                ->on('student')
                ->onDelete('cascade');
            $table->foreignId('timeslot_id')
                ->nullable()
                ->constrained('timetable_slots')
                ->onDelete('cascade');
            $table->foreignId('timeframe_id')
                ->nullable()
                ->constrained('timeframe')
                ->onDelete('cascade');
                
            // Appointment details
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('meeting_link')->nullable();
            $table->enum('meeting_type', ['online', 'physical'])->nullable();
            
            // Status tracking
            $table->string('status', 20)->default('available');
            $table->text('rejection_reason')->nullable();
            
            // Meeting details
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment');
    }
};
