<?php

namespace App\Jobs\Steam\Players;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Jobs\Traits\WorksWithinDatabaseTransaction;
use GuzzleHttp\Client;
use Steam\Configuration as SteamApiConfiguration;
use Steam\Runner\GuzzleRunner;
use Steam\Steam as SteamApi;
use Steam\Utility\GuzzleUrlBuilder;
use Steam\Command\User\GetPlayerSummaries;
use App\Components\QueueNames;
use App\Components\RecordQueue;
use App\Components\CallbackHandler;
use App\Components\DataManagers\Steam\Players as PlayersManager;
use App\Components\PostgresCursor;
use App\LeaderboardSources;
use App\Players;
use App\Jobs\Steam\Players\SaveImported as SaveImportedJob;

class Import implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WorksWithinDatabaseTransaction;
    
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
    
    protected $date;
    
    protected $data_manager;
    
    protected $steam_api;
    
    protected $group_number = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Execute the job.
     *
     * @return void
     */
    protected function handleDatabaseTransaction(): void {
        $this->date = new DateTime();
        
        
        /* ---------- Configure Data Manager ---------- */ 
        
        $this->data_manager = new PlayersManager($this->date);
        
        $this->data_manager->deleteTemp();
        
        
        /* ---------- Configure Steam API ---------- */ 
        
        $this->steam_api = new SteamApi(new SteamApiConfiguration([
            SteamApiConfiguration::STEAM_KEY => config('services.steam.client_secret')
        ]));
        
        $this->steam_api->addRunner(new GuzzleRunner(new Client(), new GuzzleUrlBuilder()));
        
        
        /* ---------- Configure Callback Handler ---------- */ 
        
        $callback_handler = new CallbackHandler();
        
        $callback_handler->setCallback(function(array $steamid_rows) {
            $steamids = [];
        
            if(!empty($steamid_rows)) {
                foreach($steamid_rows as $steamid_row) {
                    $steamids[] = $steamid_row[0];
                }
            }
        
            $response = $this->steam_api->run(new GetPlayerSummaries($steamids));
            
            $steam_profile_data = mb_convert_encoding($response->getBody()->getContents(), 'UTF-8', 'auto');
            
            $this->data_manager->saveTempFile($this->group_number, $steam_profile_data);
            
            $this->group_number += 1;
        });
        
        $callback_handler->setReattempts(5);
        
        $callback_handler->setReattemptInterval(1);
        
        
        /* ---------- Configure Record Queue ---------- */ 
        
        $record_queue = new RecordQueue(100);
        
        $record_queue->addCommitCallback($callback_handler);
        
        
        DB::beginTransaction();
        
        
        /* ---------- Retrieve outdated record ids and add to record queue ---------- */ 
        
        $cursor = new PostgresCursor(
            'get_outdated_steam_users', 
            Players::getOutdatedIdsQuery($this->data_manager->getLeaderboardSource()),
            20000
        );
        
        foreach($cursor->getRecord() as $outdated_record) {
            $record_queue->addRecord([$outdated_record->external_id]);
        }
        
        DB::commit();
        
        SaveImportedJob::dispatch($this->data_manager)->onQueue(QueueNames::PLAYERS);
    }
}
