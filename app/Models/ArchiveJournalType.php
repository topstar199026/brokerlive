<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ArchiveJournalType extends Model {
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'archive_journaltypes';

	/**
	 * The primary key associated with the table.
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';

	/**
	 * [createdBy description]
	 * @return [type] [description]
	 */
	public function createdBy() {
		return $this->belongsTo(User::class, 'created_by', 'id');
	}

	/**
	 * [updatedBy description]
	 * @return [type] [description]
	 */
	public function updatedBy() {
		return $this->belongsTo(User::class, 'updated_by', 'id');
	}
}
