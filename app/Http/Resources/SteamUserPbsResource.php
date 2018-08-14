<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SteamUserPbsResource extends JsonResource {
    /**
     * Transform a single release into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $record = [];

        if(!empty($this->steamid)) {
            $record['steamid'] = $this->steamid;
        }
        
        if(!empty($this->first_snapshot_date)) {
            $record['date'] = $this->first_snapshot_date;
        }
        
        if(!empty($this->first_rank)) {
            $record['rank'] = $this->first_rank;
        }

        $record['details'] = $this->details;
        $record['zone'] = $this->zone;
        $record['level'] = $this->level;
        $record['win'] = $this->is_win;
        $record['score'] = $this->score;
        
        switch($this->leaderboard_type) {
            case 'speed':
                $record['time'] = $this->time;
                break;
            case 'deathless':
                $record['win_count'] = $this->win_count;
                break;
        }
        
        $replay = [];
        
        if(!empty($this->ugcid) && !empty($this->downloaded) && $this->leaderboard_type != 'deathless') {
            $replay = [
                'ugcid' => $this->ugcid,
                'version' => $this->version,
                'seed' => $this->seed,
                'run_result' => $this->run_result
            ];
            
            $replay_url = '';
            
            if(!empty($this->uploaded_to_s3)) {
                $replay_url = env('AWS_URL') . "/replays/{$this->ugcid}.zip";
            }
            
            $replay['file_url'] = $replay_url;
        }
        
        $record['replay'] = $replay;
    
        return $record;
    }
}
