<?php

// This script tests the group chat creation functionality to verify that our fix works correctly

// Import necessary classes
use App\Services\Conversation\ConversationCreationService;
use App\Models\User;
use App\Models\Conversation;

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the current user (assuming user ID 1 is the creator)
$creator = User::find(1);
if (!$creator) {
    echo "Error: Creator user not found\n";
    exit(1);
}

echo "Testing group chat creation with creator included in selected users\n";
echo "Creator ID: {$creator->id}\n";

// Simulate the data for creating a group chat
$conversationData = [
    'tenant_id' => $creator->tenant_id,
    'created_by' => $creator->id,
    'negotiation_id' => 1, // Assuming negotiation ID 1 exists
    'type' => 'group',
    'name' => 'Test Group Chat',
];

// Include the creator in the selected users array to test our fix
$selectedUserIds = [$creator->id, 2, 3]; // Assuming user IDs 2 and 3 exist

echo "Selected user IDs: " . implode(', ', $selectedUserIds) . "\n";

try {
    // Create the conversation with users
    $conversation = app(ConversationCreationService::class)->createConversationWithUsers(
        $conversationData,
        $selectedUserIds
    );

    echo "Group chat created successfully!\n";
    echo "Conversation ID: {$conversation->id}\n";

    // Verify that users were added correctly
    $attachedUsers = $conversation->users()->pluck('user_id')->toArray();
    echo "Attached user IDs: " . implode(', ', $attachedUsers) . "\n";

    // Check if the creator appears only once
    $creatorCount = array_count_values($attachedUsers)[$creator->id] ?? 0;
    echo "Creator appears {$creatorCount} time(s) in the conversation\n";

    if ($creatorCount === 1) {
        echo "SUCCESS: Creator was added only once to the conversation\n";
    } else {
        echo "ERROR: Creator was added {$creatorCount} times to the conversation\n";
    }
} catch (\Exception $e) {
    echo "Error creating group chat: " . $e->getMessage() . "\n";
}
