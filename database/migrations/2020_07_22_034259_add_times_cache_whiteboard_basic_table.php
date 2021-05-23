<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimesCacheWhiteboardBasicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cache_whiteboard_basic', function (Blueprint $table) {
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });

        DB::connection()->getpdo()->exec("ALTER TABLE `cache_whiteboard_basic` CHANGE COLUMN `stamp_updated` `stamp_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()");
        DB::connection()->getpdo()->exec("UPDATE `cache_whiteboard_basic` SET  created_at = `stamp_created`");
        DB::connection()->getpdo()->exec("UPDATE `cache_whiteboard_basic` SET  updated_at = `stamp_updated`");

        Schema::table('cache_whiteboard_basic', function (Blueprint $table) {
            $table->dropColumn(['stamp_created', 'stamp_updated']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cache_whiteboard_basic', function (Blueprint $table) {
            //
        });
    }
}
