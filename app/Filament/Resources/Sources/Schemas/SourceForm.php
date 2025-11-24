<?php

namespace App\Filament\Resources\Sources\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('اسم المصدر'),
                TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(10)
                    ->label('الرمز')
                    ->helperText('مثال: خ، م، د، ت'),
                TextInput::make('type')
                    ->maxLength(255)
                    ->nullable()
                    ->label('النوع'),
            ]);
    }
}
