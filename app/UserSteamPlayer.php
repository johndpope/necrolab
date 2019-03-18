<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompositePrimaryKey;

class UserSteamPlayer extends Model {
    use HasCompositePrimaryKey;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_steam_player';
    
    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = NULL;
}
