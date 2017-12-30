<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Nanigans\SingleTableInheritance\SingleTableInheritanceTrait;

abstract class File extends Model
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
    public function account(){
        return $this->belongsTo('App\Account');
    }
}
