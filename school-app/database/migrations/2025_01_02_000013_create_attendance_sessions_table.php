<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('attendance_sessions')) {
            Schema::create('attendance_sessions', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('term_id')->nullable();
                $table->unsignedBigInteger('class_id')->nullable();
                $table->unsignedBigInteger('class_subject_id')->nullable();
                $table->unsignedBigInteger('teacher_id');
                $table->date('session_date');
                $table->dateTime('starts_at')->nullable();
                $table->dateTime('ends_at')->nullable();
                $table->enum('status', ['planned','open','closed','cancelled'])->default('planned');
                $table->string('topic', 191)->nullable();
                $table->timestamps();
                $table->index('session_date');
                $table->index('class_subject_id');
                $table->index('teacher_id');
                $table->foreign('term_id')->references('id')->on('terms')->onDelete('set null');
                $table->foreign('class_id')->references('id')->on('classes')->onDelete('set null');
                $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('set null');
                $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('attendance_sessions'); }
};
