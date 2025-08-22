@push('scripts')
	<!-- 
	  Chat Event Handling Script
	  
	  Handles DOM events and real-time updates for the chat interface:
	  - Scrolls to bottom when new messages arrive
	  - Handles message received events
	  - Shows notifications for new messages
	-->
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			// Listen for Livewire DOM updates and trigger the scroll event
			Livewire.hook('message.processed', (message, component) => {
				// Dispatch both window-level and component-level events
				window.dispatchEvent(new Event('new-message'))

				// Find the chat container component and dispatch the event directly to it
				const chatContainer = document.getElementById('chatContainer')
				if (chatContainer) {
					chatContainer.dispatchEvent(new Event('new-message'))

					// Add a small delay to ensure DOM is fully rendered before scrolling
					setTimeout(() => {
						if (chatContainer.scrollToBottom) {
							chatContainer.scrollToBottom()
						} else {
							chatContainer.scrollTop = chatContainer.scrollHeight
						}
					}, 50)
				}
			})

			// Listen specifically for received messages
			Livewire.on('message-received', () => {
				console.log('Message received event triggered')

				// Dispatch both window-level and component-level events
				window.dispatchEvent(new Event('message-received'))

				// Find the chat container and dispatch the event directly to it
				const chatContainer = document.getElementById('chatContainer')
				if (chatContainer) {
					chatContainer.dispatchEvent(new Event('message-received'))

					// Add a longer delay for received messages to ensure DOM is fully updated
					setTimeout(() => {
						console.log('Scrolling after message received')
						if (chatContainer.scrollToBottom) {
							chatContainer.scrollToBottom()
						} else {
							chatContainer.scrollTop = chatContainer.scrollHeight
						}
					}, 100)
				}
			})

			// Listen for notification events
			Livewire.on('notify', (data) => {
				console.log('Notification received:', data)

				// Check if browser notifications are supported
				if ('Notification' in window) {
					// Request permission if not already granted
					if (Notification.permission !== 'granted') {
						Notification.requestPermission()
					}

					// Show notification if permission is granted
					if (Notification.permission === 'granted') {
						const notification = new Notification(data.title, {
							body: data.message,
							icon: '/favicon.ico' // Use your site's favicon or another appropriate icon
						})

						// Close the notification after 5 seconds
						setTimeout(() => {
							notification.close()
						}, 5000)

						// Focus the window and navigate to the conversation when the notification is clicked
						notification.onclick = function () {
							window.focus()
							// You could add code here to navigate to the specific conversation
							// if you pass the conversation ID in the notification data
						}
					}
				}
			})
		})
	</script>

	<!-- 
	  Presence Store Module
	  
	  Alpine.js module that manages real-time presence and messaging:
	  - Tracks online users in the current conversation
	  - Handles joining and leaving conversations
	  - Manages message subscriptions
	  - Provides typing indicators
	-->
	<script type="module">
		window.presenceStore = ({ initialId, currentUserId }) => ({
			currentUserId: String(currentUserId ?? ''),
			currentId: null,
			channel: null,
			members: {},      // { [id]: {id,name,avatar} }
			_lastWhisper: 0,
			_msgChan: null,

			// Initialize the presence store
			init () {
				if (!window.Echo) {
					console.warn('Echo not initialized')
					return
				}
				if (initialId) {
					this.join(initialId)
					this.subscribeToMessages(initialId)   // ✅ start per-thread message stream
					$wire.openConversation(initialId)     // ✅ clear/persist unread for the initial thread
				}

				window.addEventListener('conversation-changed', (e) => {
					const { oldId, newId } = e.detail || {}
					this.switchConversation(oldId, newId)
				})

				window.addEventListener('beforeunload', () => this.leave())
			},

			// Join a conversation presence channel
			join (id) {
				this.currentId = id
				this.channel = window.Echo.join(`negotiation.${id}`)
					.here((users) => {
						const map = {}
						users.forEach(u => { map[String(u.id)] = u })
						this.members = map
					})
					.joining((u) => { this.members[String(u.id)] = u })
					.leaving((u) => { delete this.members[String(u.id)] })
					.listenForWhisper('typing', (payload) => {
						// handle typing indicator if you want
					})
			},

			// Leave the current conversation
			leave () {
				try { if (this.channel) this.channel.leave() } catch {}
				try { if (this._msgChan) this._msgChan.stopListening('.MessageSent') } catch {}
				this.channel = null
				this._msgChan = null
				this.members = {}
				this.currentId = null
			},

			// Subscribe to messages for a specific conversation
			subscribeToMessages (conversationId) {
				try { if (this._msgChan) this._msgChan.stopListening('.MessageSent') } catch {}
				this._msgChan = window.Echo.private(`conversation.${conversationId}`)
					.listen('.MessageSent', (e) => {
						window.dispatchEvent(new CustomEvent('echo-message-sent', { detail: e }))
					})
			},

			// Switch from one conversation to another
			switchConversation (oldId, newId) {
				if (oldId) { try { window.Echo.leave(`negotiation.${oldId}`) } catch {} }
				this.channel = null
				this.members = {}
				if (newId) {
					this.join(newId)
					this.subscribeToMessages(newId)   // ✅ subscribe to the new thread
					$wire.openConversation(newId)     // ✅ mark as read & reset local badge
				}
			},

			// Handle input events for typing indicators
			onInput () {
				if (!this.channel) return
				if (Date.now() - this._lastWhisper < 800) return
				this._lastWhisper = Date.now()
				this.channel.whisper('typing', { id: this.currentUserId })
			},
		})
	</script>
@endpush