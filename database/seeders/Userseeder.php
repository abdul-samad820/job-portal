<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->whereIn('email', [
            'samad@jobhub.com',
            'rahul@jobhub.com',
            'priya@jobhub.com',
            'amit@jobhub.com',
            'neha@jobhub.com',
        ])->delete();

        $users = [
            [
                'name' => 'Samad Khan',
                'email' => 'samad@jobhub.com',
                'password' => Hash::make('User@123'),
                'phone' => '9111111101',
                'address' => 'Sarsawa, Uttar Pradesh',
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            [
                'name' => 'Rahul Sharma',
                'email' => 'rahul@jobhub.com',
                'password' => Hash::make('User@123'),
                'phone' => '9111111102',
                'address' => 'Noida, Uttar Pradesh',
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(12),
            ],
            [
                'name' => 'Priya Patel',
                'email' => 'priya@jobhub.com',
                'password' => Hash::make('User@123'),
                'phone' => '9111111103',
                'address' => 'Ahmedabad, Gujarat',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'name' => 'Amit Verma',
                'email' => 'amit@jobhub.com',
                'password' => Hash::make('User@123'),
                'phone' => '9111111104',
                'address' => 'Hyderabad, Telangana',
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],
            [
                'name' => 'Neha Singh',
                'email' => 'neha@jobhub.com',
                'password' => Hash::make('User@123'),
                'phone' => '9111111105',
                'address' => 'Delhi, India',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
        ];

        DB::table('users')->insert($users);

        $this->command->info('Users seeded (all password: User@123):');
        foreach ($users as $u) {
            $this->command->line("   {$u['email']}");
        }
    }
}
