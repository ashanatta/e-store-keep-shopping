<template>
  <div class="hero">
    <div class="overlay">
      <div class="hero-content">
        <h1>Spring Collection 2026</h1>
        <p>
          Discover the latest trends in fashion. Up to 50% off on selected items.
        </p>
        <button class="shop-btn">
          Shop Now →
        </button>
      </div>
    </div>
    
      <ShopbyCategory />
     
   <Footer />
  </div>
</template>

<script>
import Footer from '@/components/Footer.vue';
import ShopbyCategory from '@/components/home/ShopbyCategory.vue';

export default {
  name: "Home",
   components: {
    ShopbyCategory,
    Footer
  }
}
</script>

<style scoped>

.hero {
  height: 90vh;
  background-image: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8');
  background-size: cover;
  background-position: center;
  position: relative;
}

.overlay {
  background: rgba(0, 0, 0, 0.6);
  height: 100%;
  display: flex;
  align-items: center;
}

.hero-content {
  color: white;
  padding-left: 80px;
  max-width: 600px;
}

.hero-content h1 {
  font-size: 60px;
  font-weight: bold;
}

.hero-content p {
  margin: 20px 0;
  font-size: 18px;
}

.shop-btn {
  padding: 10px 20px;
  border: none;
  background: white;
  color: black;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
}

.shop-btn:hover {
  background: #f8f9fa;
}

/* Responsive */
@media (max-width: 768px) {
  .hero-content {
    padding-left: 30px;
  }

  .hero-content h1 {
    font-size: 35px;
  }
}

</style>
