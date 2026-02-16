<template>
  <div>
    <Header /> <!-- use the component here -->
    <main class="p-4">
      <h2>Welcome to our store!</h2>
    </main>
  </div>
</template>

<script>
import Header from '../components/Header.vue'; // <-- correct import

export default {
  name: 'Home',
  components: {
    Header, // <-- register it here
  },
};
</script>
