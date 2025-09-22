<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('submissions')) {
            Schema::create('submissions', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('assignment_id');
                $table->unsignedBigInteger('student_id');
                $table->text('content')->nullable();
                $table->dateTime('submitted_at')->nullable();
                $table->decimal('score', 8, 2)->nullable();
                $table->text('feedback')->nullable();
                $table->timestamps();
                $table->unique(['assignment_id','student_id']);
                $table->index('student_id');
                $table->foreign('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('submissions'); }
};
