<?php

namespace App;

use App\Traits\Uuids;
use App\Traits\CreatesFolder;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use Uuids, CreatesFolder;
    
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Relación 1:1 entre User y Account
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo('App\User');
    }
}
