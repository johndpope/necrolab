<?php
namespace App\Components\Dataset\Indexes;

use App\Components\Dataset\Traits\HasIndexName;
use App\Components\Dataset\Traits\HasIndexSubName;
use App\Components\Dataset\Traits\HasSearchTerm;
use App\Components\Dataset\Traits\HasPage;
use App\Components\Dataset\Traits\HasLimit;
use App\Components\Dataset\Traits\HasExternalSiteId;
use App\Components\Dataset\Traits\CalculatesOffsets;

abstract class Index {
    use HasIndexName, HasIndexSubName, HasExternalSiteId, HasSearchTerm, HasPage, HasLimit, CalculatesOffsets;
}
