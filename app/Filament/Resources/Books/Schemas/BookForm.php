<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                
                Select::make('parent_id')
                    ->label('الكتاب الرئيسي (اختياري)')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('اترك فارغاً لإنشاء كتاب رئيسي (Kitab)، أو اختر كتاباً لإنشاء باب فرعي (Bab)'),
                
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->label('ترتيب العرض'),
            ]);
    }
}
