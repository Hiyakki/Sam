<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_closures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->date('date');
            $table->string('reason', 150)->nullable();
            $table->boolean('is_closed')->default(true);
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_closures');
    }
};
