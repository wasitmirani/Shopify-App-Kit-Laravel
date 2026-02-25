<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Osiset\ShopifyApp\Util;

class AddPasswordUpdatedAtToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table(Util::getShopsTable(), static function (Blueprint $table) {
            $table->date('password_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table(Util::getShopsTable(), static function (Blueprint $table) {
            $table->dropColumn('password_updated_at');
        });
    }
}
