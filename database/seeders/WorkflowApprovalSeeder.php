<?php

namespace Database\Seeders;

use App\Models\ApprovalLevel;
use App\Models\User;
use App\Models\mak_hrd\Unit;
use App\Models\mak_hrd\Divisi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WorkflowApprovalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample users for testing
        $this->createSampleUsers();
        
        // Create sample approval levels
        $this->createSampleApprovalLevels();
    }

    private function createSampleUsers()
    {
        // Create HRD user
        $hrdUser = User::firstOrCreate(
            ['username' => 'hrd_admin'],
            [
                'nama' => 'HRD Administrator',
                'email' => 'hrd@company.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_hrd' => true,
                'is_supervisor' => false,
            ]
        );

        // Create Supervisor users
        $supervisor1 = User::firstOrCreate(
            ['username' => 'supervisor_it'],
            [
                'nama' => 'Supervisor IT',
                'email' => 'supervisor.it@company.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_hrd' => false,
                'is_supervisor' => true,
                'supervised_units' => [1], // Assuming unit ID 1 exists
            ]
        );

        $supervisor2 = User::firstOrCreate(
            ['username' => 'supervisor_finance'],
            [
                'nama' => 'Supervisor Finance',
                'email' => 'supervisor.finance@company.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_hrd' => false,
                'is_supervisor' => true,
                'supervised_divisis' => [1], // Assuming divisi ID 1 exists
            ]
        );

        // Create PIC user
        $picUser = User::firstOrCreate(
            ['username' => 'pic_user'],
            [
                'nama' => 'PIC User',
                'email' => 'pic@company.com',
                'password' => Hash::make('password'),
                'role' => 'pic',
                'is_hrd' => false,
                'is_supervisor' => false,
            ]
        );

        echo "Sample users created successfully.\n";
    }

    private function createSampleApprovalLevels()
    {
        // Get sample units and divisis (if they exist)
        $units = Unit::limit(3)->get();
        $divisis = Divisi::limit(3)->get();
        
        // Get supervisor users
        $supervisors = User::where('is_supervisor', true)->get();

        if ($supervisors->isEmpty()) {
            echo "No supervisor users found. Please create supervisor users first.\n";
            return;
        }

        // Create approval levels for units
        foreach ($units as $index => $unit) {
            $supervisor = $supervisors->get($index % $supervisors->count());
            
            ApprovalLevel::firstOrCreate(
                [
                    'unit_id' => $unit->id,
                    'approval_type' => 'unit'
                ],
                [
                    'supervisor_user_id' => $supervisor->id,
                    'is_active' => true,
                ]
            );
        }

        // Create approval levels for divisis
        foreach ($divisis as $index => $divisi) {
            $supervisor = $supervisors->get($index % $supervisors->count());
            
            ApprovalLevel::firstOrCreate(
                [
                    'divisi_id' => $divisi->id,
                    'approval_type' => 'divisi'
                ],
                [
                    'supervisor_user_id' => $supervisor->id,
                    'is_active' => true,
                ]
            );
        }

        echo "Sample approval levels created successfully.\n";
    }
}
