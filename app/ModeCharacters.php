<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompositePrimaryKey;

class ModeCharacters extends Model {
    use HasCompositePrimaryKey;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mode_characters';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'mode_id',
        'character_id'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
