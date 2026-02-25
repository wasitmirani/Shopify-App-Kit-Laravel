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
        Schema::create('blocks', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_enabled')->default(true);
            $table->string('name')->nullable();
            $table->string('layout')->nullable()->comment('vertical OR horizontal');
            $table->json('header_text_settings')->nullable()->comment('Fields: font, size, weight, alignment, color');
            $table->string('position')->nullable();
            $table->integer('icons_per_row_desktop')->nullable();
            $table->integer('icons_per_row_mobile')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
