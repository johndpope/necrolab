<?php

namespace App\Traits;

use DateTime;
use DateInterval;
use App\Components\CallbackHandler;
use App\Components\DateIncrementor;
use App\LeaderboardSources;

trait HasPartitions {
    protected static $base_table_name;
    
    public static function loadBaseTableName() {
        if(!isset(static::$base_table_name)) {
            static::$base_table_name = (new static())->getTable();
        }
    }
    
    public static function getBaseTableName() {
        static::loadBaseTableName();
        
        return static::$base_table_name;
    }

    public static function getTableName(DateTime $date) {
        $base_name = static::getBaseTableName();
    
        return "{$base_name}_{$date->format('Y_m')}";
    }

    public static function getTableNames(DateTime $start_date, DateTime $end_date) {
        $table_names = [];
        
        $current_date = new DateTime($start_date->format('Y-m-01'));
        
        while($current_date <= $end_date) {
            $table_names[] = static::getTableName($current_date);
        
            $current_date->add(new DateInterval('P1M'));
        }
        
        return $table_names;
    }
    
    public static function dispatchPartitionCreationJob(string $job_class, $leaderboard_source_name, DateTime $date) {
        $leaderboard_sources = [];
        
        if(!empty($leaderboard_source_name)) {
            $leaderboard_source = LeaderboardSources::where('name', $leaderboard_source_name)->first();
            
            if(!empty($leaderboard_source)) {
                $leaderboard_sources[] = $leaderboard_source;
            }
        }
        else {
            $leaderboard_sources = LeaderboardSources::where('enabled', 1)->get();
        }
        
        foreach($leaderboard_sources as $leaderboard_source) {
           $job_class::dispatch(
                $leaderboard_source,
                $date
            )->onConnection('sync');
        }
    }
    
    public static function dispatchRangePartitionCreationJob(string $job_class, $leaderboard_source_name, DateTime $start_date, DateTime $end_date) {
        /* ---------- Retrieve the leaderboard sources that will have partitions added to it ---------- */
    
        $leaderboard_sources = [];
    
        if(!empty($leaderboard_source_name)) {
            $leaderboard_source = LeaderboardSources::where('name', $leaderboard_source_name)->first();
            
            if(!empty($leaderboard_source)) {
                $leaderboard_sources[] = $leaderboard_source;
            }
        }
        else {
            $leaderboard_sources = LeaderboardSources::where('enabled', 1)->get();
        }
        
        
        /* ---------- Setup CallbackHandler ---------- */
    
        $callback_handler = new CallbackHandler();
        
        $callback_handler->setCallback(function(DateTime $date, $leaderboard_sources, $job_class) {
            foreach($leaderboard_sources as $leaderboard_source) {
                $job_class::dispatch(
                    $leaderboard_source,
                    $date
                )->onConnection('sync');
            }
        });
        
        $callback_handler->addArgument($leaderboard_sources);
        $callback_handler->addArgument($job_class);
    
        
        /* ---------- Setup and run DateIncrementor ---------- */
    
        $date_incrementor = new DateIncrementor(
            $start_date, 
            $end_date, 
            new DateInterval('P1M')
        );
        
        $date_incrementor->run($callback_handler);
    }
}
