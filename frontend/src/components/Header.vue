<template>
  <header class="bg-blue-500 text-white p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold">My E-Commerce Site</h1>
    <nav>
      <ul class="flex space-x-4">
        <li><a href="/" class="hover:underline">Home</a></li>
        <li><a href="/shop" class="hover:underline">Shop</a></li>
        <li><a href="/about" class="hover:underline">About</a></li>
        <li><a href="/contact" class="hover:underline">Contact</a></li>
      </ul>
    </nav>
  </header>
</template>

<script>
export default {
  name: "Header", // <-- this is enough
};
</script>

<style scoped>
/* custom styles if needed */
</style>
