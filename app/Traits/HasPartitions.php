<?php

namespace App\Traits;

use DateTime;
use DateInterval;
use App\LeaderboardSources;
use App\Dates;

trait HasPartitions {
    protected static $base_table_name = [];

    public static function loadBaseTableName(LeaderboardSources $leaderboard_source) {
        if(!isset(static::$base_table_name[$leaderboard_source->name])) {
            $instance = new static();

            $instance->setSchema($leaderboard_source->name);

            static::$base_table_name[$leaderboard_source->name] = $instance->getTable();
        }
    }

    public static function getBaseTableName(LeaderboardSources $leaderboard_source) {
        static::loadBaseTableName($leaderboard_source);

        return static::$base_table_name[$leaderboard_source->name];
    }

    public static function getTableName(LeaderboardSources $leaderboard_source, DateTime $date) {
        $base_name = static::getBaseTableName($leaderboard_source);

        return "{$base_name}_{$date->format('Y_m')}";
    }

    public static function getTableNames(LeaderboardSources $leaderboard_source, DateTime $start_date, DateTime $end_date) {
        $table_names = [];

        $current_date = new DateTime($start_date->format('Y-m-01'));

        while($current_date <= $end_date) {
            $table_names[] = static::getTableName($leaderboard_source, $current_date);

            $current_date->add(new DateInterval('P1M'));
        }

        return $table_names;
    }

    abstract public static function clear(LeaderboardSources $leaderboard_source, Dates $date);

    /**
     * Set the date partition that this model belongs to.
     *
     * @param DateTime $partition_date The date partition that this model belongs to.
     * @return $this The current instance of the model.
     */
    public function setPartitionDate(DateTime $partition_date) {
        $table_name = "{$this->getTable()}_{$partition_date->format('Y_m')}";

        $this->setTable($table_name);

        return $this;
    }
}
