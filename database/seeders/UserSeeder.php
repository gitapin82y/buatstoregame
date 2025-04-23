<?php

// database/seeders/UserSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ResellerProfile;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@buattokogame.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
        
        // Create Example Reseller
        $reseller = User::create([
            'name' => 'Demo Reseller',
            'email' => 'reseller@buattokogame.com',
            'password' => Hash::make('password'),
            'role' => 'reseller',
            'email_verified_at' => now(),
        ]);
        
        // Create Reseller Profile
        ResellerProfile::create([
            'user_id' => $reseller->id,
            'store_name' => 'Demo Game Store',
            'store_description' => 'This is a demo game store for testing purposes.',
            'store_theme_color' => '#3490dc',
            'membership_level' => 'gold',
            'membership_expires_at' => now()->addMonths(3),
            'subdomain' => 'demo',
            'balance' => 1000000,
            'social_facebook' => 'demo.gamestore',
            'social_instagram' => 'demo.gamestore',
            'social_twitter' => 'demo_gamestore',
        ]);
        
        // Create Example User
        User::create([
            'name' => 'Demo User',
            'email' => 'user@buattokogame.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
    }
}