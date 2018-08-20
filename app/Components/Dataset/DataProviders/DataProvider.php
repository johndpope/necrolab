<?php
namespace App\Components\Dataset\DataProviders;

use App\Components\Dataset\Traits\HasPage;
use App\Components\Dataset\Traits\HasLimit;
use App\Components\Dataset\Traits\CalculatesOffsets;

abstract class DataProvider {
    use HasPage, HasLimit, CalculatesOffsets;
}
