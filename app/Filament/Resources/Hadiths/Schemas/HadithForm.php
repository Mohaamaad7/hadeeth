<?php

namespace App\Filament\Resources\Hadiths\Schemas;

use App\Models\Narrator;
use App\Models\Source;
use App\Services\HadithParser;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

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
                        
                        // Hierarchical Book Selection
                        Select::make('main_book_id')
                            ->label('الكتاب (القسم الرئيسي)')
                            ->options(fn() => \App\Models\Book::mainBooks()->pluck('name', 'id'))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (?string $state, Set $set) {
                                if (!$state) {
                                    $set('book_id', null);
                                    return;
                                }
                                
                                // Check if this book has children
                                $hasChildren = \App\Models\Book::where('parent_id', $state)->exists();
                                
                                if ($hasChildren) {
                                    // Has children: Clear book_id so user must select a child
                                    $set('book_id', null);
                                } else {
                                    // Leaf node: Auto-select the main book itself
                                    $set('book_id', $state);
                                }
                            })
                            ->helperText('اختر الكتاب الرئيسي أولاً لعرض الأبواب الفرعية'),
                        
                        Select::make('book_id')
                            ->label('الباب (القسم الفرعي)')
                            ->options(function (Get $get) {
                                $mainBookId = $get('main_book_id');
                                
                                if (!$mainBookId) {
                                    return [];
                                }
                                
                                // Fetch children
                                $children = \App\Models\Book::where('parent_id', $mainBookId)
                                    ->pluck('name', 'id');
                                
                                // If children exist, return them
                                if ($children->isNotEmpty()) {
                                    return $children;
                                }
                                
                                // No children: Return the main book itself as a leaf node
                                $mainBook = \App\Models\Book::find($mainBookId);
                                if ($mainBook) {
                                    return [$mainBook->id => $mainBook->name . ' (تصنيف مباشر)'];
                                }
                                
                                return [];
                            })
                            ->searchable()
                            ->disabled(fn(Get $get) => !$get('main_book_id'))
                            ->helperText('اختر الباب الفرعي من الكتاب المحدد')
                            ->required(),
                        
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
