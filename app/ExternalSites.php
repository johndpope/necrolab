<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExternalSites extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'external_sites';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'external_site_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
