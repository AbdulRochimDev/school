<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('enrollments')) {
            Schema::create('enrollments', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('class_id');
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('term_id')->nullable();
                $table->timestamp('enrolled_at')->nullable();
                $table->timestamps();
                $table->unique(['class_id','student_id','term_id']);
                $table->index('term_id');
                $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
                $table->foreign('term_id')->references('id')->on('terms')->onDelete('set null');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('enrollments'); }
};
