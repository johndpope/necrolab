<?php

namespace App\Jobs\Attributes;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use App\Components\Encoder;
use App\LeaderboardSources;
use App\LeaderboardTypes;
use App\Characters;
use App\Releases;
use App\Modes;
use App\SeededTypes;
use App\MultiplayerTypes;
use App\Soundtracks;
use App\DailyRankingDayTypes;
use App\LeaderboardDetailsColumns;
use App\DataTypes;
use App\ExternalSites;
use App\Http\Resources\LeaderboardSourcesResource;
use App\Http\Resources\LeaderboardTypesResource;
use App\Http\Resources\CharactersResource;
use App\Http\Resources\ReleasesResource;
use App\Http\Resources\ModesResource;
use App\Http\Resources\SeededTypesResource;
use App\Http\Resources\MultiplayerTypesResource;
use App\Http\Resources\SoundtracksResource;
use App\Http\Resources\DailyRankingDayTypesResource;
use App\Http\Resources\LeaderboardDetailsColumnsResource;
use App\Http\Resources\DataTypesResource;
use App\Http\Resources\ExternalSitesResource;

class GenerateJson implements ShouldQueue {
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
    public function handle() {
        $attributes = [
            'leaderboard_sources' => LeaderboardSourcesResource::collection(LeaderboardSources::all()),
            'leaderboard_types' => LeaderboardTypesResource::collection(LeaderboardTypes::all()),
            'characters' => CharactersResource::collection(Characters::all()),
            'releases' => ReleasesResource::collection(Releases::all()),
            'modes' => ModesResource::collection(Modes::all()),
            'seeded_types' => SeededTypesResource::collection(SeededTypes::all()),
            'multiplayer_types' => MultiplayerTypesResource::collection(MultiplayerTypes::all()),
            'soundtracks' => SoundtracksResource::collection(Soundtracks::all()),
            'number_of_days' => DailyRankingDayTypesResource::collection(DailyRankingDayTypes::all()),
            'details_columns' => LeaderboardDetailsColumnsResource::collection(LeaderboardDetailsColumns::all()),
            'data_types' => DataTypesResource::collection(DataTypes::all()),
            'sites' => ExternalSitesResource::collection(ExternalSites::all())
        ];
        
        Storage::disk('public')->put('attributes.json', json_encode($attributes));
    }
}
