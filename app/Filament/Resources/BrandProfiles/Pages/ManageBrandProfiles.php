<?php

namespace App\Filament\Resources\BrandProfiles\Pages;

use App\Filament\Resources\BrandProfiles\BrandProfileResource;
use Filament\Resources\Pages\ManageRecords;

class ManageBrandProfiles extends ManageRecords
{
    protected static string $resource = BrandProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
