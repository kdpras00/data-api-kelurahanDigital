<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AssignRoleToExistingUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-role-to-existing-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign head-of-family role to users without roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $usersWithoutRole = User::doesntHave('roles')->get();

        if ($usersWithoutRole->isEmpty()) {
            $this->info('No users found without roles.');
            return 0;
        }

        $this->info("Found {$usersWithoutRole->count()} users without roles.");
        $bar = $this->output->createProgressBar($usersWithoutRole->count());
        $bar->start();

        foreach ($usersWithoutRole as $user) {
            $user->assignRole('head-of-family');
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully assigned 'head-of-family' role to {$usersWithoutRole->count()} users.");

        return 0;
    }
}
