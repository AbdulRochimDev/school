<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('report_cards')) {
            Schema::create('report_cards', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('term_id')->nullable();
                $table->decimal('final_score', 8, 2)->nullable();
                $table->dateTime('published_at')->nullable();
                $table->timestamps();
                $table->unique(['student_id','term_id']);
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
                $table->foreign('term_id')->references('id')->on('terms')->onDelete('set null');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('report_cards'); }
};
