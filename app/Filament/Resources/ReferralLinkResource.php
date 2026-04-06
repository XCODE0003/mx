<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferralLinkResource\Pages;
use App\Filament\Resources\ReferralLinkResource\RelationManagers;
use App\Models\ReferralLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReferralLinkResource extends Resource
{
    protected static ?string $model = ReferralLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $modelLabel = 'Реферальная ссылка';

    protected static ?string $pluralModelLabel = 'Реферальные ссылки';

    protected static ?string $navigationLabel = 'Реферальные ссылки';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Название партнера')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('Код ссылки')
                            ->helperText('Оставьте пустым для автоматической генерации')
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->default(fn () => \Illuminate\Support\Str::random(10)),
                        Forms\Components\Textarea::make('description')
                            ->label('Описание')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Настройки')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Активна')
                            ->default(true),
                    ])->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Код')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Код скопирован!')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('url')
                    ->label('Реферальная ссылка')
                    ->copyable()
                    ->copyMessage('Ссылка скопирована!')
                    ->formatStateUsing(fn ($state) => $state)
                    ->url(fn ($record) => $record->url)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-link')
                    ->iconPosition('after')
                    ->weight('medium')
                    ->color('success'),
                Tables\Columns\TextColumn::make('clicks')
                    ->label('Клики')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Регистрации')
                    ->counts('users')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Покупки')
                    ->counts('orders')
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активна')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Статус')
                    ->placeholder('Все')
                    ->trueLabel('Активные')
                    ->falseLabel('Неактивные'),
            ])
            ->actions([
                Tables\Actions\Action::make('copyUrl')
                    ->label('Копировать ссылку')
                    ->icon('heroicon-o-clipboard')
                    ->action(function ($record) {
                        // Действие выполняется на фронтенде
                    })
                    ->url(fn ($record) => $record->url)
                    ->openUrlInNewTab(false),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\VisitsRelationManager::class,
            RelationManagers\UsersRelationManager::class,
            RelationManagers\OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReferralLinks::route('/'),
            'create' => Pages\CreateReferralLink::route('/create'),
            'edit' => Pages\EditReferralLink::route('/{record}/edit'),
        ];
    }
}
