<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestBroadcast extends Command
{
    protected $signature = 'test:broadcast {channel=debug} {event=debug.ping}';

    protected $description = 'Send a test broadcast via the active broadcaster';

    public function handle(BroadcastManager $bm): int
    {
        $drv = $bm->driver(); // default driver (should be reverb)
        $this->info('Driver: '.get_class($drv));
        $this->info('Options: '.json_encode(config('broadcasting.connections.reverb.options')));
        $this->info('App: '.json_encode([
            'id' => config('broadcasting.connections.reverb.app_id'),
            'key' => config('broadcasting.connections.reverb.key'),
        ]));

        try {
            $drv->broadcast([(string) $this->argument('channel')], (string) $this->argument('event'), ['msg' => 'hello']);
            $this->info('Broadcast attempted.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Broadcast failed: '.$e->getMessage());
            report($e);

            return self::FAILURE;
        }
    }
}
