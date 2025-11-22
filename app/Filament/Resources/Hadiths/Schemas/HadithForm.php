<?php

namespace App\Filament\Resources\Hadiths\Schemas;

use App\Models\Narrator;
use App\Models\Source;
use App\Services\HadithParser;
use Filament\Schemas\Components\ColorPicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Get;
use Filament\Schemas\Set;

class HadithForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                // Smart Parser Section
                Section::make('إدخال سريع - Smart Parser')
                    ->description('الصق النص الكامل للحديث هنا وسيتم تحليله تلقائياً')
                    ->schema([
                        Textarea::make('raw_text')
                            ->label('النص الخام')
                            ->rows(4)
                            ->placeholder('مثال: إنما الأعمال بالنيات [1] (صحيح) (ق) عن عمر بن الخطاب')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, Set $set, Get $get) {
                                if (empty($state)) {
                                    return;
                                }

                                $parser = new HadithParser();
                                $result = $parser->parse($state);

                                // Set parsed values
                                $set('content', $result['clean_text']);
                                $set('number_in_book', $result['number']);
                                $set('grade', $result['grade']);

                                // Handle narrator
                                if (!empty($result['narrator'])) {
                                    $narrator = Narrator::where('name', 'LIKE', '%' . $result['narrator'] . '%')->first();
                                    if ($narrator) {
                                        $set('narrator_id', $narrator->id);
                                    }
                                }

                                // Handle sources
                                if (!empty($result['sources'])) {
                                    $sourceIds = Source::whereIn('name', $result['sources'])->pluck('id')->toArray();
                                    $set('sources', $sourceIds);
                                }
                            })
                            ->helperText('سيتم تحليل النص تلقائياً عند الخروج من الحقل'),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                // Main Form Section
                Section::make('بيانات الحديث')
                    ->schema([
                        Textarea::make('content')
                            ->required()
                            ->rows(4)
                            ->label('نص الحديث'),
                        
                        TextInput::make('number_in_book')
                            ->numeric()
                            ->label('رقم الحديث في الكتاب'),
                        
                        TextInput::make('grade')
                            ->maxLength(255)
                            ->label('الدرجة')
                            ->placeholder('صحيح، حسن، ضعيف'),
                        
                        Select::make('book_id')
                            ->relationship('book', 'name')
                            ->searchable()
                            ->preload()
                            ->label('الكتاب'),
                        
                        Select::make('narrator_id')
                            ->relationship('narrator', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->label('اسم الراوي'),
                                TextInput::make('grade_status')
                                    ->label('حالة التوثيق'),
                                ColorPicker::make('color_code')
                                    ->default('#22c55e')
                                    ->label('رمز اللون'),
                            ])
                            ->label('الراوي'),
                        
                        Select::make('sources')
                            ->relationship('sources', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->label('المصادر'),
                        
                        Textarea::make('explanation')
                            ->rows(5)
                            ->nullable()
                            ->label('الشرح'),
                    ]),
            ]);
    }
}
