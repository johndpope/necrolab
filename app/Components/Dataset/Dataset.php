<?php
namespace App\Components\Dataset;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use ElcoBvg\Opcache\LengthAwarePaginator as Paginator;
use App\Components\Encoder;
use App\Components\Dataset\Indexes\Index;
use App\Components\Dataset\DataProviders\DataProvider;
use App\Components\Dataset\Traits\HasIndexSubName;
use App\Components\Dataset\Traits\HasIndexFieldName;
use App\Components\Dataset\Traits\HasExternalSiteId;
use App\Components\Dataset\Traits\HasSearchTerm;
use App\Components\Dataset\Traits\HasPage;
use App\Components\Dataset\Traits\HasLimit;
use App\LeaderboardSources;
use App\ExternalSites;

class Dataset {
    use HasIndexSubName, HasIndexFieldName, HasExternalSiteId, HasSearchTerm, HasPage, HasLimit;
    
    protected $leaderboard_source;
    
    protected $cache_base_name;
    
    protected $request;
    
    protected $index;
    
    protected $data_provider;
    
    protected $binary_fields = [];
    
    protected $filter_callback;
    
    protected $sort_callback;
    
    protected $paginator;
    
    public function __construct(LeaderboardSources $leaderboard_source, string $cache_base_name, DataProvider $data_provider) {
        $this->leaderboard_source = $leaderboard_source;
    
        $this->cache_base_name = $cache_base_name;
        
        $this->setDataProvider($data_provider);
    }
    
    public function setIndex(Index $index, string $index_field_Name) {
        $this->index = $index;
        
        $this->setIndexFieldName($index_field_Name);
    }
    
    public function setDataProvider(DataProvider $data_provider) {
        $this->data_provider = $data_provider;
    }
    
    public function setBinaryFields(array $binary_fields) {
        $this->binary_fields = $binary_fields;
    }
    
    public function setFilterCallback(callable $callback) {
        $this->filter_callback = $callback;
    }
    
    public function setSortCallback(callable $callback) {
        $this->sort_callback = $callback;
    }
    
    public function setFromRequest(Request $request) {
        $this->request = $request;
    
        $request_data = $request->validated();
        
        $external_site_id = 0;
        
        if(isset($request_data['site'])) {
            $external_site_id = ExternalSites::getByName($request_data['site'])->id;
        }
        
        $this->setExternalSiteId($external_site_id);
        
        $page = $request_data['page'] ?? 1;
        
        $this->setPage((int)$page);
        
        $limit = $request_data['limit'] ?? 100;
        
        $this->setLimit((int)$limit);
        
        if(!empty($request_data['search'])) {
            $this->setSearchTerm($request_data['search']);
        }
    }
    
    public function process() {
        $opcache = Cache::store('opcache');
        
        // Compile the cache key name that will be used to cache this dataset
        $search_term_hash = sha1($this->search_term);

        $base_key_name = "dataset:{$this->leaderboard_source}:{$this->cache_base_name}:{$this->index_sub_name}:{$this->external_site_id}:{$search_term_hash}";
        $dataset_cache_name = "{$base_key_name}:{$this->page}:{$this->limit}";
        
        // Attempt to retrieve this paginated dataset from cache first
        $this->paginator = $opcache->get($dataset_cache_name);
        
        // If this dataset doesn't exist in cache then create it and save it to cache for 1 minute
        if(empty($this->paginator)) {
            $total_count = NULL;
            
            /* 
                If an index has been specified then process it and use 
                its paginated values as a filter in the data provider.
            */
            if(isset($this->index)) {
                $this->index->setIndexSubName($this->index_sub_name);
                $this->index->setExternalSiteId($this->external_site_id);
                $this->index->setSearchTerm($this->search_term);
                $this->index->setPage($this->page);
                $this->index->setLimit($this->limit);
                
                $this->index->process();
                
                $this->data_provider->setIndexValues($this->index->getPaginatedIndex(), $this->index_field_name);
                $total_count = $this->index->getTotalCount();
            }
            else {
                $this->data_provider->setPage($this->page);
                $this->data_provider->setLimit($this->limit);
            }
            
            // Process the data provider and return its data
            $this->data_provider->process();
            
            $data = $this->data_provider->getData();
            
            // If there are any binary fields specified then retrieve their data streams
            if(!empty($this->binary_fields)) {
                foreach($data as &$row) {
                    foreach($this->binary_fields as $binary_field) {
                        if(!empty($row->$binary_field)) {
                            $row->$binary_field = Encoder::decode(stream_get_contents($row->$binary_field));
                        }
                    }
                }
            }
            
            /* 
                If a filter callback has been specified then loop through the returned 
                data and execute the callback against each row.
            */
            if(!empty($this->filter_callback)) {                
                foreach($data as $index => $entry) {
                    $include_row = call_user_func_array($this->filter_callback, [
                        $entry
                    ]);
                    
                    if(!$include_row) {
                        unset($data[$index]);
                    }
                }
            }
            
            // If a manual sorting callback has been specified then execute that
            if(!empty($this->sort_callback)) {
                $data = $data->sortBy($this->sort_callback);
            }
            
            // Add url metadata to the paginator if a request instance was set
            $request_metadata = [];
            
            if(!empty($this->request)) {
                $request_metadata = [
                    'path'  => $this->request->url(),
                    'query' => $this->request->query(),
                ];
            }
            
            // Create the paginator for this dataset
            $this->paginator = new Paginator($data, $total_count, $this->limit, NULL, $request_metadata);
            
            // Save the paginator to cache for one minute
            $opcache->put($dataset_cache_name, $this->paginator, 5);
        }
    }
    
    public function getPaginator() {
        return $this->paginator;
    }
}
