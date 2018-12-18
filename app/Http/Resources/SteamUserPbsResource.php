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
        
        if(!empty($this->lbid)) {
            $record['lbid'] = $this->lbid;
        }
        
        if(!empty($this->character_name)) {
            $record['character'] = $this->character_name;
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
        
        $details_column = $this->details_column;
        
        $details_column_value = $this->$details_column;
        
        if($this->details_column_data_type == 'seconds') {
            $details_column_value = (float)$details_column_value;
        }
        else {
            $details_column_value = (int)$details_column_value;
        }
        
        $record[$this->details_column] = $details_column_value;
        
        $replay = [];
        
        if(!empty($this->ugcid) && !empty($this->downloaded)) {
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
