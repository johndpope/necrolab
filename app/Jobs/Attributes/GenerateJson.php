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
use App\LeaderboardDetailsColumns;
use App\DataTypes;
use App\Http\Resources\LeaderboardSourcesResource;
use App\Http\Resources\LeaderboardTypesResource;
use App\Http\Resources\CharactersResource;
use App\Http\Resources\ReleasesResource;
use App\Http\Resources\ModesResource;
use App\Http\Resources\SeededTypesResource;
use App\Http\Resources\MultiplayerTypesResource;
use App\Http\Resources\SoundtracksResource;
use App\Http\Resources\LeaderboardDetailsColumnsResource;
use App\Http\Resources\DataTypesResource;

class GenerateJson implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

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
            'details_columns' => LeaderboardDetailsColumnsResource::collection(LeaderboardDetailsColumns::all()),
            'soundtracks' => DataTypesResource::collection(DataTypes::all())
        ];
        
        Storage::disk('public')->put('attributes.json', json_encode($attributes));
    }
}
