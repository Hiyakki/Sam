<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 12)->unique();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('patient_name', 120);
            $table->string('patient_email', 150);
            $table->string('patient_phone', 20);
            $table->unsignedTinyInteger('patient_age');
            $table->text('complaint');
            $table->string('prescription_path', 255)->nullable();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->enum('cancelled_by', ['patient', 'admin'])->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'appointment_date', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
