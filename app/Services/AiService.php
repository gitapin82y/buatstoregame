<?php

namespace App\Services;

use App\Models\Content;
use App\Models\ResellerProfile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class AiService
{
    /**
     * Generate content for social media
     */
    public function generateContent($prompt, $platform = 'instagram', $type = 'caption')
    {
        try {
            $apiKey = config('services.openai.api_key');
            
            $systemPrompt = "Kamu adalah asisten ahli media sosial yang membuat konten promosi untuk game. ";
            
            switch ($platform) {
                case 'instagram':
                    $systemPrompt .= "Buatkan caption Instagram yang menarik dan mengundang engagement. ";
                    break;
                case 'facebook':
                    $systemPrompt .= "Buatkan postingan Facebook yang menarik dan mengundang diskusi. ";
                    break;
                case 'twitter':
                    $systemPrompt .= "Buatkan tweet singkat dan menarik dengan hashtag yang relevan. ";
                    break;
                default:
                    $systemPrompt .= "Buatkan caption media sosial yang menarik dan mengundang engagement. ";
            }
            
            if ($type === 'caption') {
                $systemPrompt .= "Pastikan konten menarik, menggunakan emoji yang relevan, dan hashtag yang tepat. ";
            } elseif ($type === 'post') {
                $systemPrompt .= "Buatkan konten postingan lengkap yang bisa dipublikasikan. Termasuk pembukaan, konten utama, call to action, dan hashtag yang relevan. ";
            } elseif ($type === 'content_plan') {
                $systemPrompt .= "Buatkan rencana konten mingguan dengan tema, caption, dan waktu terbaik untuk diposting. ";
            }
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                return $result['choices'][0]['message']['content'] ?? '';
            }
            
            Log::error('OpenAI API error: ' . $response->body());
            return 'Maaf, terjadi kesalahan saat menghasilkan konten.';
            
        } catch (Exception $e) {
            Log::error('Error generating AI content: ' . $e->getMessage());
            return 'Maaf, terjadi kesalahan saat menghasilkan konten.';
        }
    }
    
    /**
     * Generate image for social media
     */
    public function generateImage($prompt)
    {
        try {
            $apiKey = config('services.openai.api_key');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/images/generations', [
                'prompt' => $prompt,
                'n' => 1,
                'size' => '1024x1024',
                'response_format' => 'url',
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                $imageUrl = $result['data'][0]['url'] ?? null;
                
                if ($imageUrl) {
                    // Download image
                    $imageContent = file_get_contents($imageUrl);
                    $filename = 'ai-generated/' . time() . '.png';
                    
                    // Save to storage
                    Storage::disk('public')->put($filename, $imageContent);
                    
                    return Storage::disk('public')->url($filename);
                }
            }
            
            Log::error('OpenAI Image API error: ' . $response->body());
            return null;
            
        } catch (Exception $e) {
            Log::error('Error generating AI image: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Generate content calendar
     */
    public function generateContentCalendar(ResellerProfile $reseller, $game)
    {
        try {
            $apiKey = config('services.openai.api_key');
            
            $prompt = "Buatkan rencana konten media sosial untuk 7 hari ke depan untuk toko game '{$reseller->store_name}' ";
            $prompt .= "yang fokus menjual game '{$game}'. Termasuk jenis konten, caption, dan waktu terbaik untuk diposting.";
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "Kamu adalah ahli strategi konten media sosial untuk toko game. Buatkan rencana konten yang detail, praktis, dan efektif untuk meningkatkan penjualan."
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                return $result['choices'][0]['message']['content'] ?? '';
            }
            
            Log::error('OpenAI API error: ' . $response->body());
            return 'Maaf, terjadi kesalahan saat menghasilkan kalender konten.';
            
        } catch (Exception $e) {
            Log::error('Error generating content calendar: ' . $e->getMessage());
            return 'Maaf, terjadi kesalahan saat menghasilkan kalender konten.';
        }
    }
}