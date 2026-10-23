<?php

namespace App\Filament\Resources\Collaborations\Pages;

use App\Filament\Resources\Collaborations\CollaborationResource;
use Filament\Resources\Pages\ManageRecords;

class ManageCollaborations extends ManageRecords
{
    protected static string $resource = CollaborationResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
