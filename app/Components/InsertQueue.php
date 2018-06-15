<?php
namespace App\Components;

use Illuminate\Support\Facades\DB;
use App\Components\RecordQueue;
use App\Components\CallbackHandler;

class InsertQueue {
    protected $pdo;

    protected $table_name;
    
    protected $record_queues = [];
    
    protected $parameter_bindings = [];
    
    /**
     * Constructs a VALUES segment with placeholders for each field grouped by each record wrapped in a parenthesis and separated by a comma.
     *
     * Example output: VALUES (?, ?, ?), (?, ?, ?)
     *
     * @param integer $number_of_fields The number of fields in each record.
     * @param integer $number_of_records The number of records for this segment.
     * @param string $placeholder The placeholder character for all values in this segment.
     * @return string The completed update query.
     */
    protected static function getValuesSegment(int $number_of_fields, int $number_of_records, string $placeholder) {
        $record_placeholders = implode(', ', array_fill(0, $number_of_fields, $placeholder));
    
        $multi_record_placeholders = array_fill(0, $number_of_records, $record_placeholders);
            
        $values = 'VALUES (' . implode('), (', $multi_record_placeholders) . ')';
        
        return $values;
    }
    
    /**
     * Constructs a multi insert query based on the parameters passed to it.
     *
     * @param string $table_name The name of the table to insert a new row into.
     * @param mixed $fields The fields to be inserted.
     * @param integer $number_of_records The number of records being inserted.
     * @return string The completed insert query.
     */
    protected static function getMultiInsertQuery(string $table_name, array $fields, int $number_of_records) {
        $insert_field_names = implode(", ", $fields);

        $values = static::getValuesSegment(count($fields), $number_of_records, '?');

        $insert_query = "INSERT INTO {$table_name} ({$insert_field_names})\n {$values};";

        return $insert_query;
    }
    
    public function __construct(string $table_name) {    
        $this->pdo = DB::connection()->getPdo();
    
        $this->table_name = $table_name;
    }
    
    public function addToRecordQueue(RecordQueue $record_queue) {
        $insert_callback = new CallbackHandler();
        
        $insert_callback->setCallback([
            $this,
            'commit'
        ]);
        
        $record_queue->addCommitCallback($insert_callback);
        
        $this->record_queues[] = $record_queue;
    }
    
    public function setParameterBindings(array $parameter_bindings) {
        $this->parameter_bindings = $parameter_bindings;
    }
    
    public function commit(array $records) {   
        if(!empty($records)) {                       
            $first_record = current($records);
            
            $first_record_keys = array_keys($first_record);
            
            $query = static::getMultiInsertQuery($this->table_name, $first_record_keys, count($records));

            $statement = $this->pdo->prepare($query);
            
            if(empty($this->parameter_bindings)) {
                $placeholder_values = [];
            
                array_walk_recursive($records, function($value, $key) use(&$placeholder_values) {
                    $placeholder_values[] = $value;
                });
            
                $statement->execute($placeholder_values);
            }
            else {                            
                $placeholder_index = 1;
            
                foreach($records as $record) {
                    $record = (array)$record;
                
                    foreach($this->parameter_bindings as $index => $parameter_binding) {
                        $statement->bindValue($placeholder_index, $record[$first_record_keys[$index]], $parameter_binding);
                        
                        $placeholder_index += 1;
                    }
                }
                
                $statement->execute();
            }
        }
    }
}