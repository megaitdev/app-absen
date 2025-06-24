<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        // Check if user has verified WhatsApp number
        if (!$notifiable->nomor_wa_verified_at || !$notifiable->nomor_wa) {
            return;
        }

        // Get the WhatsApp message content
        $message = $notification->toWhatsApp($notifiable);
        
        // Send WhatsApp message
        $this->sendWhatsAppMessage($notifiable->nomor_wa, $message);
    }

    /**
     * Send WhatsApp message via API
     */
    private function sendWhatsAppMessage($phoneNumber, $message)
    {
        try {
            // Format phone number (remove leading 0 and add country code)
            $formattedNumber = $this->formatPhoneNumber($phoneNumber);
            
            // WhatsApp API configuration
            $apiUrl = config('services.whatsapp.api_url');
            $apiKey = config('services.whatsapp.api_key');
            
            if (!$apiUrl || !$apiKey) {
                Log::warning('WhatsApp API configuration not found');
                return false;
            }

            // Send message via HTTP API
            $response = Http::timeout(30)->post($apiUrl, [
                'api_key' => $apiKey,
                'receiver' => $formattedNumber,
                'data' => [
                    'message' => $message
                ]
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'phone' => $formattedNumber,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('Failed to send WhatsApp message', [
                    'phone' => $formattedNumber,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp message sending failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Format phone number for WhatsApp API
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Remove all non-numeric characters
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Remove leading zero and add Indonesia country code
        if (substr($cleaned, 0, 1) === '0') {
            $cleaned = '62' . substr($cleaned, 1);
        } elseif (substr($cleaned, 0, 2) !== '62') {
            $cleaned = '62' . $cleaned;
        }
        
        return $cleaned;
    }
}
