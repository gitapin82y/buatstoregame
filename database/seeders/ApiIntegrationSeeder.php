<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApiIntegration;

class ApiIntegrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Add example API integration (Digiflazz)
        ApiIntegration::create([
            'name' => 'Digiflazz',
            'base_url' => 'https://api.digiflazz.com/v1',
            'api_key' => 'your-api-username',
            'api_secret' => 'your-api-key',
            'status' => 'active',
        ]);
    }
}