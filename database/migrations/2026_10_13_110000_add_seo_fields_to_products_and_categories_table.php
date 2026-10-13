<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('meta_title', 255)->nullable()->after('indication');
            $table->text('meta_description')->nullable()->after('meta_title');
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->text('intro_text')->nullable()->after('slug');
            $table->string('meta_title', 255)->nullable()->after('intro_text');
            $table->text('meta_description')->nullable()->after('meta_title');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description']);
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropColumn(['intro_text', 'meta_title', 'meta_description']);
        });
    }
};
