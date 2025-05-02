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
        Schema::create('student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()  // Changed from student_id to user_id
                ->constrained('user')
                ->onDelete('cascade');
            $table->string('name');
            $table->string('matric_id')->unique();
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('program');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student');
    }
};
