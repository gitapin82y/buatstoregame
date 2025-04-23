<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\GameService;
use App\Models\ServiceOption;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Mobile Legends
        $ml = Game::create([
            'name' => 'Mobile Legends',
            'slug' => 'mobile-legends',
            'description' => 'Mobile Legends: Bang Bang adalah game MOBA mobile yang dikembangkan dan diterbitkan oleh Moonton.',
            'logo' => 'games/logos/ml.png',
            'banner' => 'games/banners/ml.jpg',
            'status' => 'active',
        ]);
        
        // ML Diamond Service
        $mlDiamond = GameService::create([
            'game_id' => $ml->id,
            'name' => 'Diamond',
            'slug' => 'diamond',
            'type' => 'topup',
            'description' => 'Top up diamond Mobile Legends dengan harga termurah dan proses instan.',
            'image' => 'games/services/ml-diamond.png',
            'price_range' => '15.000 - 300.000',
            'status' => 'active',
        ]);
        
        // ML Diamond Options
        $mlDiamondOptions = [
            ['name' => '86 Diamonds', 'api_code' => 'ML86', 'base_price' => 19000],
            ['name' => '172 Diamonds', 'api_code' => 'ML172', 'base_price' => 38000],
            ['name' => '257 Diamonds', 'api_code' => 'ML257', 'base_price' => 57000],
            ['name' => '344 Diamonds', 'api_code' => 'ML344', 'base_price' => 76000],
            ['name' => '429 Diamonds', 'api_code' => 'ML429', 'base_price' => 95000],
            ['name' => '514 Diamonds', 'api_code' => 'ML514', 'base_price' => 114000],
            ['name' => '600 Diamonds', 'api_code' => 'ML600', 'base_price' => 133000],
            ['name' => '706 Diamonds', 'api_code' => 'ML706', 'base_price' => 152000],
            ['name' => '878 Diamonds', 'api_code' => 'ML878', 'base_price' => 190000],
            ['name' => '963 Diamonds', 'api_code' => 'ML963', 'base_price' => 209000],
            ['name' => '1050 Diamonds', 'api_code' => 'ML1050', 'base_price' => 228000],
            ['name' => '1412 Diamonds', 'api_code' => 'ML1412', 'base_price' => 304000],
            ['name' => '2195 Diamonds', 'api_code' => 'ML2195', 'base_price' => 456000],
            ['name' => '3688 Diamonds', 'api_code' => 'ML3688', 'base_price' => 760000],
        ];
        
        foreach ($mlDiamondOptions as $option) {
            ServiceOption::create([
                'game_service_id' => $mlDiamond->id,
                'name' => $option['name'],
                'api_code' => $option['api_code'],
                'base_price' => $option['base_price'],
                'status' => 'active',
            ]);
        }
        
        // ML Weekly Pass
        $mlWeeklyPass = GameService::create([
            'game_id' => $ml->id,
            'name' => 'Weekly Pass',
            'slug' => 'weekly-pass',
            'type' => 'topup',
            'description' => 'Weekly Diamond Pass Mobile Legends dengan harga termurah dan proses instan.',
            'image' => 'games/services/ml-weekly.png',
            'price_range' => '25.000 - 35.000',
            'status' => 'active',
        ]);
        
        // ML Weekly Pass Options
        ServiceOption::create([
            'game_service_id' => $mlWeeklyPass->id,
            'name' => 'Weekly Diamond Pass',
            'api_code' => 'MLWDP',
            'base_price' => 23000,
            'status' => 'active',
        ]);
        
        // ML Joki Rank
        $mlJoki = GameService::create([
            'game_id' => $ml->id,
            'name' => 'Joki Rank',
            'slug' => 'joki-rank',
            'type' => 'joki',
            'description' => 'Jasa joki rank Mobile Legends oleh player profesional dan terpercaya.',
            'image' => 'games/services/ml-joki.png',
            'price_range' => '50.000 - 500.000',
            'status' => 'active',
        ]);
        
        // ML Joki Options
        $mlJokiOptions = [
            ['name' => 'Warrior ke Master', 'base_price' => 75000],
            ['name' => 'Master ke Grandmaster', 'base_price' => 100000],
            ['name' => 'Grandmaster ke Epic', 'base_price' => 150000],
            ['name' => 'Epic ke Legend', 'base_price' => 200000],
            ['name' => 'Legend ke Mythic', 'base_price' => 300000],
            ['name' => 'Mythic ke Glory', 'base_price' => 500000],
        ];
        
        foreach ($mlJokiOptions as $option) {
            ServiceOption::create([
                'game_service_id' => $mlJoki->id,
                'name' => $option['name'],
                'base_price' => $option['base_price'],
                'status' => 'active',
            ]);
        }
        
        // Free Fire
        $ff = Game::create([
            'name' => 'Free Fire',
            'slug' => 'free-fire',
            'description' => 'Garena Free Fire adalah game battle royale mobile yang dikembangkan oleh 111 Dots Studio dan diterbitkan oleh Garena.',
            'logo' => 'games/logos/ff.png',
            'banner' => 'games/banners/ff.jpg',
            'status' => 'active',
        ]);
        
        // FF Diamond Service
        $ffDiamond = GameService::create([
            'game_id' => $ff->id,
            'name' => 'Diamond',
            'slug' => 'diamond',
            'type' => 'topup',
            'description' => 'Top up diamond Free Fire dengan harga termurah dan proses instan.',
            'image' => 'games/services/ff-diamond.png',
            'price_range' => '10.000 - 250.000',
            'status' => 'active',
        ]);
        
        // FF Diamond Options
        $ffDiamondOptions = [
            ['name' => '50 Diamonds', 'api_code' => 'FF50', 'base_price' => 7000],
            ['name' => '70 Diamonds', 'api_code' => 'FF70', 'base_price' => 10000],
            ['name' => '100 Diamonds', 'api_code' => 'FF100', 'base_price' => 15000],
            ['name' => '140 Diamonds', 'api_code' => 'FF140', 'base_price' => 20000],
            ['name' => '210 Diamonds', 'api_code' => 'FF210', 'base_price' => 30000],
            ['name' => '355 Diamonds', 'api_code' => 'FF355', 'base_price' => 50000],
            ['name' => '720 Diamonds', 'api_code' => 'FF720', 'base_price' => 100000],
            ['name' => '1450 Diamonds', 'api_code' => 'FF1450', 'base_price' => 200000],
            ['name' => '2180 Diamonds', 'api_code' => 'FF2180', 'base_price' => 300000],
        ];
        
        foreach ($ffDiamondOptions as $option) {
            ServiceOption::create([
                'game_service_id' => $ffDiamond->id,
                'name' => $option['name'],
                'api_code' => $option['api_code'],
                'base_price' => $option['base_price'],
                'status' => 'active',
            ]);
        }
        
        // eFootball (PES Mobile)
        $ef = Game::create([
            'name' => 'eFootball',
            'slug' => 'efootball',
            'description' => 'eFootball (sebelumnya Pro Evolution Soccer / PES) adalah game sepak bola yang dikembangkan oleh Konami.',
            'logo' => 'games/logos/ef.png',
            'banner' => 'games/banners/ef.jpg',
            'status' => 'active',
        ]);
        
        // eFootball Coins Service
        $efCoins = GameService::create([
            'game_id' => $ef->id,
            'name' => 'eFootball Coins',
            'slug' => 'coins',
            'type' => 'topup',
            'description' => 'Top up eFootball Coins dengan harga termurah dan proses instan.',
            'image' => 'games/services/ef-coins.png',
            'price_range' => '15.000 - 300.000',
            'status' => 'active',
        ]);
        
        // eFootball Coins Options
        $efCoinsOptions = [
            ['name' => '100 Coins', 'api_code' => 'EF100', 'base_price' => 16000],
            ['name' => '300 Coins', 'api_code' => 'EF300', 'base_price' => 46000],
            ['name' => '500 Coins', 'api_code' => 'EF500', 'base_price' => 76000],
            ['name' => '1000 Coins', 'api_code' => 'EF1000', 'base_price' => 150000],
            ['name' => '2000 Coins', 'api_code' => 'EF2000', 'base_price' => 300000],
            ['name' => '3000 Coins', 'api_code' => 'EF3000', 'base_price' => 450000],
        ];
        
        foreach ($efCoinsOptions as $option) {
            ServiceOption::create([
                'game_service_id' => $efCoins->id,
                'name' => $option['name'],
                'api_code' => $option['api_code'],
                'base_price' => $option['base_price'],
                'status' => 'active',
            ]);
        }
        
        // eFootball Formation Service
        $efFormation = GameService::create([
            'game_id' => $ef->id,
            'name' => 'Racikan Formasi',
            'slug' => 'racikan-formasi',
            'type' => 'formation',
            'description' => 'Jasa racikan formasi eFootball oleh pemain profesional dan berpengalaman.',
            'image' => 'games/services/ef-formation.png',
            'price_range' => '25.000 - 100.000',
            'status' => 'active',
        ]);
        
        // eFootball Formation Options
        $efFormationOptions = [
            ['name' => 'Formasi Dasar', 'base_price' => 25000],
            ['name' => 'Formasi Menengah', 'base_price' => 50000],
            ['name' => 'Formasi Pro', 'base_price' => 75000],
            ['name' => 'Formasi Custom + Konsultasi', 'base_price' => 100000],
        ];
        
        foreach ($efFormationOptions as $option) {
            ServiceOption::create([
                'game_service_id' => $efFormation->id,
                'name' => $option['name'],
                'base_price' => $option['base_price'],
                'status' => 'active',
            ]);
        }
    }
}