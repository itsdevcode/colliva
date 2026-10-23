<?php

namespace App\Filament\Resources\CreatorProfiles\Pages;

use App\Filament\Resources\CreatorProfiles\CreatorProfileResource;
use Filament\Resources\Pages\ManageRecords;

class ManageCreatorProfiles extends ManageRecords
{
    protected static string $resource = CreatorProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
