<?php
namespace App\Components;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class PostgresCursor {
    protected $connection;

    protected $name;
    
    protected $query;
    
    protected $chunk_size;
    
    public function __construct(string $name, Builder $query, int $chunk_size) {    
        $this->name = $name;
        
        $this->query = $query;
        
        $this->chunk_size = $chunk_size;
    }
    
    public function setConnection(string $connection) {
        $this->connection = $connection;
    }
    
    public function getRecord() {        
        DB::connection($this->connection)->statement("
            DECLARE {$this->name} CURSOR FOR
            {$this->query->toSql()}
        ", $this->query->getBindings());
        
        $records = [];
        
        do {
            $records = DB::connection($this->connection)->select("
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
