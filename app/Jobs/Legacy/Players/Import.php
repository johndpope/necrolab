<?php

namespace App\Jobs\Legacy\Players;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Components\PostgresCursor;
use App\LeaderboardSources;
use App\Players;

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
    
    protected $leaderboard_source;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {}
    
    protected function importPlayers(): void {
        Players::createTemporaryTable($this->leaderboard_source);
        
        $insert_queue = Players::getTempInsertQueue($this->leaderboard_source, 7000);
        
        $legacy_cursor = new PostgresCursor('legacy_players', Players::getLegacyImportQuery(), 10000);
        
        $legacy_cursor->setConnection('legacy');

        foreach($legacy_cursor->getRecord() as $legacy_record) {
            $insert_queue->addRecord([
                'created' => $legacy_record->updated,
                'updated' => $legacy_record->updated,
                'id' => $legacy_record->steam_user_id,
                'external_id' => $legacy_record->steamid,
                'username' => $legacy_record->personaname,
                'profile_url' => $legacy_record->profileurl,
                'avatar_url' => $legacy_record->avatarfull
            ]);
        }
        
        $insert_queue->commit();
        
        Players::saveLegacyTemp($this->leaderboard_source);
        
        Players::syncManualSequence($this->leaderboard_source);
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
        
        $this->importPlayers();
        
        DB::connection('legacy')->commit();
        DB::commit();
    }
}
