<?php
namespace App\Components;

use Exception;
use stdClass;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use ElcoBvg\Opcache\LengthAwarePaginator as Paginator;
use App\Components\CacheNames\Users\Steam as SteamUsersCacheNames;
use App\EntryIndexes;
use App\ExternalSites;

class EntriesDataset {    
    protected $index_name;
    
    protected $index_sub_name = '';
    
    protected $index_field_name;
    
    protected $request;

    protected $query;
    
    protected $search_term = '';
    
    protected $page = 1;
    
    protected $limit = 100;
    
    protected $external_site_id = 0;
    
    protected $paginator;
    
    public function __construct(string $index_name, string $index_field_name, Builder $query) {
        $this->index_name = $index_name;
        $this->index_field_name = $index_field_name;
        $this->query = $query;
    }
    
    public function setIndexSubName(string $index_sub_name) {
        $this->index_sub_name = $index_sub_name;
    }
    
    public function setSearchTerm(string $search_term) {
        if(empty($search_term)) {
            $search_term = '';
        }
    
        $this->search_term = $search_term;
    }
    
    public function setPage(int $page) {
        $this->page = $page;
    }
    
    public function setLimit(int $limit) {
        $this->limit = $limit;
    }
    
    public function setExternalSiteId(int $external_site_id) {
        $this->external_site_id = $external_site_id;
    }
    
    public function setFromRequest(Request $request) {
        $this->request = $request;
    
        $request_data = $request->validated();
        
        $external_site_id = 0;
        
        if(isset($request_data['site'])) {
            $external_site_id = ExternalSites::getByName($request_data['site'])->external_site_id;
        }
        
        $this->setExternalSiteId($external_site_id);
        
        $page = $request_data['page'] ?? 1;
        
        $this->setPage((int)$page);
        
        $limit = $request_data['limit'] ?? 100;
        
        $this->setLimit((int)$limit);
    }
    
    public function process() {
        $opcache = Cache::store('opcache');
        
        $base_key_name = "{$this->index_name}:{$this->index_sub_name}:{$this->external_site_id}";

        $filtered_index_key_name = "{$base_key_name}:{$this->search_term}";
        $cached_response_key_name = "{$filtered_index_key_name}:{$this->page}:{$this->limit}";
        
        // Attempt to retrieve this paginated dataset from cache first
        $this->paginator = $opcache->get($cached_response_key_name);
        
        // If this dataset doesn't exist in cache then create it and save it to cache for 1 minute
        if(empty($this->paginator)) {
            $filtered_index = NULL;
        
            /*
                If a search term has been specified then attempt to retrieve 
                the index that's already been filtered by the search term
            */            
            if(!empty($this->search_term)) {
                $filtered_index = $opcache->get($filtered_index_key_name);
            }

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
            
            // Calculate the offset based on the page and limit
            $offset = ($this->page - 1) * $this->limit;
            
            // Slice the paginated index from the filtered index based on the page and the limit
            $filtered_index_page = array_slice($filtered_index->data, $offset, $this->limit);
            
            // Implode this page index into a value that's usable with Postgres' ANY criteria
            $any_values = '{' . implode(',', $filtered_index_page) . '}';
            
            // Add the ANY criteria to the dataset query           
            $this->query->whereRaw("{$this->index_field_name} = ANY(?::integer[])", [
                $any_values
            ]);
            
            // Retrieve the dataset from the database
            $data = $this->query->get();
            
            // Add url metadata to the paginator if a request instance was set
            $request_metadata = [];
            
            if(!empty($this->request)) {
                $request_metadata = [
                    'path'  => $this->request->url(),
                    'query' => $this->request->query(),
                ];
            }
            
            // Create the paginator for this dataset
            $this->paginator = new Paginator($data, $filtered_index->count, $this->limit, NULL, $request_metadata);
            
            // Save the paginator to cache for one minute
            $opcache->put($cached_response_key_name, $this->paginator, 1);
        }
    }
    
    public function getPaginator() {
        return $this->paginator;
    }
}