<template>
  <Teleport to="body">
    <div class="tryon-backdrop" @click.self="close">
      <div class="tryon-modal" role="dialog" aria-modal="true" aria-labelledby="tryon-title">
        <div class="tryon-header">
          <div>
            <h5 id="tryon-title" class="mb-0 fw-semibold">Virtual Try-On</h5>
            <div class="small text-muted">See how {{ productName || 'this item' }} looks on you</div>
          </div>
          <button type="button" class="btn-close" aria-label="Close" @click="close"></button>
        </div>

        <div class="tryon-body">
          <div class="row g-3">
            <!-- Garment -->
            <div class="col-6">
              <div class="small fw-semibold mb-2">Item</div>
              <div class="tryon-frame">
                <img :src="garmentUrl" :alt="productName" />
              </div>
              <div class="small text-muted mt-1">Pick another color or image on the product page to change it.</div>
            </div>

            <!-- Person photo / result -->
            <div class="col-6">
              <div class="small fw-semibold mb-2">{{ resultImage ? 'Result' : 'Your photo' }}</div>

              <div v-if="resultImage" class="tryon-frame">
                <img :src="showOriginal ? personPreview : resultImage" alt="Try-on result" />
              </div>

              <label
                v-else
                class="tryon-frame tryon-upload"
                :class="{ 'has-image': personPreview, disabled: isBusy }"
              >
                <input
                  type="file"
                  accept="image/jpeg,image/png,image/webp"
                  class="d-none"
                  :disabled="isBusy"
                  @change="onFileChange"
                />
                <img v-if="personPreview" :src="personPreview" alt="Your photo" />
                <div v-else class="text-center text-muted px-2">
                  <i class="bi bi-person-bounding-box fs-1 d-block mb-2"></i>
                  <div class="small fw-semibold">Upload your photo</div>
                  <div class="small">JPG, PNG or WEBP</div>
                </div>

                <div v-if="isBusy" class="tryon-overlay">
                  <div class="spinner-border text-light mb-2"></div>
                  <div class="small">{{ statusText }}</div>
                </div>
              </label>

              <div v-if="resultImage" class="form-check form-switch mt-2 small">
                <input id="tryon-compare" v-model="showOriginal" class="form-check-input" type="checkbox" />
                <label class="form-check-label" for="tryon-compare">Show original photo</label>
              </div>
            </div>
          </div>

          <div v-if="errorMessage" class="alert alert-danger small mt-3 mb-0">{{ errorMessage }}</div>

          <ul v-if="!resultImage && !personPreview" class="small text-muted mt-3 mb-0 ps-3">
            <li>Stand facing the camera, in good light</li>
            <li>Show your upper body or full body, with only you in the photo</li>
            <li>Fitted clothes and a plain background give the best results</li>
            <li>Works best with tops: shirts, t-shirts, kurtas and jackets</li>
          </ul>

          <div class="small text-muted mt-3">
            <i class="bi bi-shield-lock me-1"></i>
            Your photo is sent to a free AI model hosted on Hugging Face only to create this preview, and is not saved on our servers. AI previews are approximate.
          </div>
        </div>

        <div class="tryon-footer">
          <template v-if="resultImage">
            <button type="button" class="btn btn-outline-dark" @click="reset">Try another photo</button>
            <a :href="resultImage" download="try-on.png" class="btn btn-dark">
              <i class="bi bi-download me-1"></i>Download
            </a>
          </template>
          <template v-else>
            <button v-if="personPreview && !isBusy" type="button" class="btn btn-outline-dark" @click="reset">
              Change photo
            </button>
            <button type="button" class="btn btn-dark" :disabled="!personFile || isBusy" @click="generate">
              {{ isBusy ? 'Generating...' : 'Generate try-on' }}
            </button>
          </template>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  productId: { type: [Number, String], required: true },
  productName: { type: String, default: '' },
  // Image path as stored in the database (sent to the backend)
  garmentPath: { type: String, required: true },
  // Full URL of the same image (for display)
  garmentUrl: { type: String, required: true },
})

const emit = defineEmits(['close'])

const POLL_INTERVAL_MS = 2500
// The free model can queue behind other users, so allow plenty of time.
const MAX_WAIT_MS = 300000
const MAX_DIMENSION = 1600

const personFile = ref(null)
const personPreview = ref('')
const resultImage = ref('')
const showOriginal = ref(false)
const errorMessage = ref('')
const phase = ref('idle') // idle | uploading | processing

// Incremented on every new run / close so stale poll responses are ignored.
let runToken = 0
let pollTimer = null

const isBusy = computed(() => phase.value !== 'idle')
const statusText = computed(() =>
  phase.value === 'uploading' ? 'Uploading photo...' : 'Creating your look... (usually 30-90 seconds)'
)

const clearPreview = () => {
  if (personPreview.value) URL.revokeObjectURL(personPreview.value)
  personPreview.value = ''
}

/**
 * Downscale large phone photos before upload. The AI works at ~864x1296,
 * so anything bigger only slows the upload down.
 */
const shrinkImage = async (file) => {
  try {
    const bitmap = await createImageBitmap(file)
    const scale = Math.min(1, MAX_DIMENSION / Math.max(bitmap.width, bitmap.height))
    const canvas = document.createElement('canvas')
    canvas.width = Math.round(bitmap.width * scale)
    canvas.height = Math.round(bitmap.height * scale)
    canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height)
    bitmap.close?.()
    const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.9))
    return blob ? new File([blob], 'photo.jpg', { type: 'image/jpeg' }) : file
  } catch {
    return file
  }
}

const onFileChange = async (event) => {
  const file = event.target.files?.[0]
  event.target.value = ''
  if (!file) return

  if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
    errorMessage.value = 'Please choose a JPG, PNG or WEBP image.'
    return
  }

  errorMessage.value = ''
  clearPreview()
  personFile.value = await shrinkImage(file)
  personPreview.value = URL.createObjectURL(personFile.value)
}

const stopPolling = () => {
  if (pollTimer) {
    clearTimeout(pollTimer)
    pollTimer = null
  }
}

const fail = (message) => {
  stopPolling()
  phase.value = 'idle'
  errorMessage.value = message
}

const poll = (predictionId, token, startedAt) => {
  pollTimer = setTimeout(async () => {
    if (token !== runToken) return

    if (Date.now() - startedAt > MAX_WAIT_MS) {
      fail('This is taking longer than expected. Please try again.')
      return
    }

    try {
      const { data } = await axios.get(`/try-on/${encodeURIComponent(predictionId)}`)
      if (token !== runToken) return

      if (data.status === 'completed' && data.image) {
        resultImage.value = data.image
        phase.value = 'idle'
      } else if (data.status === 'failed') {
        fail(data.message || 'The try-on could not be generated.')
      } else {
        poll(predictionId, token, startedAt)
      }
    } catch (err) {
      if (token !== runToken) return
      fail(err.response?.data?.message || 'Could not check the try-on status.')
    }
  }, POLL_INTERVAL_MS)
}

const generate = async () => {
  if (!personFile.value || isBusy.value) return

  const token = ++runToken
  errorMessage.value = ''
  resultImage.value = ''
  showOriginal.value = false
  phase.value = 'uploading'

  const formData = new FormData()
  formData.append('product_id', props.productId)
  formData.append('garment_image', props.garmentPath)
  formData.append('person_image', personFile.value)

  try {
    const { data } = await axios.post('/try-on', formData)
    if (token !== runToken) return
    phase.value = 'processing'
    poll(data.id, token, Date.now())
  } catch (err) {
    if (token !== runToken) return
    const response = err.response
    let message = response?.data?.message || 'Could not start the try-on. Please try again.'
    if (response?.status === 429) message = 'You have made too many try-ons. Please wait a minute.'
    if (response?.status === 422) {
      const errors = response.data?.errors || {}
      message = Object.values(errors).flat()[0] || message
    }
    fail(message)
  }
}

const reset = () => {
  runToken++
  stopPolling()
  phase.value = 'idle'
  errorMessage.value = ''
  resultImage.value = ''
  showOriginal.value = false
  personFile.value = null
  clearPreview()
}

const close = () => {
  reset()
  emit('close')
}

const onKeydown = (event) => {
  if (event.key === 'Escape') close()
}

onMounted(() => {
  document.addEventListener('keydown', onKeydown)
  document.body.style.overflow = 'hidden'
})

onUnmounted(() => {
  runToken++
  stopPolling()
  clearPreview()
  document.removeEventListener('keydown', onKeydown)
  document.body.style.overflow = ''
})
</script>

<style scoped>
.tryon-backdrop {
  position: fixed;
  inset: 0;
  z-index: 1060;
  background: rgba(0, 0, 0, 0.55);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
}

.tryon-modal {
  background: #fff;
  border-radius: 16px;
  width: 100%;
  max-width: 680px;
  max-height: calc(100vh - 32px);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
}

.tryon-header,
.tryon-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 16px 20px;
}

.tryon-header {
  border-bottom: 1px solid #eee;
}

.tryon-footer {
  border-top: 1px solid #eee;
  justify-content: flex-end;
}

.tryon-body {
  padding: 20px;
  overflow-y: auto;
}

.tryon-frame {
  position: relative;
  aspect-ratio: 2 / 3;
  border-radius: 12px;
  overflow: hidden;
  background: #f8f9fa;
  border: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0;
}

.tryon-frame img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.tryon-upload {
  cursor: pointer;
  border: 2px dashed #ced4da;
  transition: border-color 0.2s ease, background-color 0.2s ease;
}

.tryon-upload:hover:not(.disabled) {
  border-color: #212529;
  background: #f1f3f5;
}

.tryon-upload.has-image {
  border-style: solid;
}

.tryon-upload.disabled {
  cursor: progress;
}

.tryon-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  color: #fff;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 12px;
}

@media (max-width: 576px) {
  .tryon-body {
    padding: 16px;
  }

  .tryon-footer .btn {
    flex: 1;
  }
}
</style>
