<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScribbleCategory extends Model
{
    /**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'scribble_category';

	/**
	 * The primary key associated with the table.
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';
}
