<?php
namespace App\Components;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class PostgresCursor {
    protected $name;
    
    protected $query;
    
    protected $chunk_size;
    
    public function __construct(string $name, Builder $query, int $chunk_size) {    
        $this->name = $name;
        
        $this->query = $query;
        
        $this->chunk_size = $chunk_size;
    }
    
    public function getRecord() {        
        DB::statement("
            DECLARE {$this->name} CURSOR FOR
            {$this->query->toSql()}
        ", $this->query->getBindings());
        
        $records = [];
        
        do {
            $records = DB::select("
                FETCH {$this->chunk_size}
                FROM {$this->name}
            ");
            
            foreach($records as $record) {
                yield $record;
            }
        }
        while(!empty($records));
    }
}