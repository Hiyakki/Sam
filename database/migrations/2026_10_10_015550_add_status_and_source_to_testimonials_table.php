<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('rating');
            $table->string('source', 20)->default('public')->after('status');
        });

        // Update existing data
        DB::table('testimonials')->update([
            'status' => DB::raw("CASE WHEN is_active = 1 AND consent_confirmed = 1 THEN 'approved' ELSE 'pending' END"),
            'source' => 'admin', // assume old data was inserted by admin
        ]);
    }

    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['status', 'source']);
        });
    }
};
