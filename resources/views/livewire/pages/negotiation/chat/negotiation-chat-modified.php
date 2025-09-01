<?php

// This file contains the changes needed to implement message encryption and decryption
// in the negotiation-chat.blade.php file

// 1. In the sendMessage() method (around line 322), change:
// 'content' => $content,
// to:
// 'content' => encrypt($content),

// 2. In the message display section (around line 869), change:
// $displayContent = $message->content;
// to:
// $displayContent = decrypt($message->content);

// These changes will encrypt the message content before saving it and decrypt it when displaying.
// The special prefixes ([URGENT], [EMERGENCY]) will be preserved because they are added before encryption
// and the checks for them are performed after decryption.
