<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowSection extends Model
{
    /**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'workflow_section';

	/**
	 * The primary key associated with the table.
	 *
	 * @var string
	 */
    protected $primaryKey = 'id';
}
