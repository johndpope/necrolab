<?php

namespace App;

use ElcoBvg\Opcache\Model;
use App\Components\CacheNames\Core as CacheNames;
use App\Traits\GetById;
use App\Traits\GetByName;
use App\Traits\StoredInCache;

class ExternalSites extends Model {
    use GetById, GetByName, StoredInCache;

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
    
    public static function getValidationRules() {
        return [
            'name' => 'required|max:100|unique:modes',
            'display_name' => 'required|max:100',
            'active' => 'required|integer|min:0|max:1',
        ];
    }
    
    public static function getCacheQuery() {
        return static::orderBy('name', 'asc');
    }
    
    public static function addSiteIdSelectFields($query) {        
        $sites_by_id = static::getAllById();
        
        if(!empty($sites_by_id)) {
            foreach($sites_by_id as $site) {
                if($site->active == 1) {                
                    $query->addSelect([
                        "su.{$site->name}_user_id"
                    ]);
                }
            }
        }
    }
    
    public static function addToSiteIdIndexes(array &$indexes, object $entry, string $base_index_name, int $index_value, int $index_key = NULL) {
        $sites = static::getAllByid();
        
        if(!empty($sites)) {
            foreach($sites as $site) {
                if($site->active == 1) {
                    $site_id_name = "{$site->name}_user_id";
                
                    if(!empty($entry->$site_id_name)) {
                        if(!isset($index_key)) {
                            $indexes[
                                CacheNames::getIndex($base_index_name, [
                                    $site->external_site_id
                                ])
                            ][] = $index_value;
                        }
                        else {
                            $indexes[
                                CacheNames::getIndex($base_index_name, [
                                    $site->external_site_id
                                ])
                            ][$index_key] = $index_value;
                        }
                    }
                }
            }
        }
        
        if(!isset($index_key)) {
            $indexes[CacheNames::getIndex($base_index_name, array(
                CacheNames::getNoId()
            ))][] = $index_value;
        }
        else {
            $indexes[CacheNames::getIndex($base_index_name, array(
                CacheNames::getNoId()
            ))][$index_key] = $index_value;
        }
    }
}