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
        Schema::create('timeframe', function (Blueprint $table) {
            $table->id();
            // Foreign key to coordinator who set this timeframe
            $table->foreignId('coordinator_id')
                ->constrained('coordinator')
                ->onDelete('cascade');
                
            // Timeframe Details
            $table->string('semester');
            $table->string('academic_year');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            
            // Additional Settings
            $table->integer('max_applications_per_student')->default(3)
                ->comment('Maximum number of applications a student can submit');

            $table->integer('max_appointments_per_student')->default(3)
                ->comment('Maximum number of appointments a student can book');
                        
            // Deadlines
            $table->date('proposal_submission_deadline')->nullable();
            $table->date('supervisor_confirmation_deadline')->nullable();
            
            // Status and Notes
            $table->enum('status', ['upcoming', 'active', 'completed', 'cancelled'])
                ->default('upcoming');
            $table->text('coordinator_notes')->nullable();
            
            $table->timestamps();
            
            // Ensure no overlapping timeframes
            $table->unique(['semester', 'academic_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timeframe');
    }
};
