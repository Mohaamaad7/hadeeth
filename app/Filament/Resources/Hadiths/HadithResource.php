<?php

namespace App\Filament\Resources\Hadiths;

use App\Filament\Resources\Hadiths\Pages\CreateHadith;
use App\Filament\Resources\Hadiths\Pages\EditHadith;
use App\Filament\Resources\Hadiths\Pages\ListHadiths;
use App\Filament\Resources\Hadiths\Schemas\HadithForm;
use App\Filament\Resources\Hadiths\Tables\HadithsTable;
use App\Models\Hadith;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HadithResource extends Resource
{
    protected static ?string $model = Hadith::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'الأحاديث';

    protected static ?string $modelLabel = 'حديث';

    protected static ?string $pluralModelLabel = 'الأحاديث';

    public static function getRecordTitle(?\Illuminate\Database\Eloquent\Model $record): ?string
    {
        if (!$record) {
            return null;
        }

        $title = 'الحديث';
        
        if ($record->number_in_book) {
            $title .= ' رقم ' . $record->number_in_book;
        }
        
        if ($record->book) {
            $title .= ' - ' . $record->book->name;
        }
        
        return $title;
    }

    public static function form(Schema $schema): Schema
    {
        return HadithForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HadithsTable::configure($table);
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
            'index' => ListHadiths::route('/'),
            'create' => CreateHadith::route('/create'),
            'edit' => EditHadith::route('/{record}/edit'),
        ];
    }
}
