<template>
  <div class="form-card p-4">
    <h5 class="fw-semibold mb-4">Payment Method</h5>
    
    <div class="payment-options mb-4">
      <label class="payment-option" :class="{ selected: modelValue.method === 'card' }">
        <div class="d-flex align-items-center gap-3">
          <input type="radio" v-model="modelValue.method" value="card" />
          <div class="icon-circle">
            <i class="bi bi-credit-card"></i>
          </div>
          <span class="fw-medium">Credit/Debit Card</span>
        </div>
      </label>

      <label class="payment-option" :class="{ selected: modelValue.method === 'stripe' }">
        <div class="d-flex align-items-center gap-3">
          <input type="radio" v-model="modelValue.method" value="stripe" />
          <div class="icon-circle">
            <i class="bi bi-stripe"></i>
          </div>
          <span class="fw-medium">Stripe</span>
        </div>
      </label>

      <label class="payment-option" :class="{ selected: modelValue.method === 'paypal' }">
        <div class="d-flex align-items-center gap-3">
          <input type="radio" v-model="modelValue.method" value="paypal" />
          <div class="icon-circle">
            <i class="bi bi-paypal"></i>
          </div>
          <span class="fw-medium">PayPal</span>
        </div>
      </label>

      <label class="payment-option" :class="{ selected: modelValue.method === 'cod' }">
        <div class="d-flex align-items-center gap-3">
          <input type="radio" v-model="modelValue.method" value="cod" />
          <div class="icon-circle">
            <i class="bi bi-box"></i>
          </div>
          <span class="fw-medium">Cash on Delivery</span>
        </div>
      </label>
    </div>

    <!-- Card Details Form -->
    <div v-if="modelValue.method === 'card'" class="row g-3 mt-2 animate-fade-in">
      <div class="col-12">
        <label class="form-label small fw-medium">Card Number</label>
        <input 
          type="text" 
          class="form-control form-control-lg" 
          placeholder="1234 5678 9012 3456" 
          v-model="modelValue.cardNumber"
          required
        />
      </div>
      <div class="col-12">
        <label class="form-label small fw-medium">Name on Card</label>
        <input 
          type="text" 
          class="form-control form-control-lg" 
          v-model="modelValue.cardName"
          required
        />
      </div>
      <div class="col-md-6">
        <label class="form-label small fw-medium">Expiry Date</label>
        <input 
          type="text" 
          class="form-control form-control-lg" 
          placeholder="MM/YY" 
          v-model="modelValue.expiry"
          required
        />
      </div>
      <div class="col-md-6">
        <label class="form-label small fw-medium">CVV</label>
        <input 
          type="text" 
          class="form-control form-control-lg" 
          placeholder="123" 
          v-model="modelValue.cvv"
          required
        />
      </div>
    </div>

    <div class="d-flex gap-3 mt-5">
      <button 
        type="button" 
        class="btn btn-outline-dark flex-grow-1 py-3 rounded-pill fw-semibold" 
        @click="$emit('back')"
      >
        Back
      </button>
      <button 
        type="button" 
        class="btn btn-dark flex-grow-1 py-3 rounded-pill fw-semibold" 
        @click="$emit('continue')"
      >
        Review Order
      </button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  }
})

defineEmits(['update:modelValue', 'back', 'continue'])
</script>

<style scoped>
.form-card {
  border: 1px solid #e9ecef;
  border-radius: 20px;
  background: #fff;
}

.payment-option {
  border: 1px solid #f1f3f5;
  border-radius: 16px;
  padding: 16px 20px;
  margin-bottom: 12px;
  display: block;
  cursor: pointer;
  transition: all 0.2s ease;
}

.payment-option:hover {
  background: #f8f9fa;
}

.payment-option.selected {
  border-color: #111827;
  background: #fcfcfc;
}

.icon-circle {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: #f1f3f5;
  display: grid;
  place-items: center;
  font-size: 18px;
}

.payment-option.selected .icon-circle {
  background: #111827;
  color: white;
}

.form-control-lg {
  background: #f6f7f9;
  border: none;
  border-radius: 12px;
  padding: 14px 18px;
  font-size: 14px;
}

.form-control-lg:focus {
  background: #fff;
  box-shadow: 0 0 0 2px #111827;
}

.animate-fade-in {
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

input[type="radio"] {
  accent-color: #111827;
  width: 18px;
  height: 18px;
}
</style>
