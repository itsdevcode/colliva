<?php

namespace App\Filament\Resources\ContentReports\Pages;

use App\Filament\Resources\ContentReports\ContentReportResource;
use Filament\Resources\Pages\ManageRecords;

class ManageContentReports extends ManageRecords
{
    protected static string $resource = ContentReportResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
