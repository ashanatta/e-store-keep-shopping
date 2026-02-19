<template>
  <div>

    <!-- Hero Section -->
    <section class="hero">
      <div class="overlay">
        <div class="hero-content">
          <h1>Spring Collection 2026</h1>
          <p>
            Discover the latest trends in fashion. Up to 50% off on selected items.
          </p>
          <button class="shop-btn">Shop Now →</button>
        </div>
      </div>
    </section>

    <!-- Category Section -->
    <section class="py-5 bg-light">
      <div class="container">
        <ShopbyCategory />
      </div>
    </section>

    <!-- Featured Products -->
    <section class="py-5">
      <div class="container">
        <h2 class="section-title text-center mb-5">Featured Products</h2>

        <div class="row">
          <div
            class="col-lg-3 col-md-4 col-sm-6 mb-4"
            v-for="product in products"
            :key="product.id"
          >
            <FeaturedProduct
              :product="product"
              @toggle-wishlist="handleWishlist"
            />
          </div>
        </div>
      </div>
    </section>
    <div><WinterSale /></div>
    <div>
      <RecommendationProducts />
    </div>


  </div>
</template>


<script>
import ShopbyCategory from "@/components/home/ShopbyCategory.vue";
import FeaturedProduct from "@/components/home/FeaturedProduct.vue";
import WinterSale from "@/components/home/WinterSale.vue";
import RecommendationProducts from "@/components/home/RecommendationProducts.vue";

export default {
  name: "Home",
  components: {
    ShopbyCategory,
    FeaturedProduct,
    WinterSale,
    RecommendationProducts
  },
  methods: {
  handleWishlist(id) {
    console.log("Wishlist:", id);
  }
}
,
  data() {
    return {
      products: [
        {
          id: 1,
          name: "Men Casual Shirt",
          image: "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab",
          price: 29.99,
          originalPrice: 49.99,
          rating: 4.5,
          reviews: 120,
          new: true,
        },
        {
          id: 2,
          name: "Women Summer Dress",
          image: "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab",
          price: 39.99,
          originalPrice: null,
          rating: 4.2,
          reviews: 90,
          new: false,
        },
         {
          id: 2,
          name: "Women Summer Dress",
          image: "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab",
          price: 39.99,
          originalPrice: null,
          rating: 4.2,
          reviews: 90,
          new: false,
        },
         {
          id: 2,
          name: "Women Summer Dress",
          image: "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab",
          price: 39.99,
          originalPrice: null,
          rating: 4.2,
          reviews: 90,
          new: false,
        },
         {
          id: 2,
          name: "Women Summer Dress",
          image: "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab",
          price: 39.99,
          originalPrice: null,
          rating: 4.2,
          reviews: 90,
          new: false,
        },
         {
          id: 2,
          name: "Women Summer Dress",
          image: "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab",
          price: 39.99,
          originalPrice: null,
          rating: 4.2,
          reviews: 90,
          new: false,
        },
      ],
    };
  },
};
</script>

<style scoped>
.hero {
  height: 100vh;
  background-image: url("https://images.unsplash.com/photo-1441986300917-64674bd600d8");
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
  padding-left: 100px;
  max-width: 600px;
}

.hero-content h1 {
  font-size: 64px;
  font-weight: 700;
}

.hero-content p {
  margin: 25px 0;
  font-size: 18px;
  opacity: 0.9;
}

.shop-btn {
  padding: 12px 28px;
  border: none;
  background: white;
  color: black;
  border-radius: 30px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.3s ease;
}

.shop-btn:hover {
  background: black;
  color: white;
}

.section-title {
  font-weight: 700;
  font-size: 32px;
}

/* Responsive */
@media (max-width: 992px) {
  .hero-content {
    padding-left: 50px;
  }

  .hero-content h1 {
    font-size: 48px;
  }
}

@media (max-width: 768px) {
  .hero-content {
    padding-left: 30px;
  }

  .hero-content h1 {
    font-size: 36px;
  }

  .hero-content p {
    font-size: 16px;
  }
}

</style>
