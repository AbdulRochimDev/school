<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action', 150);
                $table->string('subject_type', 150)->nullable();
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->json('properties')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->index('user_id');
                $table->index(['subject_type','subject_id']);
            });
        }
    }
    public function down(): void { Schema::dropIfExists('activity_logs'); }
};
