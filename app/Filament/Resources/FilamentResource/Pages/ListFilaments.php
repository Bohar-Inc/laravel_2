<?php

namespace App\Filament\Resources\FilamentResource\Pages;

use App\Filament\Resources\FilamentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFilaments extends ListRecords
{
    protected static string $resource = FilamentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
