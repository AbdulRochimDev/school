<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('grade_items')) {
            Schema::create('grade_items', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('class_subject_id');
                $table->string('name');
                $table->decimal('weight', 5, 2)->default(0);
                $table->integer('max_score')->default(100);
                $table->timestamps();
                $table->unique(['class_subject_id','name']);
                $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('grade_items'); }
};
