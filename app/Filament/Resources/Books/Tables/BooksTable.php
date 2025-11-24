<?php

namespace App\Filament\Resources\Books\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('اسم الكتاب'),
                
                TextColumn::make('parent.name')
                    ->searchable()
                    ->sortable()
                    ->label('الكتاب الرئيسي')
                    ->placeholder('—')
                    ->badge()
                    ->color('success'),
                
                TextColumn::make('sort_order')
                    ->sortable()
                    ->label('الترتيب'),
                
                TextColumn::make('hadiths_count')
                    ->counts('hadiths')
                    ->label('عدد الأحاديث'),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->hidden(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }
}
