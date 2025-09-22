<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('payment_verifications')) {
            Schema::create('payment_verifications', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('payment_id');
                $table->unsignedBigInteger('verified_by')->nullable();
                $table->dateTime('verified_at')->nullable();
                $table->string('status', 50)->default('pending');
                $table->string('note', 255)->nullable();
                $table->timestamps();
                $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
                $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('payment_verifications'); }
};
