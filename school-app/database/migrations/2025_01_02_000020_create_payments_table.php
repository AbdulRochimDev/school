<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('invoice_id');
                $table->decimal('amount', 12, 2);
                $table->string('method', 50)->nullable();
                $table->dateTime('paid_at')->nullable();
                $table->string('reference', 100)->nullable();
                $table->timestamps();
                $table->index('invoice_id');
                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('payments'); }
};
