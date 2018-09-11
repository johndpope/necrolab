<?php

namespace App\Jobs\Rankings\Daily\Entries;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\DailyRankings;

class UpdateStats implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DateTime $date) {
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {    
        DB::beginTransaction();
        
        DailyRankings::updateStats($this->date);
        
        DB::commit();
    }
}
