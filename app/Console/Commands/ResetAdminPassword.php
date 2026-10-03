<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('admin:reset-password {password? : Password baru, akan ditanya jika tidak diisi}')]
#[Description('Reset password admin')]
class ResetAdminPassword extends Command
{
    public function handle(): int
    {
        $user = User::where('username', 'admin')->first();

        if (! $user) {
            $this->error('User admin tidak ditemukan.');
            return self::FAILURE;
        }

        $password = $this->argument('password')
            ?? $this->secret('Password baru');

        if (! $password || strlen($password) < 6) {
            $this->error('Password minimal 6 karakter.');
            return self::FAILURE;
        }

        $user->update(['password' => $password]);

        $this->info('Password admin berhasil direset.');

        return self::SUCCESS;
    }
}
