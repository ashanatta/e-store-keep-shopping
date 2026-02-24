<template>
  <div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h3 class="fw-semibold mb-0">Checkout</h3>
    </div>
    <CheckoutStepper :step="currentStep" />

    <div class="row g-4 mt-4">
      <div class="col-lg-8">
        <CheckoutShippingForm v-if="currentStep === 'shipping'" />
        <CheckoutPaymentForm v-else />
      </div>
      <div class="col-lg-4">
        <OrderSummary :items="items" :show-items="true" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"
import CheckoutStepper from "@/components/checkout/CheckoutStepper.vue"
import CheckoutShippingForm from "@/components/checkout/CheckoutShippingForm.vue"
import CheckoutPaymentForm from "@/components/checkout/CheckoutPaymentForm.vue"
import OrderSummary from "@/components/common/OrderSummary.vue"

const currentStep = ref("shipping")

const items = ref([
  {
    id: 1,
    name: "Elegant Blazer",
    category: "Blazers",
    size: "S",
    color: "Black",
    quantity: 1,
    price: 149.99,
    image: "https://images.unsplash.com/photo-1524504388940-b1c1722653e1"
  }
])
</script>
