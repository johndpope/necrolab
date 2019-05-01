<?php

namespace App\Jobs\Traits;

trait WorksWithinDatabaseTransaction {
    /**
     * The body of a job in a database transaction that would normally be executed in handle().
     *
     * @return void
     */
    abstract public static function handleDatabaseTransaction(): void;
    
    /**
     * Executes the job within a try/catch and reverts any open database transactions if exceptions are caught.
     *
     * @return void
     */
    public function handle() {
        try {
            $this->handleDatabaseTransaction();
        }
        catch(Throwable $throwable) {
            if(DB::transactionLevel() > 0) {
                DB::rollback();
            }
            
            throw $throwable;
        }
    }
}
