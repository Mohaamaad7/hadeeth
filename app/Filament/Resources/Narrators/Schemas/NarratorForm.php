<?php

namespace App\Filament\Resources\Narrators\Schemas;

use Filament\Schemas\Components\ColorPicker;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Schema;

class NarratorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('اسم الراوي'),
                Textarea::make('bio')
                    ->rows(3)
                    ->nullable()
                    ->label('السيرة الذاتية'),
                TextInput::make('grade_status')
                    ->maxLength(255)
                    ->nullable()
                    ->label('حالة التوثيق')
                    ->helperText('مثال: ثقة، صدوق، ضعيف'),
                ColorPicker::make('color_code')
                    ->default('#22c55e')
                    ->label('رمز اللون'),
            ]);
    }
}
