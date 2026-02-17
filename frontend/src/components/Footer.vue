<template>
  <footer class="bg-dark text-white mt-auto py-4">
    <div class="container">
      <div class="row">

        <div class="col-md-4">
          <h5>E-Shop</h5>
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
          <p>Email: info@eshop.com</p>
          <p>Phone: +92 300 0000000</p>
        </div>

      </div>

      <hr class="bg-light">

      <div class="text-center">
        © 2026 E-Shop. All Rights Reserved.
      </div>
    </div>
  </footer>
</template>

<script setup>
</script>
