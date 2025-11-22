<?php

namespace App\Filament\Resources\Narrators;

use App\Filament\Resources\Narrators\Pages\CreateNarrator;
use App\Filament\Resources\Narrators\Pages\EditNarrator;
use App\Filament\Resources\Narrators\Pages\ListNarrators;
use App\Filament\Resources\Narrators\Schemas\NarratorForm;
use App\Filament\Resources\Narrators\Tables\NarratorsTable;
use App\Models\Narrator;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NarratorResource extends Resource
{
    protected static ?string $model = Narrator::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'الرواة';

    protected static ?string $modelLabel = 'راوي';

    protected static ?string $pluralModelLabel = 'الرواة';

    public static function form(Schema $schema): Schema
    {
        return NarratorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NarratorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNarrators::route('/'),
            'create' => CreateNarrator::route('/create'),
            'edit' => EditNarrator::route('/{record}/edit'),
        ];
    }
}
