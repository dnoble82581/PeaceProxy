<?php

use App\Models\Conversation;
use App\Models\Subject;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{conversationId}', function (User $user, int $conversationId) {
    $conversation = Conversation::findOrFail($conversationId);

    return $conversation->users()->whereKey($user->id)->exists();
});

Broadcast::channel('tenants.{tenant}.subjects.{subject}.moods', function ($user, Tenant $tenant, Subject $subject) {
    // Must belong to the tenant AND be allowed to view this subject
    return (int) $user->tenant_id === (int) $tenant->id
        && (int) $subject->tenant_id === (int) $tenant->id
        && $user->can('view', $subject);
});

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
