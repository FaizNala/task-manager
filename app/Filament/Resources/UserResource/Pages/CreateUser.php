<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\NotificationSetting;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterSave(): void
    {
        parent::afterSave();

        $user = $this->record;

        NotificationSetting::create([
            'user_id' => $user->id,
            'email_notifications' => true,
            'reminder_before_deadline' => 24,
        ]);
    }
}
