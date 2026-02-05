<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('struks', function (Blueprint $table) {
        $table->text('snap_token')->nullable();
        $table->timestamp('snap_token_created_at')->nullable();
    });
}

public function down()
{
    Schema::table('struks', function (Blueprint $table) {
        $table->dropColumn(['snap_token', 'snap_token_created_at']);
    });
}
};
