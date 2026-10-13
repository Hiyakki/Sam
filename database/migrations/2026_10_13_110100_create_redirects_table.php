<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('source_path', 500)->unique();
            $table->string('target_path', 500);
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->timestamps();

            $table->index('source_path');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
