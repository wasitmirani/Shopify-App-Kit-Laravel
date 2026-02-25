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
        Schema::table('blocks', static function (Blueprint $table) {
            $table->after('icons_per_row_mobile', static function ($table) {
                $table->integer('size')->nullable();
                $table->json('color_settings')->nullable()->comment('Fields: block_background_color, icon_color, title_color, subtitle_color, is_transparent');
                $table->json('typography_settings')->nullable()->comment('Field: title_font_size, subtitle_font_size, title_font_style, subtitle_font_style');
                $table->integer('block_size')->nullable();
                $table->integer('goes_up')->nullable();
                $table->integer('goes_down')->nullable();
                $table->integer('space_between_blocks')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blocks', static function (Blueprint $table) {
            $table->dropColumn(['size', 'color_settings', 'typography_settings', 'block_size', 'goes_up', 'goes_down', 'space_between_blocks']);
        });
    }
};
