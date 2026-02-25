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
        Schema::create('icons', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_id');
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('link')->nullable();
            $table->boolean('open_to_new_tab')->default(0);
            $table->string('tags')->nullable();
            $table->integer('position')->nullable();
            $table->integer('size')->nullable();
            $table->json('color_settings')->nullable()->comment('Fields: block_background_color, icon_color, title_color, subtitle_color, is_transparent');
            $table->json('typography_settings')->nullable()->comment('Field: title_font_size, subtitle_font_size, title_font_style, subtitle_font_style');
            $table->integer('block_size')->nullable();
            $table->integer('goes_up')->nullable();
            $table->integer('goes_down')->nullable();
            $table->integer('space_between_blocks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('icons');
    }
};
