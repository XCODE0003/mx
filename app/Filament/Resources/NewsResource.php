<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'Новости';
    protected static ?string $modelLabel = 'Новость';
    protected static ?string $pluralModelLabel = 'Новости';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Основное')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Заголовок')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                        if ($operation === 'create' && $state) {
                            $set('slug', Str::slug($state));
                        }
                    }),

                Forms\Components\TextInput::make('slug')
                    ->label('URL (slug)')
                    ->required()
                    ->unique(News::class, 'slug', ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\Textarea::make('excerpt')
                    ->label('Краткое описание (превью)')
                    ->rows(3)
                    ->maxLength(500)
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Контент')->schema([
                Forms\Components\RichEditor::make('content')
                    ->label('Содержание')
                    ->required()
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'h2',
                        'h3',
                        'bulletList',
                        'orderedList',
                        'blockquote',
                        'codeBlock',
                        'link',
                        'attachFiles',
                    ])
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('news/attachments')
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Изображения')->schema([
                Forms\Components\FileUpload::make('thumbnail')
                    ->label('Превью (список новостей)')
                    ->image()
                    ->disk('public')
                    ->directory('news/thumbnails')
                    ->imagePreviewHeight('150')
                    ->maxSize(2048)
                    ->helperText('Отображается на странице со списком новостей'),

                Forms\Components\FileUpload::make('cover_image')
                    ->label('Обложка (страница новости)')
                    ->image()
                    ->disk('public')
                    ->directory('news/covers')
                    ->imagePreviewHeight('150')
                    ->maxSize(4096)
                    ->helperText('Отображается в шапке страницы новости'),
            ])->columns(2),

            Forms\Components\Section::make('Публикация')->schema([
                Forms\Components\Toggle::make('is_published')
                    ->label('Опубликовано')
                    ->default(false),

                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Дата публикации')
                    ->native(false)
                    ->default(now()),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('')
                    ->disk('public')
                    ->width(60)
                    ->height(40),

                Tables\Columns\TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->sortable()
                    ->limit(60),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Опубликовано')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Дата публикации')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Статус публикации'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit'   => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
