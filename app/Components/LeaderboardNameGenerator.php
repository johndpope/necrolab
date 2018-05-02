<?php

namespace App\Components;

use Exception;
use DateTime;
use App\Characters;
use App\Releases;
use App\Modes;
use App\LeaderboardTypes;

class LeaderboardNameGenerator {
    protected $date;
    
    public function __construct(DateTime $date) {
        $this->date = $date;
    }

    protected function isValidName($release_name, $mode_name, $character_name, $leaderboard_type_name, $seeded, $is_co_op, $is_custom) {
        $is_valid = true;
        
        if($release_name == 'original' && $mode_name != 'normal') {
            $is_valid = false;
        }
        
        if($leaderboard_type_name == 'deathless' && !empty($seeded)) {
            $is_valid = false;
        }
        
        if($leaderboard_type_name == 'deathless' && $mode_name != 'normal') {
            $is_valid = false;
        }
        
        if($release_name == 'original' && !Characters::isOriginalCharacter($character_name)) {
            $is_valid = false;
        }
        
        if($release_name == 'amplified_dlc' && !Characters::isAmplifiedDlcCharacter($character_name)) {
            $is_valid = false;
        }        
        
        if($mode_name != 'normal' && !Characters::isModeCharacter($character_name)) {
            $is_valid = false;
        }
        
        if($leaderboard_type_name == 'deathless' && !Characters::isDeathlessCharacter($character_name)) {
            $is_valid = false;
        }
        
        if(!empty($seeded) && !Characters::isSeededCharacter($character_name)) {
            $is_valid = false;
        }
        
        if(!empty($is_co_op) && !Characters::isCoOpCharacter($character_name)) {
            $is_valid = false;
        }
        
        return $is_valid;
    }
    
    public function getName(Releases $release, Modes $mode, Characters $character, LeaderboardTypes $leaderboard_type, $seeded, $is_co_op, $is_custom) {
        $leaderboard_name = '';
        
        $release_name = $release->name;
        $mode_name = $mode->name;
        $character_name = $character->name;
        $leaderboard_type_name = $leaderboard_type->name;
                                        
        if($this->isValidName($release_name, $mode_name, $character_name, $leaderboard_type_name, $seeded, $is_co_op, $is_custom)) {
            $character_display_name = $character['display_name'];
            
            if($character_name == 'dove') {
                $character_display_name = strtoupper($character_display_name);
            }
        
            $leaderboard_name_segments = array();
            
            if($release_name == 'amplified_dlc') {
                $leaderboard_name_segments[] = 'DLC';
            }
        
            switch($leaderboard_type_name) {
                case 'score':
                case 'deathless':
                    $leaderboard_name_segments[] = 'HARDCORE';
                    
                    if(!empty($seeded)) {
                        $leaderboard_name_segments[] = 'SEEDED';
                    }
                    break;
                case 'speed':
                    if(!empty($seeded)) {
                        $leaderboard_name_segments[] = 'SEEDED';
                    }
                
                    $leaderboard_name_segments[] = 'SPEEDRUN';
                    break;
            }
            
            if($character_name != 'cadence') {
                $leaderboard_name_segments[] = $character_display_name;
            }
            
            if(!empty($is_co_op)) {
                $leaderboard_name_segments[] = 'CO-OP';
            }
            
            if($leaderboard_type_name == 'deathless') {
                $leaderboard_name_segments[] = 'DEATHLESS';
            }
            elseif($mode_name != 'normal') {
                $mode_segment = '';
            
                if($mode_name == 'no_return') {
                    $mode_segment = 'NO RETURN';
                }
                else {
                    $mode_segment = strtoupper($mode_name);
                }                
            
                $leaderboard_name_segments[] = $mode_segment;
            }
            
            if(!empty($is_custom)) {
                $leaderboard_name_segments[] = 'CUSTOM MUSIC';
            }
            
            $leaderboard_name = implode(' ', $leaderboard_name_segments) . '_PROD';
        }
        
        return $leaderboard_name;
    }
    
    public function getNonDailyNames() {
        $characters = Characters::where('is_active', 1)->get();
        
        $releases = Releases::getAllByDate($this->date);
        
        $modes = Modes::all();
        
        $leaderboard_types = LeaderboardTypes::where('name', '!=', 'daily')->get();
        
        $seeded_types = array(
            0, 
            1
        );
        
        $co_op_types = array(
            0,
            1
        );
        
        $custom_types = array(
            0,
            1
        );
        
        $leaderboard_names = array();

        if(!empty($releases)) {
            foreach($releases as $release) {
                if(!empty($modes)) {
                    foreach($modes as $mode) {
                        if(!empty($characters)) {
                            foreach($characters as $character) {
                                if(!empty($leaderboard_types)) {
                                    foreach($leaderboard_types as $leaderboard_type) {
                                        foreach($seeded_types as $seeded) {
                                            foreach($co_op_types as $is_co_op) {
                                                foreach($custom_types as $is_custom) {
                                                    $leaderboard_name = $this->getName($release, $mode, $character, $leaderboard_type, $seeded, $is_co_op, $is_custom);
                                                    
                                                    if(!empty($leaderboard_name)) {
                                                        $leaderboard_names[] = $leaderboard_name;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $leaderboard_names;
    }
    
    public function getDailyNames() {        
        $releases = Releases::getAllByDate($this->date);
        
        $leaderboard_names = array();

        if(!empty($releases)) {
            foreach($releases as $release) {
                $release_name = $release->name;
                
                $leaderboard_name = "{$this->date->format('j/n/Y')}_PROD";
            
                if($release_name == 'amplified_dlc') {
                    $leaderboard_name = "DLC {$leaderboard_name}";
                }
                
                $leaderboard_names[] = $leaderboard_name;
            }
        }
        
        return $leaderboard_names;
    }
}