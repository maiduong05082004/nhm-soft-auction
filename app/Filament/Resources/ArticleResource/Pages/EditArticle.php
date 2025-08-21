<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['status'] == 'published') {
            if (empty($this->record->publish_time)) {
                $data['publish_time'] = now();
            } else {
                $data['publish_time'] = $this->record->publish_time;
            }
        } else {
            $data['publish_time'] = null;
        }

        return $data;
    }
}
