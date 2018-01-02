<?php

namespace App;

use App\Traits\Uuids;
use App\Traits\CreatesFolder;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use Uuids, CreatesFolder;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Relación N:M entre Group y Account
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function accounts() {
        return $this->belongsToMany('App\Account');
    }
}
