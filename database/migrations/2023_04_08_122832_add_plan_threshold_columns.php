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
            $table->after('test', static function ($table) {
                $table->integer('max_block_limit')->nullable();
                $table->integer('icon_per_block_limit')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->dropColumn(['max_block_limit', 'icon_per_block_limit']);
        });
    }
};
