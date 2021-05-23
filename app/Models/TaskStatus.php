<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    /**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = '_task_status';

	/**
	 * The primary key associated with the table.
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';
	protected $_loaded = false;

	public function loaded()
	{
		return $this->_loaded;
	}
}
