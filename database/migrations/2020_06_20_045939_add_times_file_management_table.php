<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimesFileManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_management', function (Blueprint $table) {
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });

        DB::connection()->getpdo()->exec("UPDATE `file_management` SET  created_at = `date`");
        DB::connection()->getpdo()->exec("UPDATE `file_management` SET  updated_at = `date`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_management', function (Blueprint $table) {
            //
        });
    }
}
