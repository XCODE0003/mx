<?php

namespace App\Filament\Resources\ReferralLinkResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $title = 'Покупки';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID заказа')
                    ->sortable(),
                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->copyable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->default('Гость'),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->default('—')
                    ->copyable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Предмет'),
                Tables\Columns\TextColumn::make('variant_count')
                    ->label('Кол-во вариантов')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата покупки')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                // Заказы создаются через систему
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => route('filament.admin.resources.orders.edit', $record)),
            ])
            ->bulkActions([
                //
            ]);
    }
}
