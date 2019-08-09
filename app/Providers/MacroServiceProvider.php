<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Query\Builder;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro("whereAnyValues", function (string $field_name, array $values) {
            $placeholders = array_fill(0, count($values), '?');

            $sql = 'ANY (VALUES (' . implode('), (', $placeholders) . '))';

            $this->whereRaw("{$field_name} = {$sql}", $values);

            return $this;
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
