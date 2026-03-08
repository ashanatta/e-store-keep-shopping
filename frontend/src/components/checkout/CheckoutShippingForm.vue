<template>
  <div class="form-card p-4">
    <h5 class="fw-semibold mb-4">Shipping Information</h5>
    <form @submit.prevent="$emit('continue')">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-medium">Full Name</label>
          <input 
            type="text" 
            class="form-control form-control-lg" 
            v-model="modelValue.fullName"
            required
          />
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-medium">Email</label>
          <input 
            type="email" 
            class="form-control form-control-lg" 
            v-model="modelValue.email"
            required
          />
        </div>
        <div class="col-12">
          <label class="form-label small fw-medium">Phone Number</label>
          <input 
            type="text" 
            class="form-control form-control-lg" 
            v-model="modelValue.phoneNumber"
            required
          />
        </div>
        <div class="col-12">
          <label class="form-label small fw-medium">Street Address</label>
          <input 
            type="text" 
            class="form-control form-control-lg" 
            v-model="modelValue.streetAddress"
            required
          />
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-medium">City</label>
          <input 
            type="text" 
            class="form-control form-control-lg" 
            v-model="modelValue.city"
            required
          />
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-medium">State</label>
          <input 
            type="text" 
            class="form-control form-control-lg" 
            v-model="modelValue.state"
            required
          />
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-medium">ZIP Code</label>
          <input 
            type="text" 
            class="form-control form-control-lg" 
            v-model="modelValue.zipCode"
            required
          />
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-medium">Country</label>
          <input 
            type="text" 
            class="form-control form-control-lg" 
            v-model="modelValue.country"
            required
          />
        </div>
      </div>
      <button type="submit" class="btn btn-dark w-100 mt-5 py-3 rounded-pill fw-semibold">Continue to Payment</button>
    </form>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  }
})

defineEmits(['update:modelValue', 'continue'])
</script>

<style scoped>
.form-card {
  border: 1px solid #e9ecef;
  border-radius: 20px;
  background: #fff;
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
</style>
