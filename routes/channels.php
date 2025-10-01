<?php

use App\Models\Conversation;
use App\Models\User;
use App\Support\Channels\Negotiation;
use App\Support\Channels\Subject;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{conversationId}', function (User $user, int $conversationId) {
    $conversation = Conversation::findOrFail($conversationId);

    return $conversation->users()->whereKey($user->id)->exists();
});

Broadcast::channel(Subject::SUBJECT_MOOD_PATTERN, function (User $user, int $subjectId) {
    // Retrieve the subject instance
    $subject = App\Models\Subject::find($subjectId);

    // Check if the subject exists and authorize the user using the 'view' policy
    if (! $subject || $user->cannot('view', $subject)) {
        return false; // Unauthorized
    }

    // Authorized: return the user data or true
    return [
        'id' => $user->id,
        'name' => $user->name,
    ];

});

Broadcast::channel(Negotiation::NEGOTIATION_OBJECTIVE_PATTERN, function (User $user, int $negotiationId) {
    return true;
});

Broadcast::channel(Negotiation::NEGOTIATION_DEMAND_PATTERN, function (User $user, int $negotiationId) {
    return true;
});

Broadcast::channel(Subject::SUBJECT_WARNING_PATTERN, function (User $user, int $subjectId) {
    return true;
});

Broadcast::channel(Subject::SUBJECT_WARRANT_PATTERN, function (User $user, int $subjectId) {
    return true;
});

Broadcast::channel(Subject::SUBJECT_DOCUMENT_PATTERN, function (User $user, int $subjectId) {
    return true;
});

Broadcast::channel(Negotiation::NEGOTIATION_DELIVERY_PLAN_PATTERN, function (User $user, int $negotiationId) {
    return true;
});

Broadcast::channel(Negotiation::NEGOTIATION_RFI_PATTERN, function (User $user, int $negotiationId) {
    return true;
});

Broadcast::channel(Negotiation::NEGOTIATION_DOCUMENT_PATTERN, function (User $user, int $negotiationId) {
    return true;
});

Broadcast::channel(Subject::SUBJECT_ASSESSMENT_PATTERN, function (User $user, int $subjectId) {
    return true;
});

Broadcast::channel(subject::SUBJECT_CONTACT_PATTERN, function (User $user, int $subjectId) {
    return true;
});

Broadcast::channel(Negotiation::NEGOTIATION_HOOK_PATTERN, function (User $user, int $negotiationId) {
    return true;
});

Broadcast::channel(Negotiation::NEGOTIATION_TRIGGERS_PATTERN, function (User $user, int $negotiationId) {
    return true;
});

Broadcast::channel(Subject::SUBJECT_PATTERN, function (User $user, int $subjectId) {
    // Retrieve the subject instance
    $subject = App\Models\Subject::find($subjectId);

    // Check if the subject exists and authorize the user using the 'view' policy
    if (! $subject || $user->cannot('view', $subject)) {
        return false; // Unauthorized
    }

    // Authorized: return the user data or true
    return [
        'id' => $user->id,
        'name' => $user->name,
    ];
});

// Used for conversations and messages.
Broadcast::channel('negotiation.{conversationId}', function (User $user, int $conversationId) {
    // TODO: ensure the user can access this conversation before returning data.
    //    return $user->conversations()->whereKey($conversationId)->exists()
    //        ? ['id' => $user->id, 'name' => $user->name, 'avatar' => $user->avatar_path ?? null]
    //        : false;

    return [
        'id' => $user->id,
        'name' => $user->name,
        'avatar' => $user->avatarUrl(),
    ];
});

Broadcast::channel('private.users.{userId}', function ($user, $userId) {
    return true;
});

Broadcast::channel('private.negotiation.{tenantId}.{negotiationId}', function (User $user, $tenantId, $negotiationId) {
    return true;
});

Broadcast::channel('tenants.{tenantId}.notifications', function ($user, $tenantId) {
    return (int) $user->tenant_id === (int) $tenantId; // adapt to your tenant resolver
});
