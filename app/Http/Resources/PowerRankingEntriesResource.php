<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\RankPoints;

class PowerRankingEntriesResource extends JsonResource {
    /**
     * Transform a single power ranking entry into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {        
        $score_total = 0;
        $speed_total_time = 0;
        $deathless_total_win_count = 0;
        
        $total_points = 0;
        $total_score_points = 0;
        $total_speed_points = 0;
        $total_deathless_points = 0;
        
        $character_rankings = $this->characters;

        foreach($character_rankings as $character_name => &$character_ranking) {
            $character_ranking['points'] = 0;
            $character_ranking['name'] = $character_name;
            
            foreach($character_ranking as $category_name => &$category_data) {
                // Only process data in categories which are always arrays
                if(is_array($category_data)) {
                    /* ---------- Generate and summarize points ---------- */
                    
                    $points = RankPoints::calculateFromRank($category_data['rank']);
                    
                    $category_data['points'] = $points;
                    $character_ranking['points'] += $points;
                    $total_points += $points;
                    
                    /* ---------- Summarize category points and metrics ---------- */
                    
                    switch($category_name) {
                        case 'score':
                            $total_score_points += $points;
                            
                            $score_total += $category_data['score'];
                            break;
                        case 'speed':
                            $total_speed_points += $points;
                            
                            $speed_total_time += $category_data['time'];
                            break;
                        case 'deathless':
                            $total_deathless_points += $points;
                            
                            $deathless_total_win_count += $category_data['win_count'];
                            break;
                    }
                }
            }
        }
        
        
        /* ---------- Compile score rankings if present ---------- */
        
        $score_rankings = [];
        
        if(!empty($this->score_rank)) {
            $score_rankings = [
                'rank' => (int)$this->score_rank,
                'points' => $total_score_points,
                'score' => $score_total
            ];
        }

        
        /* ---------- Compile speed rankings if present ---------- */
        
        $speed_rankings = [];
        
        if(!empty($this->speed_rank)) {
            $speed_rankings = [
                'rank' => (int)$this->speed_rank,
                'points' => $total_speed_points,
                'time' => $speed_total_time
            ];
        }
        
        
        /* ---------- Compile deathless rankings if present ---------- */
        
        $deathless_rankings = [];
        
        if(!empty($this->deathless_rank)) {
            $deathless_rankings = [
                'rank' => (int)$this->deathless_rank,
                'points' => $total_deathless_points,
                'win_count' => $deathless_total_win_count,
            ];
        }
        
        
        return [
            'steamid' => $this->steamid,
            'characters' => $character_rankings,
            'score' => $score_rankings,
            'speed' => $speed_rankings,
            'deathless' => $deathless_rankings,
            'rank' => (int)$this->rank,
            'points' => (float)$total_points
        ];
    }
}