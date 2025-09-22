<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('assignments')) {
            Schema::create('assignments', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('class_subject_id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->dateTime('due_at')->nullable();
                $table->integer('max_score')->nullable();
                $table->timestamps();
                $table->index('class_subject_id');
                $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('assignments'); }
};
