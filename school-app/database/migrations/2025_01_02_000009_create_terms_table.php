<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('terms')) {
            Schema::create('terms', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->string('name');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();
                $table->unique('name');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('terms'); }
};
