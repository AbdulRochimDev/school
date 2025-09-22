<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('ledger_entries')) {
            Schema::create('ledger_entries', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('ledger_id');
                $table->date('entry_date');
                $table->string('type', 20);
                $table->decimal('amount', 12, 2);
                $table->string('reference', 100)->nullable();
                $table->string('note', 255)->nullable();
                $table->timestamps();
                $table->index('ledger_id');
                $table->unique('reference');
                $table->foreign('ledger_id')->references('id')->on('ledgers')->onDelete('cascade');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('ledger_entries'); }
};
