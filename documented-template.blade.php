<!-- 
  Negotiation Chat Template
  
  This template renders the chat interface for negotiations, including:
  - Tabs for switching between public, private, and group chats
  - Sidebar with conversation lists
  - Chat area with messages
  - Message input with whisper functionality
  - New conversation modal
  
  The template uses Alpine.js for frontend interactivity and Laravel Echo for real-time updates.
-->

<div class="h-full bg-white dark:bg-dark-800 rounded-lg p-2 shadow-sm">
	@php use Illuminate\Support\Js; @endphp
	<div
			class="flex flex-col h-full justify-between"
			x-data="presenceStore({ initialId: {{ Js::from($selectedConversationId) }} })"
			x-init="init()"
	>
		<!-- Header with tabs for navigation between public, private, and group chats -->
		<div class="flex items-center justify-between gap-4 border-b border-gray-200 dark:border-dark-400 pb-2">
			<!-- Tab navigation section -->
		</div>

		<!-- Main content area with sidebar and chat -->
		<div class="flex h-full overflow-hidden">
			<!-- Sidebar with conversation list -->
			<div class="w-1/4 border-r border-gray-200 dark:border-dark-400 pr-2 overflow-y-auto hidden md:block">
				<!-- Public chat section -->
				<!-- Private chats section -->
				<!-- Group chats section -->
				<!-- Online users section -->
			</div>

			<!-- Chat area with messages and input -->
			<div class="flex-1 flex flex-col overflow-hidden">
				<!-- Chat header -->
				<!-- Messages container -->
				<!-- Message input area -->
			</div>
		</div>
	</div>

	<!-- New conversation modal -->
	<!-- JavaScript for real-time functionality -->
</div>