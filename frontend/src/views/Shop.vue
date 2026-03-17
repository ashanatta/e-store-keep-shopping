<template>
  <ProductListingLayout :title="pageTitle" category="all" />
</template>

<script setup>
import { computed } from "vue"
import { useRoute } from "vue-router"
import ProductListingLayout from "@/components/productListing/ProductListingLayout.vue"

const route = useRoute()
const pageTitle = computed(() => {
  const q = route.query.q?.trim()
  return q ? `Search results for "${q}"` : "Product Listing"
})
</script>
