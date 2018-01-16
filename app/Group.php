<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use Uuids;

    protected static function boot() {
        
        parent::boot();

        /**
         * Crea la carpeta del usuario y se la asocia.
         */
        static::created(function ($group) {
            $groupsFolder = Folder::where('path', 'groups/')->first();

            $groupFolder = new Folder([
                'name' => $group->name, 
                'path' => $group->path, 
                'size' => 4096
            ]);
            $groupFolder->folder()->associate($groupsFolder);
            $groupFolder->group()->associate($group);
            $groupFolder->save();
        });
    }

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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files() {
        return $this->hasMany('App\File');
    }

    /**
     * Relación 1:N entre Group y File para la subclase Archive
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function archives() {
        return $this->hasMany('App\Archive');
    }

    /**
     * Relación 1:N entre Group y File para la subclase Folder
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function folders() {
       return $this->hasMany('App\Folder');
    }

    /**
     * Accessor que devuelve la ruta del directorio raíz del grupo.
     *
     * @return string
     */
    public function getPathAttribute() {
        return "groups/$this->name/";
    }

    /**
     * Comprueba si las cuentas del grupo tienen 
     * espacio suficiente para albergar $bytes.
     *
     * @param int $bytes
     * @return boolean
     */
    public function canStore($bytes) {
        $accountsCount = $this->accounts()->count();
        $bytesPerAccount = $bytes / $accountsCount;

        foreach ($this->accounts as $account) {
            if( !$account->canStore($bytesPerAccount)) {
                return false;
            }
        }

        return true;
    }
}
