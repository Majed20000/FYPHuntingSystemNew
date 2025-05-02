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
        Schema::create('supervise', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->unsignedBigInteger('lecturer_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('proposal_id');

            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('lecturer_id')
                ->references('id')
                ->on('lecturer')
                ->onDelete('cascade');
                
            $table->foreign('student_id')
                ->references('id')
                ->on('student')
                ->onDelete('cascade');
                
            $table->foreign('proposal_id')
                ->references('id')
                ->on('projectproposal')
                ->onDelete('cascade');
            
            // Ensure a student has only one active supervisor
            $table->unique(['student_id', 'lecturer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervise');
    }
};
