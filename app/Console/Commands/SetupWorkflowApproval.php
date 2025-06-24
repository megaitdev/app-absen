<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupWorkflowApproval extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'workflow:setup {--seed : Run seeder after migration}';

    /**
     * The console command description.
     */
    protected $description = 'Setup workflow approval system (run migrations and optionally seed data)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up Workflow Approval System...');
        
        // Run migrations
        $this->info('Running migrations...');
        Artisan::call('migrate', [], $this->getOutput());
        
        if ($this->option('seed')) {
            $this->info('Running workflow approval seeder...');
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\WorkflowApprovalSeeder'
            ], $this->getOutput());
        }
        
        $this->info('Workflow Approval System setup completed!');
        
        $this->newLine();
        $this->info('Next steps:');
        $this->line('1. Configure approval levels in Settings > Approval Levels');
        $this->line('2. Set users as supervisors or HRD in user management');
        $this->line('3. Configure WhatsApp API settings in .env file:');
        $this->line('   WHATSAPP_API_URL=your_whatsapp_api_url');
        $this->line('   WHATSAPP_API_KEY=your_whatsapp_api_key');
        
        return 0;
    }
}
