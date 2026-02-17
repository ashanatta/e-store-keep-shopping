<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="#">E-Shop</a>

      <button 
        class="navbar-toggler" 
        type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#navbarNav"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <router-link to="/" class="nav-link">Home</router-link>
          </li>
          <li class="nav-item">
             <router-link to="/shop" class="nav-link">Shop</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/men" class="nav-link">Men</router-link>
          </li>
          <li class="nav-item">
             <router-link to="/women" class="nav-link">Women</router-link>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup>
</script>
