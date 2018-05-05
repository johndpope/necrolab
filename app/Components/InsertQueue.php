<?php
namespace App\Components;

use Illuminate\Support\Facades\DB;

class InsertQueue {
    protected $pdo;

    protected $table_name;

    protected $records = [];
    
    protected $commit_count;
    
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
    protected static function getValuesSegment($number_of_fields, $number_of_records, $placeholder = '?') {
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
    protected static function getMultiInsertQuery($table_name, array $fields, $number_of_records) {
        $insert_field_names = implode(", ", $fields);

        $values = static::getValuesSegment(count($fields), $number_of_records);

        $insert_query = "INSERT INTO {$table_name} ({$insert_field_names})\n {$values};";

        return $insert_query;
    }
    
    public function __construct(string $table_name, int $commit_count) {
        $this->pdo = DB::connection()->getPdo();
    
        $this->table_name = $table_name;
        
        $this->setCommitCount($commit_count);
    }
    
    public function setCommitCount(int $commit_count) {
        $this->commit_count = $commit_count;
    }
    
    public function addRecord(array $record) {
        if(count($this->records) >= $this->commit_count) {
            $this->commit();
        }
        
        $this->records[] = $record;
    }
    
    public function addRecords(array &$records) {
        if(!empty($records)) {
            foreach($records as &$record) {
                $this->addRecord($record);
            }
        }
    }
    
    public function commit() {   
        if(!empty($this->records)) {                       
            $first_record = current($this->records);
            
            $query = static::getMultiInsertQuery($this->table_name, array_keys($first_record), count($this->records));
            
            $placeholder_values = [];
            
            array_walk_recursive($this->records, function($value, $key) use(&$placeholder_values) {
                $placeholder_values[] = $value;
            });

            $statement = $this->pdo->prepare($query);
            
            $statement->execute($placeholder_values);
            
            $this->records = [];
        }
    }
}