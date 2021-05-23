<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowProcess extends Model
{
    /**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'workflow_process';

	/**
	 * The primary key associated with the table.
	 *
	 * @var string
	 */
    protected $primaryKey = 'id';
}
