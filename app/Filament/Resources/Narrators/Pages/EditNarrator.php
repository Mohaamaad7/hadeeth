<?php

namespace App\Filament\Resources\Narrators\Pages;

use App\Filament\Resources\Narrators\NarratorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNarrator extends EditRecord
{
    protected static string $resource = NarratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
