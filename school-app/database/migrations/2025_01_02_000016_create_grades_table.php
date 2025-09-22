<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('grades')) {
            Schema::create('grades', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('grade_item_id');
                $table->unsignedBigInteger('student_id');
                $table->decimal('score', 8, 2)->nullable();
                $table->dateTime('graded_at')->nullable();
                $table->timestamps();
                $table->unique(['grade_item_id','student_id']);
                $table->index('student_id');
                $table->foreign('grade_item_id')->references('id')->on('grade_items')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('grades'); }
};
