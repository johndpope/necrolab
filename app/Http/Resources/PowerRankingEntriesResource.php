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
        $total_points = 0;
        $categories = [];
        $characters = [];
        
        
        /* ---------- Compile categories ---------- */
        
        if(!empty($this->category_ranks)) {
            foreach($this->category_ranks as $category_name => $category_rank) {
                $categories[$category_name] = [
                    'name' => $category_name,
                    'rank' => (int)$category_rank,
                    'points' => 0
                ];
            }
        }
        
        
        /* ---------- Compile characters ---------- */
        
        $character_rankings = $this->characters;
    
        if(!empty($character_rankings)) {
            foreach($character_rankings as $character_name => $character_ranking) {
                $characters[$character_name]['name'] = $character_name;
                $characters[$character_name]['rank'] = $character_ranking['rank'];
                $characters[$character_name]['points'] = 0;
                
                $character_categories = $character_ranking['categories'];
                
                if(!empty($character_categories)) {
                    foreach($character_categories as $category_name => $character_category) {
                        $characters[$character_name]['categories'][$category_name]['rank'] = $character_category['rank'];
                    
                        $points = RankPoints::calculateFromRank($character_category['rank']);
                        
                        $characters[$character_name]['categories'][$category_name]['points'] = $points;
                    
                        // Summarize points
                        $characters[$character_name]['points'] += $points;
                        $total_points += $points;
                        $categories[$category_name]['points'] += $points;
                        
                        
                        $character_category_details = $character_category['details'];
                        
                        if(!empty($character_category_details)) {
                            foreach($character_category_details as $details_name => $details_value) {
                                if(is_float($details_value + 0)) {
                                    $details_value = (float)$details_value;
                                }
                                else {
                                    $details_value = (int)$details_value;
                                }
                            
                                // Summarize category details
                                if(!isset($categories[$category_name]['details'][$details_name])) {
                                    $categories[$category_name]['details'][$details_name] = 0;
                                }
                                
                                $categories[$category_name]['details'][$details_name] += $details_value;
                            
                                // Summarize character category details
                                if(!isset($characters[$character_name]['categories'][$category_name]['details'][$details_name])) {
                                    $characters[$character_name]['categories'][$category_name]['details'][$details_name] = 0;
                                }
                                
                                $characters[$character_name]['categories'][$category_name]['details'][$details_name] += $details_value;
                            }
                        }
                    }
                }
            }
        }    
        

        /* ---------- Compile response record ---------- */
        
        $record = [];
        
        // If this record is in a player context then only show its date. Otherwise show player data.
        if(!empty($this->date)) {
            $record['date'] = $this->date;
        }
        else {
            $record['player'] = new PlayersResource($this->resource);
        }        
        
        $record['rank'] = $this->rank;
        $record['points'] = $total_points;
        $record['characters'] = $characters;
        $record['categories'] = $categories;
        
        return $record;
    }
}
