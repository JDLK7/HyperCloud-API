<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;

class File extends Model
{
    use Uuids, SingleTableInheritanceTrait;

    /**
     * Nombre de la que contiene el mapeado del modelo.
     *
     * @var string
     */
    protected $table = "files";
    
    /**
     * Nombre de la columna que contiene el tipo de modelo a instanciar.
     *
     * @var string
     */
    protected static $singleTableTypeField = 'type';

    /**
     * Subclases del modelo que que se instanciarán según el valor de "type".
     *
     * @var array
     */
    protected static $singleTableSubclasses = [Archive::class, Folder::class];

    /**
     * Variables asignables en masa.
     *
     * @var array
     */
    protected $fillable = ['name', 'path', 'extension', 'size'];

    /**
     * Campos ocultos al serializar el modelo.
     *
     * @var array
     */
    protected $hidden = ['account', 'group', 'folder', 'created_at', 'path'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['icon'];

    /**
     * Indica si los IDs son auto-incrementables.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Relación 1:N entre Account y File. Devuelve la cuenta a la 
     * que pertenece un fichero (archivo o carpeta).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account() {
        return $this->belongsTo('App\Account');
    }

    /**
     * Relación 1:N entre Group y File. Devuelve el grupo 
     * al que pertenece un fichero (archivo o carpeta).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group() {
        return $this->belongsTo('App\Group');
    }

    /**
     * Relación reflexiva que representa las carpetas 
     * contenidas dentro de otra carpeta. Devuelve
     * la carpeta padre del fichero actual.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function folder() {
        return $this->belongsTo('App\Folder');
    }

    /**
     * Comprueba si el fichero es una carpeta.
     *
     * @return boolean
     */
    public function isFolder() {
        return $this->type === 'folder';
    }

    /**
     * Accessor que devuelve el icono según el formato del fichero.
     *
     * @return string
     */
    public function getIconAttribute() {
        if($this->isFolder()) {
            return 'fa-folder';
        }

        $iconExtension = DB::table('extension_icon')
            ->where('extension', $this->extension)->first();

        if(!is_null($iconExtension)) {
            return $iconExtension->icon;
        }

        return 'fa-question';
    }
}
