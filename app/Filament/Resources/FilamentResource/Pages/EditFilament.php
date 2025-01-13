<?php

namespace App\Filament\Resources\FilamentResource\Pages;

use App\Filament\Resources\FilamentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFilament extends EditRecord
{
    protected static string $resource = FilamentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

//    protected function mutateFormDataBeforeSave(array $data): array
//    {
//        // Use dd() to inspect the update data
//        dd($data);
//
//        return $data;
//    }
}
