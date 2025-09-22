<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->string('key', 150)->primary();
                $table->text('value')->nullable();
            });
        }
    }
    public function down(): void { Schema::dropIfExists('settings'); }
};
