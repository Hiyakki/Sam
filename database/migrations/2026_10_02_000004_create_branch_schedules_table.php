<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 1 = Senin, 7 = Minggu
            $table->boolean('is_open')->default(true);
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->unsignedSmallInteger('slot_duration_minutes')->default(60);
            $table->unsignedSmallInteger('capacity_per_slot')->default(1);
            $table->timestamps();

            $table->unique(['branch_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_schedules');
    }
};
