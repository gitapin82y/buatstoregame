<?php

// database/seeders/SettingSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Application settings
        $settings = [
            // General settings
            ['key' => 'site_name', 'value' => 'BuatTokoGame', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Platform Reseller Game Terbaik', 'group' => 'general'],
            ['key' => 'site_logo', 'value' => 'logo.png', 'group' => 'general'],
            ['key' => 'site_favicon', 'value' => 'favicon.ico', 'group' => 'general'],
            ['key' => 'admin_email', 'value' => 'admin@buattokogame.com', 'group' => 'general'],
            ['key' => 'contact_phone', 'value' => '+6281234567890', 'group' => 'general'],
            
            // Payment settings
            ['key' => 'xendit_mode', 'value' => 'sandbox', 'group' => 'payment'],
            ['key' => 'default_currency', 'value' => 'IDR', 'group' => 'payment'],
            ['key' => 'min_withdrawal', 'value' => '50000', 'group' => 'payment'],
            
            // Store settings
            ['key' => 'server_ip', 'value' => '123.456.789.10', 'group' => 'store'],
            ['key' => 'default_theme_color', 'value' => '#3490dc', 'group' => 'store'],
            
            // Affiliate settings
            ['key' => 'affiliate_enabled', 'value' => 'true', 'group' => 'affiliate'],
            ['key' => 'default_commission_rate', 'value' => '5', 'group' => 'affiliate'],
        ];
        
        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}