<?php

namespace App\Jobs\Legacy\Leaderboards\Entries;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Components\PostgresCursor;
use App\LeaderboardSources;
use App\Leaderboards;
use App\LeaderboardEntries;

class Import implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600;
    
    /**
     * The date used to indicate which month partition to import for.
     *
     * @var DateTime
     */
    protected $date;
    
    /**
     * The leaderboard source used to specify the database schema to save to.
     *
     * @var \App\LeaderboardSources
     */
    protected $leaderboard_source;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DateTime $date) {
        $this->date = $date;
    }
    
    protected function importLeaderboardEntries(): void {
        $leaderboard_ids_by_external_id = Leaderboards::getIdsByExternalId($this->leaderboard_source);
    
        LeaderboardEntries::createTemporaryTable($this->leaderboard_source);
        
        $insert_queue = LeaderboardEntries::getTempInsertQueue($this->leaderboard_source, 15000);
        
        $legacy_cursor = new PostgresCursor('legacy_leaderboard_entries', LeaderboardEntries::getLegacyImportQuery($this->leaderboard_source, $this->date), 20000);
        
        $legacy_cursor->setConnection('legacy');
        
        $previous_partition_name = '';

        foreach($legacy_cursor->getRecord() as $legacy_record) {
            if(isset($leaderboard_ids_by_external_id[$legacy_record->lbid])) {                        
                $insert_queue->addRecord([
                    'leaderboard_snapshot_id' => $legacy_record->leaderboard_snapshot_id,
                    'player_pb_id' => $legacy_record->steam_user_pb_id,
                    'player_id' => $legacy_record->steam_user_id,
                    'rank' => $legacy_record->rank
                ]);
            }
        }
        
        $insert_queue->commit();
        
        LeaderboardEntries::saveNewTemp($this->leaderboard_source, $this->date);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $this->leaderboard_source = LeaderboardSources::getByName('steam');
    
        DB::beginTransaction();
        DB::connection('legacy')->beginTransaction();
        
        $this->importLeaderboardEntries();
        
        DB::connection('legacy')->commit();
        DB::commit();
    }
}
