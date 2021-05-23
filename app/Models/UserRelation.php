<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRelation extends Model
{
    /**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'user_relation';

	/**
	 * The primary key associated with the table.
	 *
	 * @var string
	 */
    protected $primaryKey = 'id';
}
