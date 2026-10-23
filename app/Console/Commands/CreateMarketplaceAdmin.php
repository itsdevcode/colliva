<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateMarketplaceAdmin extends Command
{
    protected $signature = 'shoutly:promote-admin {email : Email of an existing verified account}';

    protected $description = 'Grant administration access to an existing verified account';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        if (! $user || ! $user->hasVerifiedEmail()) {
            $this->error('Register the account and verify its email first.');

            return self::FAILURE;
        }
        DB::transaction(function () use ($user) {
            $user->forceFill(['role' => 'admin'])->save();
            AuditLog::create(['user_id' => $user->id, 'action' => 'user.admin_promoted_via_cli', 'subject_type' => User::class, 'subject_id' => $user->id]);
        });
        $this->info('Administration access granted.');

        return self::SUCCESS;
    }
}
