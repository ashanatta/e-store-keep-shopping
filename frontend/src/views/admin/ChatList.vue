<template>
  <div class="admin-chat-container d-flex h-100">
    <!-- Chat Sidebar (Active Users) -->
    <div class="chat-sidebar border-end bg-white d-flex flex-column" style="width: 300px;">
      <div class="p-3 border-bottom bg-light">
        <h5 class="mb-0 fw-bold">Customer Chats</h5>
      </div>
      
      <div class="active-chats flex-grow-1 overflow-auto">
        <div v-if="loadingUsers" class="text-center py-5">
          <div class="spinner-border spinner-border-sm text-muted"></div>
        </div>
        <div v-else>
          <div
            v-for="chat in activeChats"
            :key="chat.id"
            class="chat-user-item p-3 border-bottom cursor-pointer transition-all"
            :class="{ 'active bg-dark text-white': selectedUser?.id === chat.id }"
            @click="selectUser(chat)"
          >
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="fw-semibold">{{ chat.name }}</span>
              <span class="badge bg-danger rounded-pill" v-if="chat.unread_count > 0">
                {{ chat.unread_count }}
              </span>
            </div>
            <div class="small opacity-75 text-truncate" :class="{ 'text-muted': selectedUser?.id !== chat.id }">
              {{ chat.last_message || 'No messages yet' }}
            </div>
            <div class="small opacity-50 mt-1" style="font-size: 0.7rem;">
              {{ formatTime(chat.last_message_time) }}
            </div>
          </div>
          
          <div v-if="activeChats.length === 0" class="text-center py-5 text-muted">
            <i class="bi bi-chat-left-text fs-1 mb-3 d-block opacity-25"></i>
            No active conversations
          </div>
        </div>
      </div>
    </div>

    <!-- Chat Main Area -->
    <div class="chat-main flex-grow-1 bg-light d-flex flex-column h-100 position-relative">
      <div v-if="!selectedUser" class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
        <i class="bi bi-chat-dots fs-1 mb-3 opacity-25"></i>
        <h5>Select a customer to start chatting</h5>
      </div>
      
      <template v-else>
        <!-- Chat Header -->
        <div class="chat-header p-3 border-bottom bg-white d-flex align-items-center gap-3">
          <div class="avatar bg-dark text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
            {{ selectedUser.name.charAt(0).toUpperCase() }}
          </div>
          <div>
            <h6 class="mb-0 fw-bold">{{ selectedUser.name }}</h6>
            <span class="small text-success">Online</span>
          </div>
        </div>

        <!-- Messages Area -->
        <div class="chat-messages p-4 flex-grow-1 overflow-auto" ref="messagesContainer">
          <div v-if="loadingMessages" class="text-center py-5">
            <div class="spinner-border spinner-border-sm text-muted"></div>
          </div>
          <div v-else>
            <div
              v-for="(msg, index) in messages"
              :key="msg.id || index"
              class="message-wrapper mb-3 d-flex"
              :class="{ 'justify-content-end': String(msg.sender_id) === String(currentUser.id) }"
            >
              <div
                class="message-bubble p-2 px-3 rounded-4 shadow-sm"
                :class="String(msg.sender_id) === String(currentUser.id) ? 'bg-dark text-white' : 'bg-white'"
              >
                <div class="message-text">{{ msg.message }}</div>
                <div class="message-time small opacity-50 mt-1 text-end" style="font-size: 0.65rem;">
                  {{ formatTime(msg.created_at) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Input Area -->
        <div class="chat-footer p-3 border-top bg-white">
          <form @submit.prevent="send" class="input-group">
            <input
              v-model="newMessage"
              type="text"
              class="form-control border-0 bg-light p-3"
              placeholder="Type your reply..."
              :disabled="sending"
            />
            <button class="btn btn-dark px-4" type="submit" :disabled="!newMessage.trim() || sending">
              <i class="bi bi-send-fill"></i>
            </button>
          </form>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue'
import axios from 'axios'
import { useAuth } from '@/composables/useAuth'
import echo from '@/utils/echo'

const { user: currentUser } = useAuth()
const activeChats = ref([])
const selectedUser = ref(null)
const messages = ref([])
const newMessage = ref('')
const loadingUsers = ref(false)
const loadingMessages = ref(false)
const sending = ref(false)
const messagesContainer = ref(null)

const fetchActiveChats = async () => {
  try {
    loadingUsers.value = true
    const res = await axios.get('/chat/active')
    activeChats.value = res.data
  } catch (error) {
    console.error('Failed to fetch active chats:', error)
  } finally {
    loadingUsers.value = false
  }
}

const selectUser = async (user) => {
  selectedUser.value = user
  try {
    loadingMessages.value = true
    const res = await axios.get(`/chat/${user.id}`)
    messages.value = res.data
    // Update activeChats unread count
    const chatIndex = activeChats.value.findIndex(c => c.id === user.id)
    if (chatIndex !== -1) {
      activeChats.value[chatIndex].unread_count = 0
    }
    scrollToBottom()
  } catch (error) {
    console.error('Failed to fetch messages:', error)
  } finally {
    loadingMessages.value = false
  }
}

const send = async () => {
  if (!newMessage.value.trim() || !selectedUser.value) return
  
  const text = newMessage.value
  newMessage.value = ''
  sending.value = true
  
  try {
    const res = await axios.post('/chat', {
      receiver_id: selectedUser.value.id,
      message: text
    })
    
    const sentMsg = {
      ...res.data,
      created_at: res.data.created_at || new Date().toISOString()
    }
    
    if (!messages.value.find(m => m.id === sentMsg.id)) {
      messages.value.push(sentMsg)
    }
    
    // Update last message in sidebar
    const chatIndex = activeChats.value.findIndex(c => c.id === selectedUser.value.id)
    if (chatIndex !== -1) {
      activeChats.value[chatIndex].last_message = text
      activeChats.value[chatIndex].last_message_time = res.data.created_at
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
  if (messagesContainer.value) {
    setTimeout(() => {
      messagesContainer.value.scrollTo({
        top: messagesContainer.value.scrollHeight,
        behavior: 'smooth'
      })
    }, 50)
  }
}

const formatTime = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

onMounted(() => {
  fetchActiveChats()
  
  // Listen for incoming messages on admin's private channel
  console.log('AdminChat: Subscribing to private channel:', `chat.${currentUser.value.id}`)
  const channel = echo.private(`chat.${currentUser.value.id}`)
  
  channel.on('pusher:subscription_succeeded', () => {
    console.log('AdminChat: Successfully subscribed to private channel')
  })

  channel.on('pusher:subscription_error', (status) => {
    console.error('AdminChat: Subscription error:', status)
  })

  channel.listen('.message.sent', (e) => {
    console.log('AdminChat: New message received via socket:', e)
    
    // Check if message is already in list to avoid duplicates
    if (!messages.value.find(m => m.id === e.id)) {
      // If message is from the currently selected user
      if (selectedUser.value && e.sender_id === selectedUser.value.id) {
        messages.value.push(e)
        scrollToBottom()
      }
      
      // Update sidebar
      const chatIndex = activeChats.value.findIndex(c => c.id === e.sender_id || c.id === e.receiver_id)
      if (chatIndex !== -1) {
        activeChats.value[chatIndex].last_message = e.message
        activeChats.value[chatIndex].last_message_time = e.created_at
        if (!selectedUser.value || selectedUser.value.id !== e.sender_id) {
          activeChats.value[chatIndex].unread_count++
        }
      } else {
        // New chat from a user not in sidebar
        fetchActiveChats()
      }
    }
  })
})

onUnmounted(() => {
  if (currentUser.value) {
    echo.leave(`chat.${currentUser.value.id}`)
  }
})

watch(selectedUser, () => {
  scrollToBottom()
})
</script>

<style scoped>
.admin-chat-container {
  height: calc(100vh - 100px);
}

.chat-main {
  min-height: 0;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  min-height: 0;
}

.chat-user-item {
  cursor: pointer;
  transition: all 0.2s ease;
}

.chat-user-item:hover {
  background-color: #f1f1f1;
}

.chat-user-item.active {
  background-color: #212529;
}

.message-bubble {
  max-width: 70%;
  font-size: 0.95rem;
}

.avatar {
  font-weight: bold;
  font-size: 1.2rem;
}

.transition-all {
  transition: all 0.2s ease;
}
</style>
