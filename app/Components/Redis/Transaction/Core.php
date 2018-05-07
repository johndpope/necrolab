<?php
/**
* The core object for redis transactions.
* Copyright (c) 2018, Tommy Bolger
* All rights reserved.
* 
* Redistribution and use in source and binary forms, with or without 
* modification, are permitted provided that the following conditions 
* are met:
* 
* Redistributions of source code must retain the above copyright 
* notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright 
* notice, this list of conditions and the following disclaimer in the 
* documentation and/or other materials provided with the distribution.
* Neither the name of the author nor the names of its contributors may 
* be used to endorse or promote products derived from this software 
* without specific prior written permission.
* 
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
* COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER 
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN 
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
* POSSIBILITY OF SUCH DAMAGE.
*/

namespace App\Components\Redis\Transaction;

use Redis;
use Illuminate\Redis\Connections\PhpRedisConnection;
use App\Components\CallbackHandler;

class Core {
    /**
    * @var integer The type of transaction.
    */ 
    protected $transaction_type;
    
    protected $client;
    
    protected $commit_size;

    /**
    * @var object The redis transaction object.
    */ 
    protected $current_transaction;
    
    /**
    * @var integer The number of queued commands in the current transaction.
    */ 
    protected $current_transaction_size = 0;
    
    /**
    * @var array The callbacks to execute when a commit is issued.
    */ 
    protected $commit_callbacks = [];
    
    /**
     * Initializes this instance of Transaction.
     *
     * @param Illuminate\Support\Facades\Redis $client The Redis client.
     * @return void
     */
    public function __construct(PhpRedisConnection $client, int $commit_size) {
        $this->client = $client;
        
        $this->commit_size = $commit_size;
        
        $this->intializeTransaction();
    }
    
    protected function intializeTransaction() {
        $this->current_transaction = $this->client->multi($this->transaction_type);
    }
    
    /**
     * Catches all function calls not present in this class and passes them to the redis transaction object.
     *
     * @param string The function name.
     * @param array the function arguments.
     * @return mixed
     */
    public function __call(string $function_name, array $arguments) {
        if($this->current_transaction_size == $this->commit_size) {
            $this->commit();
            
            $this->intializeTransaction();
        }
        
        $return_value = call_user_func_array(array($this->current_transaction, $function_name), $arguments);
        
        $this->current_transaction_size += 1;
        
        return $return_value;
    }
    
    /**
     * Adds a callback to be executed when a transaction is committed.
     *
     * @return void
     */
    public function addCommitCallback(CallbackHandler $commit_callback) {
        $this->commit_callbacks[] = $commit_callback;
    }
    
    /**
     * Commits the current transaction, resets the transaction size counter, and executes and post process functionality.
     *
     * @return void
     */
    public function commit() {
        $return_data = $this->current_transaction->exec();
        
        $this->current_transaction_size = 0;
        
        if(!empty($return_data)) {
            if(!empty($this->commit_callbacks)) {
                foreach($this->commit_callbacks as $commit_callback) {
                    $commit_callback->prependArgument($return_data);
                    
                    $commit_callback->execute();
                    
                    $commit_callback->removeFirstArgument();
                }
            }
        }
        
        unset($return_data);
        $this->current_transaction = NULL;
    }
    
    /**
     * Returns the object used to directly interact with redis.
     * This is for operations that require references to be passed to Redis functions, such as scan.     
     *
     * @return RedisClient the RedisClient instance.
     */
    public function getCurrentTransaction() {
        return $this->current_transaction;
    }
}