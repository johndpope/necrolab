<?php
namespace App\Components\Dataset\Indexes;

use stdClass;
use Illuminate\Support\Facades\Cache;
use App\Components\CacheNames\Users\Steam as SteamUsersCacheNames;
use App\Components\Dataset\Indexes\Index;
use App\EntryIndexes;

class Sql
extends Index {    
    protected $total_count = 0;
    
    protected $paginated_index = [];
    
    public function __construct(string $index_name) {
        $this->setIndexName($index_name);
    }    
    
    public function process() {
        $opcache = Cache::store('opcache');

        $filtered_index_key_name = "{$this->index_name}:{$this->index_sub_name}:{$this->external_site_id}:{$this->search_term}";
        $paginated_index_key_name = "index:{$filtered_index_key_name}:{$this->page}:{$this->limit}";
        
        // Attempt to retrieve this paginated index from cache first
        $this->paginated_index = $opcache->get($paginated_index_key_name);
        
        // If this paginated index doesn't exist in cache then create it and save it to cache for 1 minute
        if(empty($this->paginated_index)) {            
            // Attempt to retrieve the filtered index from cache first
            $filtered_index = $opcache->get($filtered_index_key_name);

            // If there is no filtered index already retrieved from cache then retrieve the original base index from entry_indexes
            if(empty($filtered_index)) {
                // Initialize the object used for storing index data
                $filtered_index = new stdClass();
                
                $filtered_index->data = [];
                
                $base_index_data = EntryIndexes::getDecodedRecord("{$this->index_name}:$this->external_site_id", $this->index_sub_name);
                
                if(!empty($base_index_data)) {
                    // If a search term has been specified then filter the base index by it
                    if(!empty($this->search_term)) {
                        $steam_usernames = EntryIndexes::getDecodedRecord(SteamUsersCacheNames::getUsersByName());
                        
                        /*
                            Loop through the base index and use the steam_user_id in it to match search term to the corresponding 
                            steam username index record. If there is a match then add that row to the filtered index.
                        */
                        foreach($base_index_data as $steam_user_id) {
                            if(isset($steam_usernames[$steam_user_id]) && stripos($steam_usernames[$steam_user_id]) !== false) {
                                $filtered_index->data[] = $steam_user_id;
                            }
                        }
                    }
                    // If a search term is not specified then set the filtered index as the base index
                    else {
                        $filtered_index->data = $base_index_data;
                    }
                    
                    unset($base_index_data);
                }
                
                // Get a count of how many entries are in the index for pagination
                $filtered_index->count = count($filtered_index->data);

                // Save the filtered index to opcache for one minute
                $opcache->put($filtered_index_key_name, $filtered_index, 1);
            }
            
            // Set the total count of this index
            $this->total_count = $filtered_index->count;
            
            // Calculate the offset based on the page and limit
            $offset = static::getCalculatedOffset($this->page, $this->limit);
            
            // Slice the filtered index from the filtered index based on the page and the limit to get the paginated index
            $this->paginated_index = array_slice($filtered_index->data, $offset, $this->limit);
            
            // Save the paginated index to cache for one minute
            $opcache->put($paginated_index_key_name, $this->paginated_index, 1);
        }
    }
    
    public function getTotalCount() {
        return $this->total_count;
    }
    
    public function getPaginatedIndex() {
        return $this->paginated_index;
    }
}
