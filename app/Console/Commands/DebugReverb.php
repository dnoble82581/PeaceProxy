<?php

namespace App\Console\Commands;

use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DebugReverb extends Command
{
    protected $signature = 'debug:reverb {channel=debug} {event=debug.ping}';
    protected $description = 'Send a test event to Reverb and print HTTP response';

    public function handle(BroadcastManager $bm): int
    {
        $conn = config('broadcasting.connections.reverb');
        $opts = $conn['options'] ?? [];
        $host = $opts['host'];
        $port = $opts['port'];
        $scheme = $opts['scheme'];
        $appId = $conn['app_id'];
        $key = $conn['key'];
        $secret = $conn['secret'];

        $payload = [
            'name' => (string)$this->argument('event'),
            'channels' => [(string)$this->argument('channel')],
            'data' => ['msg' => 'hello']
        ];
        $json = json_encode($payload);

        // Reverb follows the Pusher-compatible signature: HMAC-SHA256 of body using app secret.
        $sig = hash_hmac('sha256', $json, $secret);

        $url = sprintf('%s://%s:%s/apps/%s/events', $scheme, $host, $port, $appId);

        $this->info("POST $url");
        $res = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Reverb-Key' => $key,
            'X-Reverb-Signature' => $sig,
        ])->timeout(5)->post($url, $payload);

        $this->info('Status: '.$res->status());
        $this->line('Body: '.$res->body());

        return self::SUCCESS;
    }
}
