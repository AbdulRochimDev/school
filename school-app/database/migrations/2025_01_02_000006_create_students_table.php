<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('class_id')->nullable();
                $table->string('nis')->nullable();
                $table->string('nisn')->nullable();
                $table->string('name');
                $table->timestamps();
                $table->unique('user_id');
                $table->index(['class_id']);
                $table->index(['nis','nisn']);
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('class_id')->references('id')->on('classes')->onDelete('set null');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('students'); }
};
