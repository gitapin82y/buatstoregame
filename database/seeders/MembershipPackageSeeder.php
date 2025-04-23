<?php

// database/seeders/MembershipPackageSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MembershipPackage;

class MembershipPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Silver Package
        MembershipPackage::create([
            'name' => 'Silver Package',
            'level' => 'silver',
            'price' => 99000,
            'duration_days' => 30,
            'description' => 'Basic package for starting resellers',
            'features' => json_encode([
                'Subdomain (nama.buattokogame.com)',
                'Unlimited Game Integration',
                'Transaksi Tanpa Batas',
                'Support Email',
                'Custom Color Theme',
            ]),
            'status' => 'active',
        ]);
        
        // Gold Package
        MembershipPackage::create([
            'name' => 'Gold Package',
            'level' => 'gold',
            'price' => 199000,
            'duration_days' => 30,
            'description' => 'Premium package with advanced features',
            'features' => json_encode([
                'Semua fitur Silver',
                'Custom Domain (.my.id)',
                'Content Generator dengan AI',
                'Social Media Content Planner',
                'Priority Support',
                'Brand Image Generator',
            ]),
            'status' => 'active',
        ]);
        
        // Gold Package (3 Months)
        MembershipPackage::create([
            'name' => 'Gold Package (3 Months)',
            'level' => 'gold',
            'price' => 499000,
            'duration_days' => 90,
            'description' => 'Premium package for 3 months with discount',
            'features' => json_encode([
                'Semua fitur Gold',
                'Diskon 16%',
                'Custom Domain (.my.id)',
                'Content Generator dengan AI',
                'Social Media Content Planner',
                'Priority Support',
                'Brand Image Generator',
            ]),
            'status' => 'active',
        ]);
    }
}