<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Vendor;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        // $data['roles'] contains the selected role IDs
        // dd($this->data);
        if ($this->data['roles'] && strtolower(Role::find($this->data['roles'])?->name) == 'vendor') {
            Vendor::updateOrCreate([
                'user_id' => $this->record->id,
            ], [
                'user_id' => $this->record->id,
                'store_name' => $this->data['name'],
                'status' => 'draft'
            ]); // Create a new vendor

        }
    }
}
