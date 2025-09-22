<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('class_meetings')) {
            Schema::create('class_meetings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('class_subject_id');
                $table->unsignedTinyInteger('weekday');
                $table->time('start_time');
                $table->time('end_time');
                $table->string('room', 50)->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }
    }
    public function down(): void { Schema::dropIfExists('class_meetings'); }
};

