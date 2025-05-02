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
        Schema::create('lecturer', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()
                                        ->constrained('user')
                                        ->onDelete('cascade');
            $table->string('name');
            $table->string('staff_id')->unique();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('research_group')->nullable();
            $table->integer('max_students')->default(5); // Maximum number of students
            $table->integer('current_students')->default(0); // Current number of supervised students
            $table->boolean('accepting_students')->default(true); // Whether accepting new students
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturer');
    }
};
