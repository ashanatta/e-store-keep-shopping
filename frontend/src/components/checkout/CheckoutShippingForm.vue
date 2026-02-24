<template>
  <div class="form-card p-4">
    <h5 class="fw-semibold mb-4">Shipping Information</h5>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label small">Full Name</label>
        <input type="text" class="form-control form-control-lg" />
      </div>
      <div class="col-md-6">
        <label class="form-label small">Email</label>
        <input type="email" class="form-control form-control-lg" />
      </div>
      <div class="col-12">
        <label class="form-label small">Phone Number</label>
        <input type="text" class="form-control form-control-lg" />
      </div>
      <div class="col-12">
        <label class="form-label small">Street Address</label>
        <input type="text" class="form-control form-control-lg" />
      </div>
      <div class="col-md-6">
        <label class="form-label small">City</label>
        <input type="text" class="form-control form-control-lg" />
      </div>
      <div class="col-md-6">
        <label class="form-label small">State</label>
        <input type="text" class="form-control form-control-lg" />
      </div>
      <div class="col-md-6">
        <label class="form-label small">ZIP Code</label>
        <input type="text" class="form-control form-control-lg" />
      </div>
      <div class="col-md-6">
        <label class="form-label small">Country</label>
        <input type="text" class="form-control form-control-lg" value="USA" />
      </div>
    </div>
    <button class="btn btn-dark w-100 mt-4">Continue to Payment</button>
  </div>
</template>

<script setup></script>

<style scoped>
.form-card {
  border: 1px solid #e9ecef;
  border-radius: 16px;
  background: #fff;
}

.form-control-lg {
  background: #f6f7f9;
  border: none;
  border-radius: 12px;
  padding: 12px 16px;
  font-size: 14px;
}
</style>
