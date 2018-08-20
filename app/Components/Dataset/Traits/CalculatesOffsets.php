<?php

namespace App\Components\Dataset\Traits;

trait CalculatesOffsets {
    public static function getCalculatedOffset(int $page, int $limit) {
        return ($page - 1) * $limit;
    }
}
