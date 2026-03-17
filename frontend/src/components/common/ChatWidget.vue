<template>
  <div class="chat-widget">
    <!-- Floating Chat Button -->
    <button
      class="chat-toggle btn btn-dark rounded-circle shadow-lg d-flex align-items-center justify-content-center"
      @click="toggleChat"
      v-if="!isOpen"
    >
      <i class="bi bi-chat-dots-fill fs-4"></i>
      <span v-if="unreadTotal > 0" class="badge bg-danger rounded-pill position-absolute top-0 end-0 translate-middle-y">
        {{ unreadTotal }}
      </span>
    </button>

    <!-- Chat Window -->
    <div v-if="isOpen" class="chat-window card shadow-lg border-0">
      <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-headset"></i>
          <h6 class="mb-0 fw-semibold">Support Chat</h6>
        </div>
        <button class="btn-close btn-close-white" @click="toggleChat"></button>
      </div>

      <div class="card-body p-0 d-flex flex-column">
        <!-- Messages Area -->
        <div class="messages-container flex-grow-1 p-3" ref="messagesBox">
          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border spinner-border-sm text-muted"></div>
          </div>
          <template v-else>
            <div v-if="messages.length === 0" class="text-center text-muted py-5 small">
              How can we help you today?
            </div>
            <div
              v-for="(msg, index) in messages"
              :key="msg.id || index"
              class="message-wrapper mb-3 d-flex"
              :class="{ 'justify-content-end': String(msg.sender_id) === String(currentUser?.id) }"
            >
              <div
                class="message-bubble p-2 px-3 rounded-4 shadow-sm"
                :class="String(msg.sender_id) === String(currentUser?.id) ? 'bg-dark text-white' : 'bg-light'"
              >
                <div class="message-text">{{ msg.message }}</div>
                <div class="message-time small opacity-75 mt-1" style="font-size: 0.65rem;">
                  {{ formatTime(msg.created_at) }}
                </div>
              </div>
            </div>
          </template>
        </div>

        <!-- Input Area -->
        <div class="card-footer bg-white border-top p-3">
          <form @submit.prevent="send" class="input-group">
            <input
              v-model="newMessage"
              type="text"
              class="form-control border-0 bg-light"
              placeholder="Type a message..."
              :disabled="sending"
            />
            <button class="btn btn-dark" type="submit" :disabled="!newMessage.trim() || sending">
              <i class="bi bi-send-fill"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, onUnmounted, watch } from 'vue'
import axios from 'axios'
import { useAuth } from '@/composables/useAuth'
import echo from '@/utils/echo'

const { user: currentUser, isAuthenticated } = useAuth()
const isOpen = ref(false)
const loading = ref(false)
const messages = ref([])
const newMessage = ref('')
const sending = ref(false)
const adminUser = ref(null)
const messagesBox = ref(null)
const unreadTotal = ref(0)

const toggleChat = async () => {
  if (!isAuthenticated.value) {
    alert('Please login to chat with support.')
    return
  }
  
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    unreadTotal.value = 0
    await fetchAdminAndMessages()
    scrollToBottom()
  }
}

const fetchAdminAndMessages = async () => {
  try {
    loading.value = true
    const adminRes = await axios.get('/chat/admin')
    adminUser.value = adminRes.data
    
    const messagesRes = await axios.get(`/chat/${adminUser.value.id}`)
    messages.value = messagesRes.data
    scrollToBottom()
  } catch (error) {
    console.error('Failed to fetch chat data:', error)
  } finally {
    loading.value = false
  }
}

const send = async () => {
  if (!newMessage.value.trim() || !adminUser.value) return
  
  const text = newMessage.value
  newMessage.value = ''
  sending.value = true
  
  try {
    const res = await axios.post('/chat', {
      receiver_id: adminUser.value.id,
      message: text
    })
    
    // Ensure the message has a consistent format
    const sentMsg = {
      ...res.data,
      created_at: res.data.created_at || new Date().toISOString()
    }
    
    if (!messages.value.find(m => m.id === sentMsg.id)) {
      messages.value.push(sentMsg)
    }
    scrollToBottom()
  } catch (error) {
    console.error('Failed to send message:', error)
  } finally {
    sending.value = false
  }
}

const scrollToBottom = async () => {
  await nextTick()
  if (messagesBox.value) {
    setTimeout(() => {
      messagesBox.value.scrollTo({
        top: messagesBox.value.scrollHeight,
        behavior: 'smooth'
      })
    }, 50)
  }
}

const formatTime = (dateStr) => {
  const date = new Date(dateStr)
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const subscribeToChat = () => {
  if (!isAuthenticated.value || !currentUser.value) return
  
  // Cleanup any existing subscription
  echo.leave(`chat.${currentUser.value.id}`)
  
  console.log('ChatWidget: Subscribing to private channel:', `chat.${currentUser.value.id}`)
  const channel = echo.private(`chat.${currentUser.value.id}`)
  
  channel.on('pusher:subscription_succeeded', () => {
    console.log('ChatWidget: Successfully subscribed to private channel')
  })

  channel.on('pusher:subscription_error', (status) => {
    console.error('ChatWidget: Subscription error:', status)
  })

  channel.listen('.message.sent', (e) => {
    console.log('ChatWidget: New message received via socket:', e)
    // Check if message is already in list to avoid duplicates
    if (!messages.value.find(m => m.id === e.id)) {
      // If the chat is open, add it immediately
      if (isOpen.value) {
        messages.value.push(e)
        scrollToBottom()
      } else if (String(e.sender_id) !== String(currentUser.value.id)) {
        // If chat is closed and it's from the other person, show notification
        unreadTotal.value++
      }
    }
  })
}

// Subscribe on mount or when authentication changes
onMounted(() => {
  subscribeToChat()
})

watch(isAuthenticated, (newVal) => {
  if (newVal) {
    subscribeToChat()
  } else {
    echo.leaveAll()
  }
})

onUnmounted(() => {
  if (currentUser.value) {
    echo.leave(`chat.${currentUser.value.id}`)
  }
})

watch(isOpen, (newVal) => {
  if (newVal) {
    scrollToBottom()
  }
})
</script>

<style scoped>
.chat-widget {
  position: fixed;
  bottom: 1rem;
  right: 1rem;
  z-index: 1050;
}

@media (min-width: 576px) {
  .chat-widget {
    bottom: 2rem;
    right: 2rem;
  }
}

.chat-toggle {
  width: 52px;
  height: 52px;
  position: relative;
}

@media (min-width: 576px) {
  .chat-toggle {
    width: 60px;
    height: 60px;
  }
}

.chat-window {
  width: calc(100vw - 2rem);
  max-width: 350px;
  height: min(85vh, 500px);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  border-radius: 1rem;
}

@media (min-width: 576px) {
  .chat-window {
    width: 350px;
    height: 500px;
  }
}

.card-body {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 0; /* Important for flex child to be able to shrink */
}

.messages-container {
  flex: 1;
  overflow-y: auto;
  background-color: #f8f9fa;
}

.message-bubble {
  max-width: 80%;
  font-size: 0.9rem;
}

.message-bubble.bg-dark {
  border-bottom-right-radius: 2px;
}

.message-bubble.bg-light {
  border-bottom-left-radius: 2px;
}
</style>
