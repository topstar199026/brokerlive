<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsForJournaltypes extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('journaltypes', function (Blueprint $table) {
			$table->timestamps();
			$table->integer('created_by')->nullable();
			$table->integer('updated_by')->nullable();
		});

		DB::connection()->getpdo()->exec("ALTER TABLE `journaltypes` CHANGE COLUMN `stamp_updated` `stamp_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()");
		DB::connection()->getpdo()->exec("UPDATE `journaltypes` SET  created_at = stamp_created");
		DB::connection()->getpdo()->exec("UPDATE `journaltypes` SET  updated_at = stamp_updated");

		Schema::table('journaltypes', function (Blueprint $table) {
			$table->dropColumn(['stamp_created', 'stamp_updated']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('journaltypes', function (Blueprint $table) {
			$table->timestamps('stamp_created');
			$table->timestamps('stamp_updated');
		});

		DB::connection()->getpdo()->exec("UPDATE `journaltypes` SET   stamp_created = created_at");
		DB::connection()->getpdo()->exec("UPDATE `journaltypes` SET   stamp_updated = updated_at");

		Schema::table('journaltypes', function (Blueprint $table) {
			$table->dropColumn(['created_at', 'updated_at', 'created_by', 'updated_by']);
		});
	}
}
