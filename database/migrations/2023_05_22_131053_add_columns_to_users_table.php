<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('shopify_id')->after('id')->nullable();
            $table->after('email_verified_at', function ($table){
                $table->string('owner_name')->nullable();
                $table->string('shop_owner')->nullable();
                $table->string('owner_email')->nullable();
                $table->string('phone')->nullable();
                $table->string('country')->nullable();
                $table->string('currency')->nullable();
                $table->string('language')->nullable();
                $table->string('shopify_plan_name')->nullable();
                $table->boolean('page_views_limit_crossed')->default(false)->nullable();
                $table->json('segment_events')->nullable();
                $table->string('main_theme_id')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['shopify_id', 'shop_owner','owner_name','owner_email', 'phone', 'country', 'currency', 'language', 'page_views_limit_crossed','shopify_plan_name','segment_events','main_theme_id']);
        });
    }
};
