<template>
  <div class="form-card p-4">
    <h5 class="fw-semibold mb-4">Payment Method</h5>
    <div
      class="payment-option"
      :class="{ selected: modelValue.method === 'card' }"
      @click="modelValue.method = 'card'"
    >
      <div class="d-flex align-items-center gap-2">
        <input type="radio" :checked="modelValue.method === 'card'" />
        <i class="bi bi-credit-card"></i>
        <span>Credit/Debit Card</span>
      </div>
    </div>
    <div
      class="payment-option"
      :class="{ selected: modelValue.method === 'stripe' }"
      @click="modelValue.method = 'stripe'"
    >
      <div class="d-flex align-items-center gap-2">
        <input type="radio" :checked="modelValue.method === 'stripe'" />
        <i class="bi bi-stripe"></i>
        <span>Stripe</span>
      </div>
    </div>
    <div
      class="payment-option"
      :class="{ selected: modelValue.method === 'paypal' }"
      @click="modelValue.method = 'paypal'"
    >
      <div class="d-flex align-items-center gap-2">
        <input type="radio" :checked="modelValue.method === 'paypal'" />
        <i class="bi bi-paypal"></i>
        <span>PayPal</span>
      </div>
    </div>
    <div
      class="payment-option"
      :class="{ selected: modelValue.method === 'cod' }"
      @click="modelValue.method = 'cod'"
    >
      <div class="d-flex align-items-center gap-2">
        <input type="radio" :checked="modelValue.method === 'cod'" />
        <i class="bi bi-box"></i>
        <span>Cash on Delivery</span>
      </div>
    </div>

    <div v-if="modelValue.method === 'stripe'" class="mt-3">
      <div class="stripe-container p-3 rounded-3 border bg-light text-center">
        <i class="bi bi-credit-card fs-1 text-muted mb-2 d-block"></i>
        <p class="mb-0 fw-semibold">Stripe Secure Payment</p>
        <p class="text-muted small">You'll enter your card details in the next step.</p>
      </div>
    </div>

    <div v-if="modelValue.method === 'card'" class="row g-3 mt-3">
      <div class="col-12">
        <label class="form-label small">Card Number</label>
        <input
          v-model="modelValue.cardNumber"
          type="text"
          class="form-control form-control-lg"
          placeholder="1234 5678 9012 3456"
        />
      </div>
      <div class="col-12">
        <label class="form-label small">Name on Card</label>
        <input
          v-model="modelValue.cardName"
          type="text"
          class="form-control form-control-lg"
        />
      </div>
      <div class="col-md-6">
        <label class="form-label small">Expiry Date</label>
        <input
          v-model="modelValue.expiry"
          type="text"
          class="form-control form-control-lg"
          placeholder="MM/YY"
        />
      </div>
      <div class="col-md-6">
        <label class="form-label small">CVV</label>
        <input
          v-model="modelValue.cvv"
          type="text"
          class="form-control form-control-lg"
          placeholder="123"
        />
      </div>
    </div>

    <div class="d-flex gap-3 mt-4">
      <button class="btn btn-outline-dark flex-grow-1" @click="$emit('back')">Back</button>
      <button class="btn btn-dark flex-grow-1" @click="$emit('continue')">Review Order</button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: Object,
    required: true
  }
})

defineEmits(['back', 'continue'])
</script>

<style scoped>
.form-card {
  border: 1px solid #e9ecef;
  border-radius: 16px;
  background: #fff;
}

.payment-option {
  border: 1px solid #e9ecef;
  border-radius: 12px;
  padding: 12px 16px;
  margin-bottom: 12px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.payment-option.selected {
  border-color: #111827;
}

.form-control-lg {
  background: #f6f7f9;
  border: none;
  border-radius: 12px;
  padding: 12px 16px;
  font-size: 14px;
}
</style>
