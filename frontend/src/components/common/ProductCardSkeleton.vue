<template>
  <div aria-hidden="true">
    <div class="skeleton sk-media"></div>
    <div class="skeleton sk-line w-25 mt-3"></div>
    <div class="skeleton sk-line w-75 mt-2"></div>
    <div class="skeleton sk-line w-50 mt-2"></div>
  </div>
</template>

<style scoped>
.sk-media {
  aspect-ratio: 3 / 4;
  border-radius: var(--radius);
}

.sk-line {
  height: 12px;
  border-radius: 6px;
}
</style>
