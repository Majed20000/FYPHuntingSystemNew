<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('timeframe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coordinator_id')->constrained('coordinator')->onDelete('cascade');
            $table->string('semester');
            $table->string('academic_year');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->integer('max_applications_per_student')->default(1);
            $table->integer('max_appointments_per_student')->default(3);
            $table->dateTime('proposal_submission_deadline');
            $table->dateTime('supervisor_confirmation_deadline');
            $table->string('status')->default('draft');
            $table->timestamps();

            // Add indexes for frequently queried columns
            $table->index('is_active');
            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('timeframe');
    }
}; 