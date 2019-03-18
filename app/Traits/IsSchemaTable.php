<?php

namespace App\Traits;

use Exception;
use App\LeaderboardSources;

trait IsSchemaTable {    
    protected static $schema_table_names = [];
    
    /**
     * The schema associated with the model.
     *
     * @var string
     */
    protected $schema;
    
    public static function loadSchemaTableName(LeaderboardSources $leaderboard_source): void {
        if(!isset(static::$schema_table_names[$leaderboard_source->name])) {
            $instance = new static();
            
            $instance->setSchema($leaderboard_source->name);
        
            static::$schema_table_names[$leaderboard_source->name] = $instance->getTable();
        }
    }
    
    public static function getSchemaTableName(LeaderboardSources $leaderboard_source): string {
        static::loadSchemaTableName($leaderboard_source);
        
        return static::$schema_table_names[$leaderboard_source->name];
    }
    
    /**
     * Set the schema that this model belongs to.
     *
     * @param string $schema The name of the schema this model belongs to.
     * @return $this The current instance of the model.
     */
    public function setSchema(string $schema) {
        $this->schema = $schema;
        
        return $this;
    }
    
    /**
     * Set the schema that this model belongs to in a static context.
     *
     * @param string $schema The name of the schema this model belongs to.
     * @return object The instance of the model that was created.
     */
    public static function setSchemaStatic(string $schema) {
        $instance = new static();
        
        $instance->setSchema($schema);
        
        return $instance;
    }
    
    /**
     * Returns the schema set for this model.
     *
     * @return string
     */
    public function getSchema(): string {
        return $this->schema;
    }
    
    /**
     * Get the table associated with the model.
     *
     * @throws Exception If schema has not been specified.
     * @return string
     */
    public function getTable(): string {
        if(empty($this->schema)) {
            throw new Exception("A schema is required to be set for this model. Use setSchema() to set it.");
        }
    
        $table = parent::getTable();
        
        return "{$this->schema}.{$table}";
    }
}
