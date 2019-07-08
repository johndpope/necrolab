<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\DB;

trait CanBeVacuumed {
    public function vacuum(array $options = []) {
        $supported_options = [
            'analyze' => 'ANALYZE',
            'full' => 'FULL'
        ];

        $parsed_options = [];

        foreach($options as $option) {
            if(!isset($supported_options[$option])) {
                throw new Exception("Vacuum option '{$option}' is not supported. Supported options are 'analyze' and 'full'.");
            }

            $parsed_options[] = $supported_options[$option];
        }

        $parsed_options_sql = implode(', ', $parsed_options);

        var_dump("VACUUM ({$parsed_options_sql}) {$this->getTable()}");

        DB::statement("
            VACUUM ({$parsed_options_sql}) {$this->getTable()};
        ");
    }
}