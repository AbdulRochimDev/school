<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('attendance_records')) {
            Schema::create('attendance_records', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('attendance_session_id');
                $table->unsignedBigInteger('student_id');
                $table->enum('status', ['present','late','excused','absent'])->default('absent');
                $table->dateTime('checkin_at')->nullable();
                $table->string('note', 255)->nullable();
                $table->timestamps();
                $table->unique(['attendance_session_id','student_id']);
                $table->index('student_id');
                $table->foreign('attendance_session_id')->references('id')->on('attendance_sessions')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('attendance_records'); }
};
