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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $table->string('title', 150);
            $table->text('description');
            $table->string('level', 20)->default('beginner');
            $table->string('banner_path')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'subject_id']);
            $table->index('instructor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};

