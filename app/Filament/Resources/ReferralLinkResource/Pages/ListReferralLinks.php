<?php

namespace App\Filament\Resources\ReferralLinkResource\Pages;

use App\Filament\Resources\ReferralLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReferralLinks extends ListRecords
{
    protected static string $resource = ReferralLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
