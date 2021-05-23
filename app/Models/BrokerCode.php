<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrokerCode extends Model
{
    /**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'brokercodes';

	/**
	 * The primary key associated with the table.
	 *
	 * @var string
	 */
    protected $primaryKey = 'id';
    
    public function broker()
    {
        return $this->belongsTo('App\Models\User', 'broker_id');
    }
    public function lender()
    {
        return $this->belongsTo('App\Models\Lender', 'lender_id');
    }
}
