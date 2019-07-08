<?php

namespace App\Traits;

trait GeneratesNewInstance {
    public static function getNewInstance() {
        return new static();
    }
}
