<?php

namespace App\Filament\Resources\Hadiths\Pages;

use App\Filament\Resources\Hadiths\HadithResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHadith extends EditRecord
{
    protected static string $resource = HadithResource::class;

    public function getTitle(): string
    {
        $record = $this->getRecord();
        
        $title = 'الحديث';
        
        if ($record->number_in_book) {
            $title .= ' رقم ' . $record->number_in_book;
        }
        
        if ($record->book) {
            $title .= ' - ' . $record->book->name;
        }
        
        return $title;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
