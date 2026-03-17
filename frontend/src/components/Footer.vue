<template>
  <footer class="bg-dark text-white mt-auto py-4 footer-responsive">
    <div class="container px-3 px-md-0">
      <div class="row">

        <div class="col-md-4">
          <h5>Doller Shope</h5>
          <p>Your trusted online clothing store.</p>
        </div>

        <div class="col-md-4">
          <h5>Quick Links</h5>
          <ul class="list-unstyled">
            <li><router-link to="/" class="text-white text-decoration-none">Home</router-link></li>
            <li><router-link to="/men" class="text-white text-decoration-none">Men</router-link></li>
            <li><router-link to="/women" class="text-white text-decoration-none">Women</router-link></li>
          </ul>
        </div>

        <div class="col-md-4">
          <h5>Contact</h5>
          <p>Email: muhammadashan1714@gmail.com</p>
          <p>Phone: +923060297685 </p>
        </div>

      </div>

      <hr class="bg-light">

      <div class="text-center">
        © 2026 2-Doller Shope. All Rights Reserved.
      </div>
    </div>
  </footer>
</template>

<script setup>
</script>

<style scoped>
.footer-responsive .row > [class*="col-"] {
  margin-bottom: 1.5rem;
}

.footer-responsive .row > [class*="col-"]:last-of-type {
  margin-bottom: 0;
}

@media (min-width: 768px) {
  .footer-responsive .row > [class*="col-"] {
    margin-bottom: 0;
  }
}

.footer-responsive h5 {
  font-size: 1rem;
  margin-bottom: 0.5rem;
}

.footer-responsive p,
.footer-responsive ul {
  font-size: 0.9rem;
}
</style>
