<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsForDealstatuses extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('dealstatuses', function (Blueprint $table) {
			$table->timestamps();
			$table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });

        DB::connection()->getpdo()->exec("ALTER TABLE `dealstatuses` CHANGE COLUMN `stamp_updated` `stamp_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()");
		DB::connection()->getpdo()->exec("UPDATE `dealstatuses` SET  created_at = stamp_created");
		DB::connection()->getpdo()->exec("UPDATE `dealstatuses` SET  updated_at = stamp_updated");

		Schema::table('dealstatuses', function (Blueprint $table) {
			$table->dropColumn(['stamp_created', 'stamp_updated']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('dealstatuses', function (Blueprint $table) {
			$table->timestamps('stamp_created');
			$table->timestamps('stamp_updated');
		});

		DB::connection()->getpdo()->exec("UPDATE `dealstatuses` SET   stamp_created = created_at");
		DB::connection()->getpdo()->exec("UPDATE `dealstatuses` SET   stamp_updated = updated_at");

		Schema::table('dealstatuses', function (Blueprint $table) {
			$table->dropColumn(['created_at', 'updated_at', 'created_by', 'updated_by']);
		});
	}
}
