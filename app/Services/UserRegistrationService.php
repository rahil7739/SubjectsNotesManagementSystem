<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Notifications\RegistrationRequestNotification;

class UserRegistrationService
{
    public function createPendingUser(array $data): User
    {
        $user = new User($data);
        $user->role = RoleEnum::User;
        $user->is_approved = false;
        $user->save();

        User::where('role', RoleEnum::Admin)->get()->each(
            fn (User $admin) => $admin->notify(new RegistrationRequestNotification($user)),
        );

        return $user;
    }
}
