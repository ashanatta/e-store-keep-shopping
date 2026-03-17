<template>
  <div class="form-card p-4">
    <h5 class="fw-semibold mb-4">Shipping Information</h5>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label small">Full Name</label>
        <input
          v-model="modelValue.fullName"
          type="text"
          class="form-control form-control-lg"
        />
      </div>
      <div class="col-md-6">
        <label class="form-label small">Email</label>
        <input
          v-model="modelValue.email"
          type="email"
          class="form-control form-control-lg"
        />
      </div>
      <div class="col-12">
        <label class="form-label small">Phone Number</label>
        <input
          v-model="modelValue.phoneNumber"
          type="text"
          class="form-control form-control-lg"
        />
      </div>
      <div class="col-12">
        <label class="form-label small">Street Address</label>
        <input
          v-model="modelValue.streetAddress"
          type="text"
          class="form-control form-control-lg"
        />
      </div>
      <div class="col-md-6">
        <label class="form-label small">City</label>
        <input
          v-model="modelValue.city"
          type="text"
          class="form-control form-control-lg"
        />
      </div>
      <div class="col-md-6">
        <label class="form-label small">State</label>
        <input
          v-model="modelValue.state"
          type="text"
          class="form-control form-control-lg"
        />
      </div>
      <div class="col-md-6">
        <label class="form-label small">ZIP Code</label>
        <input
          v-model="modelValue.zipCode"
          type="text"
          class="form-control form-control-lg"
        />
      </div>
      <div class="col-md-6">
        <label class="form-label small">Country</label>
        <input
          v-model="modelValue.country"
          type="text"
          class="form-control form-control-lg"
        />
      </div>
    </div>
    <button class="btn btn-dark w-100 mt-4" @click="$emit('continue')">Continue to Payment</button>
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: Object,
    required: true
  }
})

defineEmits(['continue'])
</script>

<style scoped>
.form-card {
  border: 1px solid #e9ecef;
  border-radius: 16px;
  background: #fff;
}

@media (max-width: 576px) {
  .form-card {
    padding: 1rem !important;
  }
}

.form-control-lg {
  background: #f6f7f9;
  border: none;
  border-radius: 12px;
  padding: 12px 16px;
  font-size: 14px;
}
</style>
