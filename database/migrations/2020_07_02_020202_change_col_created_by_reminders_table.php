<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColCreatedByRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getpdo()->exec(" UPDATE reminders AS B JOIN (SELECT A.created_by AS username, users.id AS userid FROM (SELECT created_by FROM reminders GROUP BY created_by) AS A INNER JOIN users ON users.username = A.created_by) AS C ON B.created_by = C.username SET B.created_by = C.userid where B.id > 0");

        Schema::table('reminders', function (Blueprint $table) {
            $table->integer('created_by')->length(11)->charset(null)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reminders', function (Blueprint $table) {
            //
        });
    }
}
