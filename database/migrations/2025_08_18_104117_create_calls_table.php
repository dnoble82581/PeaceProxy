<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            // Ownership / linking
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('negotiation_id')->nullable()->constrained()->nullOnDelete();
            $table->morphs('callable'); // callable_type, callable_id (e.g., Subject)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // Twilio identifiers
            $table->char('account_sid', 34)->index();
            $table->char('call_sid', 34)->unique();             // CAxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
            $table->char('parent_call_sid', 34)->nullable()->index();
            $table->char('conference_sid', 34)->nullable()->index(); // CF... if you use <Conference>
            $table->string('api_version', 16)->nullable();      // e.g., 2010-04-01

            // Addressing & channel
            $table->enum('direction', ['inbound', 'outbound-api', 'outbound-dial'])->index();
            $table->string('from_e164', 20)->index();
            $table->string('to_e164', 20)->index();
            $table->string('caller_name')->nullable();          // CNAM if provided
            $table->string('forwarded_from')->nullable();       // if PSTN forwarding occurred

            // Status & lifecycle
            $table->enum('status', [
                'queued', 'ringing', 'in-progress', 'completed',
                'busy', 'failed', 'no-answer', 'canceled',
            ])->index();

            // Useful timeline stamps (populate from StatusCallback or Call resource)
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('ringing_at')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable(); // Twilio CallDuration

            // AMD / DTMF (Twilio)
            $table->enum('answered_by', [
                'human', 'machine', 'machine_start', 'machine_end_beep', 'fax', 'unknown',
            ])->nullable();               // Twilio AnsweredBy
            $table->unsignedInteger('amd_duration_ms')->nullable(); // if you enable duration return
            $table->string('dtmf_digits', 32)->nullable();          // Digits from <Gather>
            $table->json('dtmf_payload')->nullable();

            // Error / SIP / pricing
            $table->unsignedSmallInteger('sip_response_code')->nullable(); // SipResponseCode
            $table->integer('error_code')->nullable();                     // ErrorCode from resource/webhook
            $table->text('error_text')->nullable();
            $table->decimal('price', 8, 4)->nullable();    // may be negative on credits
            $table->char('price_unit', 3)->nullable();     // e.g., USD

            // Recording/transcription summary (keep light here; details in child table)
            $table->boolean('has_recordings')->default(false);
            $table->enum('transcript_status', ['queued', 'processing', 'completed', 'failed'])->nullable();
            $table->string('transcript_provider')->nullable();
            $table->string('transcript_language', 10)->nullable();
            $table->longText('transcript_text')->nullable();

            // Webhook bookkeeping & misc
            $table->string('last_event_type')->nullable(); // e.g., 'initiated','ringing','answered','completed','recording'
            $table->json('last_event_payload')->nullable();
            $table->timestamp('last_event_at')->nullable();

            $table->text('notes')->nullable(); // consider encryption
            $table->json('meta')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Helpful indexes
            $table->index(['tenant_id', 'status', 'created_at']);
            $table->index(['tenant_id', 'call_sid']);
        });

        Schema::create('call_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_id')->constrained('calls')->cascadeOnDelete();
            $table->string('type'); // 'initiated','ringing','answered','completed','gather','amd','error'
            $table->json('payload'); // raw webhook
            $table->unsignedBigInteger('sequence')->nullable(); // if you pass SequenceNumber
            $table->timestamp('occurred_at')->index();
            $table->timestamps();

            $table->index(['call_id', 'occurred_at']);
        });

        Schema::create('call_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_id')->constrained('calls')->cascadeOnDelete();

            $table->char('recording_sid', 34)->unique();  // RExxxxxxxx...
            $table->string('status')->nullable();         // queued, in-progress, completed, failed
            $table->unsignedInteger('duration_seconds')->nullable(); // Twilio RecordingDuration
            $table->string('source')->nullable();         // 'dial', 'trunking', etc.
            $table->string('channels')->nullable();       // 'mono' or 'dual'
            $table->text('recording_url')->nullable();    // temp Twilio URL
            $table->string('storage_path')->nullable();   // your S3 key after fetch
            $table->decimal('price', 8, 4)->nullable();
            $table->char('price_unit', 3)->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();

            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['call_id', 'created_at']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
