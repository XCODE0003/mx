<?php

namespace App\Filament\Resources\ReferralLinkResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits';

    protected static ?string $title = 'История переходов';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->default('Гость'),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->default('—'),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP адрес'),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->user_agent),
                Tables\Columns\TextColumn::make('visited_at')
                    ->label('Дата визита')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('visited_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                // Визиты создаются автоматически
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
