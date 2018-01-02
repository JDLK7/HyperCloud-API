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

    /**
     * Devuelve todos los archivos y carpetas de un grupo.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function files(){
        return $this->archives()->get()->merge($this->folders()->get());
    }

    /**
     * Relación 1:N entre Group y File para la subclase Archive
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function archives(){
        return $this->hasMany('App\Archive');
    }

    /**
     * Relación 1:N entre Group y File para la subclase Folder
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function folders(){
       return $this->hasMany('App\Folder');
    }
}
