<template>
  <section class="winter-sale py-5">
    <div class="container">
      <div class="row align-items-center g-5">

        <!-- Text Side -->
        <div class="col-md-6 sale-text">
          <h2>Winter Sale</h2>
          <p>
            Stay warm with our premium winter collection. Up to 40% off!
          </p>
          <button class="sale-btn">
            Shop Winter Collection
          </button>
        </div>

        <!-- Image Side -->
        <div class="col-md-6">
          <div class="image-wrapper">
            <img
              src="https://images.unsplash.com/photo-1699435870820-36eab47448f8?w=800"
              alt="Winter Sale"
            />
          </div>
        </div>

      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "WinterSale"
}
</script>

<style scoped>
.winter-sale {
  background: #000000;
  color: white;
}

.sale-text h2 {
  font-size: 42px;
  font-weight: 700;
}

.sale-text p {
  font-size: 18px;
  margin: 20px 0;
  opacity: 0.9;
}

.sale-btn {
  padding: 12px 30px;
  background: white;
  color: #050505;
  border: none;
  border-radius: 30px;
  font-weight: 600;
  transition: 0.3s ease;
}

.sale-btn:hover {
  background: black;
  color: white;
}

.image-wrapper {
  aspect-ratio: 4/3;
  border-radius: 15px;
  overflow: hidden;
}

.image-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: 0.4s ease;
}

.image-wrapper img:hover {
  transform: scale(1.05);
}

/* Responsive */
@media (max-width: 768px) {
  .sale-text h2 {
    font-size: 32px;
  }
}
</style>
