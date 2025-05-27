<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class SetupDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:setup {--force : Force the operation to run even in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up demo data for quickly trying out the application';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Check if we're in production and the --force flag wasn't used
        if (app()->environment('production') && ! $this->option('force')) {
            $this->error('This command cannot be run in production without the --force flag.');
            $this->warn('Running this command in production may overwrite existing data.');
            $this->warn('If you really want to do this, use the --force flag.');

            return Command::FAILURE;
        }

        $this->info('Setting up demo data...');

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Run the demo data seeder
            $this->call('db:seed', [
                '--class' => 'Database\\Seeders\\DemoDataSeeder',
            ]);

            // Commit the transaction
            DB::commit();

            $this->newLine();
            $this->info('Demo data has been set up successfully!');
            $this->info('You can now log in with the following credentials:');
            $this->info('- Admin: admin@example.com / password');
            $this->info('- User: user@example.com / password');

            return Command::SUCCESS;
        } catch (Throwable $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            $this->newLine();
            $this->error('Failed to set up demo data!');
            $this->error($e->getMessage());

            $this->newLine();
            $this->info('Troubleshooting tips:');
            $this->info('1. Make sure your database connection is configured correctly in .env');
            $this->info('2. Check if the required tables exist by running "php artisan migrate:status"');
            $this->info('3. If tables are missing, run "php artisan migrate" to create them');
            $this->info('4. If you have existing data conflicts, consider using a fresh database');

            return Command::FAILURE;
        }
    }
}
