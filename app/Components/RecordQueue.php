<?php
namespace App\Components;

use Exception;
use App\Components\CallbackHandler;

class RecordQueue {  
    protected $records = [];
    
    protected $commit_callbacks = [];
    
    protected $commit_count;
    
    public function __construct(int $commit_count) {        
        $this->setCommitCount($commit_count);
    }
    
    public function setCommitCount(int $commit_count) {
        $this->commit_count = $commit_count;
    }
    
    public function addCommitCallback(CallbackHandler $commit_callback) {
        $this->commit_callbacks[] = $commit_callback;
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
            if(!empty($this->commit_callbacks)) {
                foreach($this->commit_callbacks as $commit_callback) {                    
                    $commit_callback->prependArgument($this->records);
                    
                    $commit_callback->execute();
                    
                    $commit_callback->removeFirstArgument();
                }
            }
            else {
                throw new Exception("No commit callbacks were specified for this record queue.");
            }
            
            $this->records = [];
        }
    }
}