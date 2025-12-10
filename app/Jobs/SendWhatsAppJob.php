<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Foundation\Bus\Dispatchable; // <-- ADD THIS
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SendWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels; // <-- ADD Dispatchable

    public $payload;
    public $tries = 3;
    public $backoff = [10, 30, 60];
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function middleware()
    {
        // Rate limit (global key 'whatsapp') - adjust as per provider limits
        return [ new RateLimited('whatsapp') ];
    }

    public function handle()
    {
        $client = new Client(['timeout' => 20]);

        $phone = $this->payload['phone'];
        try {
            if ($this->payload['type'] === 'session') {
                $body = [
                    // provider-specific session payload
                    'to' => $phone,
                    'message' => $this->payload['session_message'],
                ];
                $res = $client->post(config('services.gupshup.session_url'), [
                    'json' => $body,
                    'headers' => [ 'Authorization' => 'Bearer '.config('services.gupshup.token') ],
                ]);
            } else {
                // template message
                $body = [
                    'to' => $phone,
                    'template_params' => $this->payload['template_params'],
                    // provider template id etc
                ];
                $res = $client->post(config('services.gupshup.template_url'), [
                    'json' => $body,
                    'headers' => [ 'Authorization' => 'Bearer '.config('services.gupshup.token') ],
                ]);
            }

            $statusCode = $res->getStatusCode();
            $body = json_decode((string)$res->getBody(), true);

            // check provider response format for successful submission
            if ($statusCode === 200 && (isset($body['status']) && $body['status'] === 'submitted')) {
                Log::info("WhatsApp queued/sent to {$phone} for task {$this->payload['task_id']}");
            } else {
                Log::warning("Unexpected provider response for {$phone}: " . json_encode($body));
                // optionally throw to retry
                // throw new \Exception('Provider returned non-submitted status');
            }
        } catch (RequestException $e) {
            Log::error("WhatsApp send failed to {$phone}: " . $e->getMessage());
            throw $e; // let Laravel retry based on $tries/backoff
        } catch (\Exception $e) {
            Log::error("WhatsApp send unexpected error for {$phone}: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        // record failure to DB or S3/csv for later analysis
        Log::error("SendWhatsAppJob failed for {$this->payload['phone']}: " . $exception->getMessage());
    }
}
