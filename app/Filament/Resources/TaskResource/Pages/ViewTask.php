<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Предпросмотр')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn (): string => route('tasks.view', $this->record->id))
                ->openUrlInNewTab(),
            Actions\Action::make('downloadZip')
                ->label('Скачать ZIP')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn (): string => route('admin.tasks.prepare-download', $this->record->id))
                ->openUrlInNewTab(),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
