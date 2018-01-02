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

    /**
     * Devuelve todos los archivos y carpetas de una cuenta.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function files() {
        return $this->folders()->get()
                    ->merge($this->archives()->get());
    }

    /**
     * Relación 1:N entre Account y File para la subclase Archive
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function archives() {
        return $this->hasMany('App\Archive');
    }

    /**
     * Relación 1:N entre Account y File para la subclase Folder
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function folders() {
       return $this->hasMany('App\Folder');
    }

    /**
     * Relación 1:N entre Suscription y Account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function suscription() {
        return $this->belongsTo('App\Suscription');
    }

    /**
     * Relación N:M entre Group y Account
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups() {
        return $this->belongsToMany('App\Group');
    }
}
