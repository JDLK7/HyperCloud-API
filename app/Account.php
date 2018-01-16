<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use Uuids;
    
    protected static function boot() {
        
        parent::boot();

        /**
         * Crea la carpeta del usuario y se la asocia.
         */
        static::created(function ($account) {
            $usersFolder = Folder::where('path', 'users/')->first();

            $accountFolder = new Folder([
                'name' => $account->userName, 
                'path' => $account->path, 
                'size' => 4096
            ]);
            $accountFolder->folder()->associate($usersFolder);
            $accountFolder->account()->associate($account);
            $accountFolder->save();
        });
    }
    
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Atributos asignables en masa.
     *
     * @var array
     */
    protected $fillable = ['userName'];

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
        return $this->hasMany('App\File');
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

    /**
     * Accessor que devuelve la ruta del directorio raíz del usuario.
     *
     * @return string
     */
    public function getPathAttribute() {
        return "users/$this->userName/";
    }

    /**
     * Devuelve la carpeta del usuario.
     *
     * @return \App\Folder
     */
    public function folder() {
        return Folder::where('path', $this->path)->first();
    }

    /**
     * Accessor que devuelve la ruta de la imagen de perfil del usuario
     *
     * @return string
     */
    public function getAvatarPathAttribute() {
        return "";
    }

    /**
     * Comprueba si la cuenta tiene espacio suficiente para albergar $bytes.
     *
     * @param int $bytes
     * @return boolean
     */
    public function canStore($bytes) {
        $spaceLeft = $this->suscription->spaceOffer - ($this->space + $bytes);

        return $spaceLeft >= 0;
    }
}
