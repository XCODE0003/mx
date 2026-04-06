<?php

namespace App\Filament\Resources\ReferralLinkResource\Widgets;

use Filament\Widgets\Widget;

class ReferralLinkUrlWidget extends Widget
{
    protected static string $view = 'filament.resources.referral-link-resource.widgets.referral-link-url-widget';

    public $record;

    protected int | string | array $columnSpan = 'full';

    public function mount($record): void
    {
        $this->record = $record;
    }
}
