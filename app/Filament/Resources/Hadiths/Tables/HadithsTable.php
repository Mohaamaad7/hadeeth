<?php

namespace App\Filament\Resources\Hadiths\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HadithsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number_in_book')
                    ->sortable()
                    ->label('الرقم'),
                
                TextColumn::make('content')
                    ->limit(50)
                    ->searchable()
                    ->label('نص الحديث'),
                
                TextColumn::make('grade')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'صحيح' => 'success',
                        'حسن' => 'info',
                        'ضعيف' => 'warning',
                        default => 'gray',
                    })
                    ->label('الدرجة'),
                
                TextColumn::make('book.name')
                    ->sortable()
                    ->searchable()
                    ->label('الكتاب'),
                
                TextColumn::make('narrator.name')
                    ->sortable()
                    ->searchable()
                    ->label('الراوي'),
                
                TextColumn::make('sources.code')
                    ->badge()
                    ->separator(',')
                    ->label('المصادر'),
                
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
            ->defaultSort('number_in_book');
    }
}
