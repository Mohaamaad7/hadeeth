<?php

namespace App\Filament\Resources\Narrators\Pages;

use App\Filament\Resources\Narrators\NarratorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNarrators extends ListRecords
{
    protected static string $resource = NarratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
