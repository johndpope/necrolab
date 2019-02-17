<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlayerPbsResource extends JsonResource {
    /**
     * Transform a single release into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $record = [];
        
        if(!empty($this->leaderboard_id)) {
            $record['leaderboard_id'] = $this->leaderboard_id;
        }
        
        if(!empty($this->character_name)) {
            $record['character'] = $this->character_name;
        }
        
        if(!empty($this->leaderboard_type_name)) {
            $record['leaderboard_type'] = $this->leaderboard_type_name;
        }
        
        if(!empty($this->first_snapshot_date)) {
            $record['date'] = $this->first_snapshot_date;
        }
        
        if(!empty($this->first_rank)) {
            $record['rank'] = $this->first_rank;
        }
        
        $details_decoded = json_decode($this->details, true);
        $details = [];
        
        if(!empty($details_decoded)) {
            foreach($details_decoded as $details_name => $details_value) {
                if(is_float($details_value + 0)) {
                    $details[$details_name] = (float)$details_value;
                }
                else {
                    $details[$details_name] = (int)$details_value;
                }
            }
        }
        
        $record['details'] = $details;
        
        if(!empty($this->show_zone_level)) {
            $record['zone'] = $this->zone;
            $record['level'] = $this->level;
            $record['win'] = $this->is_win;
            $record['run_result'] = $this->run_result;
        }
        
        if(!empty($this->show_seed)) {
            $record['seed'] = $this->seed;
        }
        
        $replay = [];
        
        if(!empty($this->show_replay) && !empty($this->ugcid) && !empty($this->downloaded)) {
            $replay = [
                'ugcid' => $this->ugcid,
                'version' => $this->version
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
