<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create 
                            {--name=Admin : The admin name}
                            {--email=admin@admin.com : The admin email}
                            {--password= : The admin password}
                            {--force : Force creation even if user exists}';
    protected $description = 'Create an admin user';

    public function handle()
    {
        $name = $this->option('name') ?: $this->ask('What is the admin name?', 'Admin');
        $email = $this->option('email') ?: $this->ask('What is the admin email?', 'admin@admin.com');
        $password = $this->option('password') ?: $this->secret('What is the admin password?');

        if (empty($password)) {
            $this->error('Password cannot be empty!');
            return 1;
        }

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            if ($this->option('force')) {
                $existingUser->update([
                    'name' => $name,
                    'password' => Hash::make($password),
                ]);
                $this->info('Admin user updated successfully!');
                $this->info("Email: {$email}");
                return 0;
            } else {
                $this->error("User with email {$email} already exists. Use --force to update.");
                return 1;
            }
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $this->info('Admin user created successfully!');
        $this->info("Email: {$email}");
        return 0;
    }
}