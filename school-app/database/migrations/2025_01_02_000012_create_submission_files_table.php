<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('submission_files')) {
            Schema::create('submission_files', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('submission_id');
                $table->string('file_path', 255);
                $table->string('mime_type', 100)->nullable();
                $table->unsignedBigInteger('size_bytes')->nullable();
                $table->timestamps();
                $table->index('submission_id');
                $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('submission_files'); }
};
