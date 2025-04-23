<?php
namespace App\Services;

use App\Models\ApiIntegration;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GameApiService
{
    /**
     * Get API configuration
     */
    private function getApiConfig()
    {
        $api = ApiIntegration::where('status', 'active')->first();
        if (!$api) {
            throw new Exception('No active API integration found');
        }
        
        return $api;
    }
    
    /**
     * Process transaction by calling game topup API
     */
    public function processTransaction(UserTransaction $transaction)
    {
        try {
            $api = $this->getApiConfig();
            $option = $transaction->option;
            $service = $transaction->service;
            
            // Berbeda metode berdasarkan tipe layanan
            if ($service->type === 'topup') {
                return $this->processTopUp($transaction, $api);
            } elseif ($service->type === 'joki') {
                return $this->processJoki($transaction, $api);
            } elseif ($service->type === 'formation') {
                return $this->processFormation($transaction, $api);
            } else {
                return $this->processGenericService($transaction, $api);
            }
        } catch (Exception $e) {
            Log::error('Error processing game transaction: ' . $e->getMessage());
            
            // Update transaction status to failed
            $transaction->update([
                'process_status' => 'failed'
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Process top-up transaction
     */
    private function processTopUp(UserTransaction $transaction, ApiIntegration $api)
    {
        $option = $transaction->option;
        
        // Gunakan API DigiFlazz sebagai contoh
        $endpoint = $api->base_url . '/transaction';
        
        $payload = [
            'username' => $api->api_key,
            'api_key' => $this->generateDigiflazzSign($api),
            'buyer_sku_code' => $option->api_code,
            'customer_no' => $transaction->user_identifier,
            'ref_id' => $transaction->invoice_number,
            'testing' => config('app.env') !== 'production'
        ];
        
        $response = Http::post($endpoint, $payload);
        $result = $response->json();
        
        Log::info('Game API response:', $result);
        
        if ($response->successful() && isset($result['data']) && $result['data']['status'] === 'Sukses') {
            $transaction->update([
                'process_status' => 'completed',
                'completed_at' => now()
            ]);
            
            // Send notification
            event(new \App\Events\TransactionCompleted($transaction));
            
            return true;
        } elseif ($response->successful() && isset($result['data']) && $result['data']['status'] === 'Pending') {
            // Status pending, tunggu callback
            return true;
        } else {
            // Handle error
            $errorMessage = $result['data']['message'] ?? 'Unknown error';
            Log::error('Game API error: ' . $errorMessage);
            
            return false;
        }
    }
    
    /**
     * Process joki transaction
     */
    private function processJoki(UserTransaction $transaction, ApiIntegration $api)
    {
        // Joki biasanya diproses manual
        $transaction->update([
            'process_status' => 'processing'
        ]);
        
        // Kirim notifikasi ke admin bahwa ada order joki
        event(new \App\Events\JokiOrderReceived($transaction));
        
        return true;
    }
    
    /**
     * Process formation transaction
     */
    private function processFormation(UserTransaction $transaction, ApiIntegration $api)
    {
        // Formation biasanya diproses manual
        $transaction->update([
            'process_status' => 'processing'
        ]);
        
        // Kirim notifikasi ke admin bahwa ada order formation
        event(new \App\Events\FormationOrderReceived($transaction));
        
        return true;
    }
    
    /**
     * Process generic service transaction
     */
    private function processGenericService(UserTransaction $transaction, ApiIntegration $api)
    {
        // Generic service diproses manual
        $transaction->update([
            'process_status' => 'processing'
        ]);
        
        // Kirim notifikasi ke admin
        event(new \App\Events\ServiceOrderReceived($transaction));
        
        return true;
    }
    
    /**
     * Generate sign untuk API DigiFlazz
     */
    private function generateDigiflazzSign(ApiIntegration $api)
    {
        $username = $api->api_key;
        $apiKey = $api->api_secret;
        
        return md5($username . $apiKey . time());
    }
    
    /**
     * Check transaction status
     */
    public function checkTransactionStatus(UserTransaction $transaction)
    {
        try {
            $api = $this->getApiConfig();
            $endpoint = $api->base_url . '/transaction';
            
            $payload = [
                'username' => $api->api_key,
                'api_key' => $this->generateDigiflazzSign($api),
                'ref_id' => $transaction->invoice_number
            ];
            
            $response = Http::post($endpoint, $payload);
            $result = $response->json();
            
            if ($response->successful() && isset($result['data'])) {
                $status = $result['data']['status'];
                
                if ($status === 'Sukses') {
                    $transaction->update([
                        'process_status' => 'completed',
                        'completed_at' => now()
                    ]);
                    
                    // Send notification
                    event(new \App\Events\TransactionCompleted($transaction));
                } elseif ($status === 'Gagal') {
                    $transaction->update([
                        'process_status' => 'failed'
                    ]);
                    
                    // Send notification
                    event(new \App\Events\TransactionFailed($transaction));
                }
                
                return $status;
            }
            
            return null;
        } catch (Exception $e) {
            Log::error('Error checking transaction status: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get game list from API
     */
    public function getGameList()
    {
        try {
            $api = $this->getApiConfig();
            $endpoint = $api->base_url . '/price-list';
            
            $payload = [
                'username' => $api->api_key,
                'sign' => $this->generateDigiflazzSign($api),
                'code' => 'pulsa'
            ];
            
            $response = Http::post($endpoint, $payload);
            
            if ($response->successful()) {
                return $response->json()['data'];
            }
            
            return [];
        } catch (Exception $e) {
            Log::error('Error getting game list: ' . $e->getMessage());
            return [];
        }
    }
}
