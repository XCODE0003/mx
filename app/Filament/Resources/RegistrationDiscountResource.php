<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrationDiscountResource\Pages;
use App\Models\RegistrationDiscount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RegistrationDiscountResource extends Resource
{
    protected static ?string $model = RegistrationDiscount::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Скидки при регистрации';
    protected static ?string $modelLabel = 'Скидка';
    protected static ?string $pluralModelLabel = 'Скидки при регистрации';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Условия скидки')->schema([
                Forms\Components\TextInput::make('discount_percent')
                    ->label('Размер скидки (%)')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100)
                    ->suffix('%'),

                Forms\Components\DatePicker::make('registration_from')
                    ->label('Дата начала регистрации')
                    ->required()
                    ->native(false)
                    ->displayFormat('d.m.Y'),

                Forms\Components\DatePicker::make('registration_to')
                    ->label('Дата окончания регистрации')
                    ->required()
                    ->native(false)
                    ->displayFormat('d.m.Y')
                    ->afterOrEqual('registration_from'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Активна')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('discount_percent')
                    ->label('Скидка')
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('registration_from')
                    ->label('С')
                    ->date('d.m.Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('registration_to')
                    ->label('По')
                    ->date('d.m.Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активна')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRegistrationDiscounts::route('/'),
            'create' => Pages\CreateRegistrationDiscount::route('/create'),
            'edit'   => Pages\EditRegistrationDiscount::route('/{record}/edit'),
        ];
    }
}
