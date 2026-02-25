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
            $table->after('', static function ($table) {
                $table->boolean('upload_custom_icons')->default(false);
                $table->boolean('add_link')->default(false);
                $table->boolean('trigger_product_tag')->default(false);
                $table->boolean('3d_icon')->default(false);
                $table->boolean('live_chat')->default(false);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', static function (Blueprint $table) {
            $table->dropColumn(['upload_custom_icons', 'add_link', 'trigger_product_tag', '3d_icon', 'live_chat']);
        });
    }
};
