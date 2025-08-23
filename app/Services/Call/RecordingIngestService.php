<?php

namespace App\Services\Call;

class RecordingIngestService implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use \Illuminate\Bus\Queueable;
    use \Illuminate\Queue\SerializesModels;

    public function __construct(public int $recordingId)
    {
    }

    public function handle(\Twilio\Rest\Client $twilio, \App\Contracts\CallRecordingRepositoryInterface $recs): void
    {
        $rec = $recs->find($this->recordingId);
        if (! $rec || ! $rec->recording_sid) {
            return;
        }

        // Twilio provides temporary URL; fetch and stream to S3
        $uri = $rec->recording_url.'.mp3'; // or .wav
        $stream = \Http::withBasicAuth(config('twilio.account_sid'), config('twilio.auth_token'))->get($uri)->body();

        $path = "calls/{$rec->call_id}/recordings/{$rec->recording_sid}.mp3";
        \Storage::disk('s3-private')->put($path, $stream);

        $recs->update($rec->id, ['storage_path' => $path]);
    }
}
