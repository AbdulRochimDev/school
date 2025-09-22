<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->string('number')->unique();
                $table->decimal('amount', 12, 2);
                $table->string('status', 50)->default('pending');
                $table->date('due_date')->nullable();
                $table->dateTime('issued_at')->nullable();
                $table->timestamps();
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('invoices'); }
};
