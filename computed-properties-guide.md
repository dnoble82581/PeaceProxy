# Guide to Using Computed Properties in Livewire

## Issues Fixed

We resolved errors related to computed properties in the negotiation-chat.blade.php file:

1. First issue:
```
Undefined variable $publicConversation (View: /Users/dustynoble/Herd/PeaceProxyPro_2/resources/views/livewire/pages/negotiation/chat/negotiation-chat.blade.php)
```

2. Second issue: Potential errors with `$currentConversation` access, which was being accessed incorrectly in both PHP code and Blade templates.

3. Third issue: Potential errors with `$conversationUsers` access, which was being accessed directly in Blade templates.

4. Fourth issue: Potential errors with `$messages` access, which was being accessed directly in Blade templates.

5. Fifth issue: Potential errors with `$privateConversations` access, which was being accessed directly in Blade templates.

6. Sixth issue: Potential errors with `$availableUsers` access, which was being accessed directly in Blade templates.

7. Seventh issue: Potential errors with `$groupConversations` access, which was being accessed directly in Blade templates.

## Root Cause

The errors occurred because the component was trying to access computed properties incorrectly:

- `publicConversation()`, `currentConversation()`, `conversationUsers()`, `messages()`, `privateConversations()`, `availableUsers()`, and `groupConversations()` are all defined as computed property methods (with the `#[Computed]` attribute)
- The code was trying to access them directly without parentheses in PHP code
- The code was also trying to access them directly in Blade templates

In Livewire components, there's a distinction between:
1. Public properties (directly accessible in templates)
2. Computed properties (methods that need to be called properly)

## The Fixes

We implemented fixes for both computed properties:

### Fix for $publicConversation

1. In the PHP code section, we changed:
   ```php
   // Before (incorrect):
   if ($this->activeTab === 'public') {
       return $this->publicConversation;
   }
   
   // After (correct):
   if ($this->activeTab === 'public') {
       return $this->publicConversation();
   }
   ```

2. In the Blade template section, we changed:
   ```php
   // Before (incorrect):
   @if($publicConversation)
       <div wire:click="selectConversation({{ $publicConversation->id }})">
           {{ $publicConversation->name }}
           {{ $publicConversation->activeUsers->count() }}
       </div>
   @endif
   
   // After (correct):
   @php
       $publicConv = $this->publicConversation();
   @endphp
   @if($publicConv)
       <div wire:click="selectConversation({{ $publicConv->id }})">
           {{ $publicConv->name }}
           {{ $publicConv->activeUsers->count() }}
       </div>
   @endif
   ```

### Fix for $currentConversation

1. In the PHP code section, we added parentheses to all instances:
   ```php
   // Before (incorrect):
   if (!$this->currentConversation) {
       return new Collection();
   }
   
   return $this->currentConversation->messages();
   
   // After (correct):
   if (!$this->currentConversation()) {
       return new Collection();
   }
   
   return $this->currentConversation()->messages();
   ```

2. In the Blade template section, we used the local variable approach:
   ```php
   // Before (incorrect):
   <div class="flex-1 flex flex-col overflow-hidden {{ $currentConversation ? '' : 'items-center justify-center' }}">
   @if($currentConversation)
       <!-- Content -->
   @endif
   
   // After (correct):
   @php
       $currentConv = $this->currentConversation();
   @endphp
   <div class="flex-1 flex flex-col overflow-hidden {{ $currentConv ? '' : 'items-center justify-center' }}">
   @if($currentConv)
       <!-- Content -->
   @endif
   ```

### Fix for $conversationUsers

In the Blade template section, we used the local variable approach:
   ```php
   // Before (incorrect):
   <p class="text-sm text-gray-500 dark:text-dark-300">
       {{ $conversationUsers->count() }} participants
   </p>
   
   @if($isWhisper && $whisperToUserId)
       @php
           $whisperUser = $conversationUsers->firstWhere('id', $whisperToUserId);
       @endphp
       <!-- Content -->
   @endif
   
   @if($conversationUsers->count() > 1 && ($currentConv->type === 'public' || $currentConv->type === 'group'))
       <!-- Content -->
       @foreach($conversationUsers->where('id', '!=', auth()->id()) as $user)
           <!-- Content -->
       @endforeach
   @endif
   
   // After (correct):
   @php
       $convUsers = $this->conversationUsers();
   @endphp
   <p class="text-sm text-gray-500 dark:text-dark-300">
       {{ $convUsers->count() }} participants
   </p>
   
   @if($isWhisper && $whisperToUserId)
       @php
           $whisperUser = $convUsers->firstWhere('id', $whisperToUserId);
       @endphp
       <!-- Content -->
   @endif
   
   @if($convUsers->count() > 1 && ($currentConv->type === 'public' || $currentConv->type === 'group'))
       <!-- Content -->
       @foreach($convUsers->where('id', '!=', auth()->id()) as $user)
           <!-- Content -->
       @endforeach
   @endif
   ```

### Fix for $messages

In the Blade template section, we used the local variable approach:
   ```php
   // Before (incorrect):
   <div class="flex-1 overflow-y-auto p-4 space-y-4">
       @foreach($messages as $message)
           <!-- Content -->
       @endforeach
   </div>
   
   // After (correct):
   <div class="flex-1 overflow-y-auto p-4 space-y-4">
       @php
           $msgs = $this->messages();
       @endphp
       @foreach($msgs as $message)
           <!-- Content -->
       @endforeach
   </div>
   ```

### Fix for $privateConversations

In the Blade template section, we used the local variable approach:
   ```php
   // Before (incorrect):
   <div class="py-2">
       <div class="font-medium text-sm text-gray-500 dark:text-dark-300 mb-2">Private Chats</div>
       @if($privateConversations->count() > 0)
           @foreach($privateConversations as $conversation)
               <!-- Content -->
           @endforeach
       @endif
   </div>
   
   // After (correct):
   <div class="py-2">
       <div class="font-medium text-sm text-gray-500 dark:text-dark-300 mb-2">Private Chats</div>
       @php
           $privateConvs = $this->privateConversations();
       @endphp
       @if($privateConvs->count() > 0)
           @foreach($privateConvs as $conversation)
               <!-- Content -->
           @endforeach
       @endif
   </div>
   ```

### Fix for $availableUsers

In the Blade template section, we used the local variable approach:
   ```php
   // Before (incorrect):
   <div class="max-h-48 overflow-y-auto border border-gray-300 dark:border-dark-400 rounded-md p-2">
       @foreach($availableUsers as $user)
           <label class="flex items-center p-2 hover:bg-gray-100 dark:hover:bg-dark-600 rounded">
               <!-- Content -->
           </label>
       @endforeach
   </div>
   
   // After (correct):
   <div class="max-h-48 overflow-y-auto border border-gray-300 dark:border-dark-400 rounded-md p-2">
       @php
           $availUsers = $this->availableUsers();
       @endphp
       @foreach($availUsers as $user)
           <label class="flex items-center p-2 hover:bg-gray-100 dark:hover:bg-dark-600 rounded">
               <!-- Content -->
           </label>
       @endforeach
   </div>
   ```

### Fix for $groupConversations

In the Blade template section, we used the local variable approach:
   ```php
   // Before (incorrect):
   <div class="py-2">
       <div class="font-medium text-sm text-gray-500 dark:text-dark-300 mb-2">Group Chats</div>
       @if($groupConversations->count() > 0)
           @foreach($groupConversations as $conversation)
               <!-- Content -->
           @endforeach
       @endif
   </div>
   
   // After (correct):
   <div class="py-2">
       <div class="font-medium text-sm text-gray-500 dark:text-dark-300 mb-2">Group Chats</div>
       @php
           $groupConvs = $this->groupConversations();
       @endphp
       @if($groupConvs->count() > 0)
           @foreach($groupConvs as $conversation)
               <!-- Content -->
           @endforeach
       @endif
   </div>
   ```

## Best Practices for Computed Properties in Livewire

### 1. Accessing Computed Properties in PHP Code

When accessing computed properties within PHP code (including other methods in the same component), always use parentheses:

```php
// Correct:
$result = $this->computedProperty();

// Incorrect:
$result = $this->computedProperty;
```

### 2. Accessing Computed Properties in Blade Templates

There are two approaches:

#### Option 1: Use a local variable (recommended for complex templates)

```php
@php
    $result = $this->computedProperty();
@endphp
{{ $result }}
```

This approach is clearer and prevents confusion, especially in complex templates.

#### Option 2: Use the Livewire directive (works in some cases)

In some Livewire setups, you can access computed properties directly in templates:

```php
{{ $computedProperty }}
```

However, this doesn't always work reliably, especially in Livewire Volt components, and can lead to the "undefined variable" error we encountered.

### 3. When to Use Computed Properties

Use computed properties when:
- You need to calculate a value based on other properties
- The calculation might be performed multiple times in a template
- You want to cache the result of an expensive operation

### 4. Debugging Computed Properties

If you encounter "undefined variable" errors with computed properties:
1. Check if you're using parentheses in PHP code
2. Try using a local variable in the template
3. Verify that the computed property is properly defined with the `#[Computed]` attribute

## Example of a Well-Defined Computed Property

```php
#[Computed]
public function totalPrice(): float
{
    return $this->quantity * $this->unitPrice;
}
```

Then access it in PHP code:
```php
$total = $this->totalPrice();
```

And in templates (using the local variable approach):
```php
@php
    $total = $this->totalPrice();
@endphp
<div>Total: ${{ number_format($total, 2) }}</div>
```

By following these best practices, you can avoid "undefined variable" errors when working with computed properties in Livewire components.