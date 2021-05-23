<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimesDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });

        DB::connection()->getpdo()->exec("ALTER TABLE `deals` CHANGE COLUMN `stamp_updated` `stamp_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()");
		DB::connection()->getpdo()->exec("UPDATE `deals` SET  created_at = stamp_created");
        DB::connection()->getpdo()->exec("UPDATE `deals` SET  updated_at = stamp_updated");

        Schema::table('deals', function (Blueprint $table) {
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
        Schema::table('deals', function (Blueprint $table) {
            //
        });
    }
}
