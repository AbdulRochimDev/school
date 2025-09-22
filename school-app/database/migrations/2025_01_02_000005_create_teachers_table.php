<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('teachers')) {
            Schema::create('teachers', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('nip')->nullable();
                $table->string('name');
                $table->timestamps();
                $table->unique('user_id');
                $table->index('nip');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('teachers'); }
};
