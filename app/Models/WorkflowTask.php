<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowTask extends Model
{
    /**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'workflow_task';

	/**
	 * The primary key associated with the table.
	 *
	 * @var string
	 */
    protected $primaryKey = 'id';
}
