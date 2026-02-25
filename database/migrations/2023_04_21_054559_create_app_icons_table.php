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
        Schema::create('app_icons', static function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('category')->nullable();
            $table->string('name')->nullable();
            $table->text('url')->nullable();

            $table->unique(['type', 'category', 'name']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_icons');
    }
};
