<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('اسم الكتاب'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->label('ترتيب العرض'),
            ]);
    }
}
