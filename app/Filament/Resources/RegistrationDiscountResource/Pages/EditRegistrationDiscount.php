<?php

namespace App\Filament\Resources\RegistrationDiscountResource\Pages;

use App\Filament\Resources\RegistrationDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegistrationDiscount extends EditRecord
{
    protected static string $resource = RegistrationDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
