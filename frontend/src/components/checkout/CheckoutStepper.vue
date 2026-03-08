<template>
  <div class="stepper">
    <!-- Step 1: Shipping -->
    <div class="step" :class="{ active: step === 'shipping', done: step !== 'shipping' }">
      <div class="step-circle">
        <i v-if="step !== 'shipping'" class="bi bi-check2"></i>
        <i v-else class="bi bi-truck"></i>
      </div>
      <div class="step-label">Shipping</div>
    </div>

    <div class="step-line" :class="{ active: step !== 'shipping' }"></div>

    <!-- Step 2: Payment -->
    <div class="step" :class="{ active: step === 'payment', done: step === 'review' }">
      <div class="step-circle">
        <i v-if="step === 'review'" class="bi bi-check2"></i>
        <i v-else class="bi bi-credit-card"></i>
      </div>
      <div class="step-label">Payment</div>
    </div>

    <div class="step-line" :class="{ active: step === 'review' }"></div>

    <!-- Step 3: Review -->
    <div class="step" :class="{ active: step === 'review' }">
      <div class="step-circle">
        <i class="bi bi-box"></i>
      </div>
      <div class="step-label">Review</div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  step: {
    type: String,
    default: "shipping"
  }
})
</script>

<style scoped>
.stepper {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  color: #9aa0a6;
  width: 80px;
}

.step-circle {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: #f0f1f3;
  display: grid;
  place-items: center;
  font-size: 18px;
  transition: all 0.3s ease;
}

.step-line {
  height: 2px;
  width: 60px;
  background: #e5e7eb;
  margin-bottom: 24px; /* Align with circles */
  transition: all 0.3s ease;
}

.step.active,
.step.done {
  color: #111827;
}

.step.active .step-circle,
.step.done .step-circle {
  background: #111827;
  color: white;
}

.step-line.active {
  background: #111827;
}

.step-label {
  font-size: 13px;
  font-weight: 500;
}
</style>
