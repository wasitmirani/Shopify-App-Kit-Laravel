<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plans', static function (Blueprint $table) {
            $table->integer('page_views_threshold')->nullable()->after('test');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', static function (Blueprint $table) {
            $table->dropColumn('page_views_threshold');
        });
    }
};
