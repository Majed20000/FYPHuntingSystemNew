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
        Schema::create('projectproposal', function (Blueprint $table) {
            $table->id();
            
            // Proposal Creator (can be lecturer or student)
            $table->foreignId('lecturer_id')
                ->nullable()
                ->constrained('lecturer')
                ->onDelete('cascade');
            $table->foreignId('student_id')
                ->nullable()
                ->constrained('student')
                ->onDelete('cascade');
            $table->foreignId('timeframe_id')
                ->constrained('timeframe')
                ->onDelete('cascade');
            
            // Proposal Details
            $table->string('title');
            $table->text('description');
            
            // Proposal Type and Status
            $table->enum('proposal_type', ['lecturer', 'student'])
                ->comment('lecturer: posted by lecturer, student: submitted by student');
            $table->enum('status', [
                'available',      // For lecturer proposals
                'unavailable',    // For lecturer proposals
                'pending',       // For student applications
                'approved',      // Application approved
                'rejected',      // Application rejected
            ])->default('available');
            
            // Application Details
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            
            // Constraints
            $table->unique(['student_id', 'lecturer_id', 'title'], 'unique_proposal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projectproposal');
    }
};
