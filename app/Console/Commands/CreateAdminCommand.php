<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    protected $signature = 'sam:create-admin';

    protected $description = 'Buat satu akun admin untuk panel SAM';

    public function handle(): int
    {
        $this->info('=== Pembuatan Akun Admin SAM ===');
        $this->newLine();

        // Nama
        $name = $this->ask('Nama lengkap admin');
        $nameValidator = Validator::make(['name' => $name], ['name' => 'required|string|min:2|max:255']);
        if ($nameValidator->fails()) {
            $this->error($nameValidator->errors()->first('name'));

            return self::FAILURE;
        }

        // Email
        $email = $this->ask('Alamat email');
        $emailValidator = Validator::make(['email' => $email], ['email' => 'required|email|unique:users,email']);
        if ($emailValidator->fails()) {
            $this->error($emailValidator->errors()->first('email'));

            return self::FAILURE;
        }

        // Password
        $password = $this->secret('Password (min. 12 karakter)');
        $passwordValidator = Validator::make(
            ['password' => $password],
            ['password' => 'required|string|min:12']
        );
        if ($passwordValidator->fails()) {
            $this->error($passwordValidator->errors()->first('password'));

            return self::FAILURE;
        }

        $passwordConfirm = $this->secret('Konfirmasi password');
        if ($password !== $passwordConfirm) {
            $this->error('Password dan konfirmasi tidak cocok.');

            return self::FAILURE;
        }

        // Konfirmasi
        if (! $this->confirm("Buat admin dengan email [{$email}]?", true)) {
            $this->warn('Dibatalkan.');

            return self::SUCCESS;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->newLine();
        $this->info("✓ Akun admin berhasil dibuat (ID: {$user->id}).");
        $this->line("  Nama  : {$user->name}");
        $this->line("  Email : {$user->email}");
        $this->newLine();
        $this->warn('Simpan kredensial ini di tempat aman. Password tidak dapat dipulihkan dari sini.');

        return self::SUCCESS;
    }
}
