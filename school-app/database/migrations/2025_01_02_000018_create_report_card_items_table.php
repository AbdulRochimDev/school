<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('report_card_items')) {
            Schema::create('report_card_items', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
                $table->id();
                $table->unsignedBigInteger('report_card_id');
                $table->unsignedBigInteger('grade_item_id');
                $table->decimal('score', 8, 2)->nullable();
                $table->decimal('weight', 5, 2)->nullable();
                $table->timestamps();
                $table->unique(['report_card_id','grade_item_id']);
                $table->foreign('report_card_id')->references('id')->on('report_cards')->onDelete('cascade');
                $table->foreign('grade_item_id')->references('id')->on('grade_items')->onDelete('cascade');
            });
        }
    }
    public function down(): void { Schema::dropIfExists('report_card_items'); }
};
