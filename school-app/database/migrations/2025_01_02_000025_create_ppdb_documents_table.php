<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('ppdb_documents')) {
            Schema::create('ppdb_documents', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('ppdb_application_id');
                $table->string('type', 100);
                $table->string('file_path', 255);
                $table->dateTime('verified_at')->nullable();
                $table->timestamps();
                $table->index('ppdb_application_id');
                $table->foreign('ppdb_application_id')->references('id')->on('ppdb_applications')->onDelete('cascade');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('ppdb_documents'); }
};
