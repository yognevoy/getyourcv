<?php

namespace App\Actions\Auth;

use App\Actions\Resume\ForceDeleteResume;
use App\Models\User;

class DeleteUserAccount
{
    public function __construct(private readonly ForceDeleteResume $forceDeleteResume) {}

    /**
     * Permanently erases the user's resumes and the account itself.
     */
    public function execute(User $user): void
    {
        $user->resumes()->withTrashed()->get()->each(
            fn ($resume) => $this->forceDeleteResume->execute($resume),
        );

        $user->delete();
    }
}
