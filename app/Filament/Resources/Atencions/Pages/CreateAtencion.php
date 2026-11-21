<?php

namespace App\Filament\Resources\Atencions\Pages;

use App\Filament\Resources\Atencions\AtencionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAtencion extends CreateRecord
{
    protected static string $resource = AtencionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['estado'] = 'en_espera';
        $data['user_admision_id'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Crear'),

            $this->getCancelFormAction()
                ->label('Cancelar'),
        ];
    }
}
