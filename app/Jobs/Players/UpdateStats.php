<?php

namespace App\Jobs\Players;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Jobs\Traits\WorksWithinDatabaseTransaction;
use App\Components\PostgresCursor;
use App\LeaderboardSources;
use App\LeaderboardEntries;
use App\Dates;
use App\PlayerPbs;
use App\PlayerStats;
use App\LeaderboardDetailsColumns;

class UpdateStats implements ShouldQueue {
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

    /**
     * The leaderboard source used to determine the schema to generate rankings on.
     *
     * @var \App\LeaderboardSources
     */
    protected $leaderboard_source;

    /**
     * The date that stats will be generated for.
     *
     * @var \App\Dates
     */
    protected $date;

    /**
     * Create a new job instance.
     *
     * @param \App\LeaderboardSources $leaderboard_source The leaderboard source context for this process.
     * @param \App\Dates $date The date that this process works within the context of.
     * @return void
     */
    public function __construct(LeaderboardSources $leaderboard_source, Dates $date) {
        $this->leaderboard_source = $leaderboard_source;
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    protected function handleDatabaseTransaction(): void {
        $first_place_ranks_by_player = LeaderboardEntries::getFirstPlaceRanksByPlayer($this->leaderboard_source, $this->date);
        $details_columns = LeaderboardDetailsColumns::all();

        DB::beginTransaction();

        PlayerStats::createTemporaryTable($this->leaderboard_source);

        $stats_insert_queue = PlayerStats::getTempInsertQueue($this->leaderboard_source, 3000);

        $cursor = new PostgresCursor(
            "{$this->leaderboard_source->name}_players_stats_update",
            PlayerPbs::getPlayerStatsQuery($this->leaderboard_source, $this->date),
            3000
        );

        foreach($cursor->getRecord() as $player_stats) {
            $player_stats->date_id = $this->date->id;
            $player_stats->first_place_ranks = $first_place_ranks_by_player[$player_stats->player_id] ?? 0;

            $details = [];

            foreach($details_columns as $details_column) {
                $details_field_name = "details_{$details_column->name}";

                if(isset($player_stats->$details_field_name)) {
                    $details[$details_column->name] = $player_stats->$details_field_name;
                }
            }

            $record = [
                'player_id' => $player_stats->player_id,
                'release_id' => $player_stats->release_id,
                'pbs' => $player_stats->pbs,
                'leaderboards' => $player_stats->leaderboards,
                'first_place_ranks' => $player_stats->first_place_ranks,
                'dailies' => $player_stats->dailies,
                'leaderboard_types' => $player_stats->leaderboard_types,
                'characters' => $player_stats->characters,
                'modes' => $player_stats->modes,
                'seeded_types' => $player_stats->seeded_types,
                'multiplayer_types' => $player_stats->multiplayer_types,
                'soundtracks' => $player_stats->soundtracks,
                'details' => json_encode($details)
            ];

            $hash = substr(md5(serialize($record)), 0, 8);

            /*
                Date is added after hashing to help detect if the stats record
                is the same on any other date for the same release
            */
            $record['date_id'] = $player_stats->date_id;
            $record['hash'] = $hash;

            $stats_insert_queue->addRecord($record);
        }

        $stats_insert_queue->commit();

        PlayerStats::addOverallToTemp($this->leaderboard_source);

        PlayerStats::clear($this->leaderboard_source, $this->date);

        PlayerStats::saveNewTemp($this->leaderboard_source);

        DB::commit();
    }
}
