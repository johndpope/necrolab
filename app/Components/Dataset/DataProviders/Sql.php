<?php
namespace App\Components\Dataset\DataProviders;

use Illuminate\Database\Query\Builder;
use App\Components\Dataset\DataProviders\DataProvider;
use App\Components\Dataset\Traits\HasIndexFieldName;

class Sql
extends DataProvider {
    use HasIndexFieldName;

    protected $query;
    
    protected $index_values = [];
    
    protected $data = [];
    
    public function __construct(Builder $query) {        
        $this->query = $query;
    }
    
    public function setIndexValues(array $index_values, string $index_field_name) {
        $this->index_values = $index_values;
        $this->setIndexFieldName($index_field_name);
    }
    
    public function process() {            
        // If an index field name has been specified then use the corresponding index values to filter the query
        if(!empty($this->index_field_name)) {                
            // Implode these index values into a value that's usable with Postgres' ANY criteria
            $any_values = '{' . implode(',', $this->index_values) . '}';
            
            // Add the ANY criteria to the dataset query           
            $this->query->whereRaw("{$this->index_field_name} = ANY(?::integer[])", [
                $any_values
            ]);
        }
        else {
            // Set the limit criteria in the query
            $this->query->limit($this->limit);
            
            // Calculate and set the offset criteria in the query
            $offset = static::getCalculatedOffset($this->page, $this->limit);
                
            $this->query->offset($offset);
        }

        // Retrieve the dataset from the database
        $this->data = $this->query->get();
    }
    
    public function getData() {
        return $this->data;
    }
}
