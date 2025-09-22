<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('classes')) {
            Schema::create('classes', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->string('name');
                $table->string('level', 50)->nullable();
                $table->unsignedBigInteger('homeroom_teacher_id')->nullable();
                $table->timestamps();
                $table->index('homeroom_teacher_id');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('classes'); }
};
