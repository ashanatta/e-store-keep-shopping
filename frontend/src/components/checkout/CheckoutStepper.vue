<template>
  <div class="stepper">
    <div class="step" :class="{ active: step === 'shipping', done: step !== 'shipping' }">
      <div class="step-circle">
        <i class="bi bi-truck"></i>
      </div>
      <div class="step-label">Shipping</div>
    </div>
    <div class="step-line" :class="{ active: step !== 'shipping' }"></div>
    <div class="step" :class="{ active: step === 'payment', done: step === 'review' }">
      <div class="step-circle">
        <i class="bi bi-credit-card"></i>
      </div>
      <div class="step-label">Payment</div>
    </div>
    <div class="step-line" :class="{ active: step === 'review' }"></div>
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
  gap: 8px;
  flex-wrap: wrap;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  color: #9aa0a6;
}

.step-circle {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #f0f1f3;
  display: grid;
  place-items: center;
  font-size: 14px;
}

.step-line {
  height: 2px;
  width: 24px;
  background: #e5e7eb;
  flex-shrink: 0;
}

@media (min-width: 576px) {
  .stepper {
    gap: 20px;
  }

  .step {
    gap: 8px;
  }

  .step-circle {
    width: 44px;
    height: 44px;
    font-size: 18px;
  }

  .step-line {
    width: 60px;
  }
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
</style>
