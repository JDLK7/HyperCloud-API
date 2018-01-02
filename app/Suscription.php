<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class Suscription extends Model
{
    use Uuids;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Relación 1:N entre Suscription y Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accounts(){
        return $this->hasMany('App\Account');
    }
}
