<template>
  <div>
    <section class="about-hero py-5">
      <div class="container">
        <div class="row align-items-center g-4">
          <div class="col-lg-7">
            <h1 class="fw-bold mb-3">About Us</h1>
            <p class="text-muted mb-4">
              We’re building a modern fashion store for the entire family. Clean designs, quality
              fabrics, and an easy shopping experience.
            </p>
            <div class="d-flex flex-wrap gap-2">
              <span class="badge rounded-pill text-bg-dark px-3 py-2">Quality</span>
              <span class="badge rounded-pill text-bg-dark px-3 py-2">Comfort</span>
              <span class="badge rounded-pill text-bg-dark px-3 py-2">Style</span>
              <span class="badge rounded-pill text-bg-dark px-3 py-2">Fast Delivery</span>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="hero-card p-4">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="icon-circle">
                  <i class="bi bi-bag"></i>
                </div>
                <div>
                  <div class="fw-semibold">FASHION</div>
                  <div class="text-muted small">Since 2026</div>
                </div>
              </div>
              <div class="row g-3">
                <div class="col-6">
                  <div class="stat">
                    <div class="stat-number">1K+</div>
                    <div class="stat-label">Products</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="stat">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Brands</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="stat">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Support</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="stat">
                    <div class="stat-number">Fast</div>
                    <div class="stat-label">Shipping</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="py-5 bg-light">
      <div class="container">
        <div class="row g-4">
          <div class="col-lg-4">
            <div class="info-card p-4 h-100">
              <div class="info-icon mb-3">
                <i class="bi bi-stars"></i>
              </div>
              <h5 class="fw-semibold">Our Mission</h5>
              <p class="text-muted mb-0">
                Make shopping simple and enjoyable with curated collections for men, women, and kids.
              </p>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="info-card p-4 h-100">
              <div class="info-icon mb-3">
                <i class="bi bi-shield-check"></i>
              </div>
              <h5 class="fw-semibold">Our Promise</h5>
              <p class="text-muted mb-0">
                Reliable delivery, secure checkout, and easy returns so you can shop with confidence.
              </p>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="info-card p-4 h-100">
              <div class="info-icon mb-3">
                <i class="bi bi-heart"></i>
              </div>
              <h5 class="fw-semibold">Our Style</h5>
              <p class="text-muted mb-0">
                Timeless basics, seasonal trends, and everyday comfort — made to fit your life.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="py-5">
      <div class="container">
        <div class="row g-4">
          <div class="col-lg-6">
            <div class="contact-card p-4 h-100">
              <h5 class="fw-semibold mb-3">Customer Service</h5>
              <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-envelope"></i>
                <span class="text-muted">info@eshop.com</span>
              </div>
              <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-telephone"></i>
                <span class="text-muted">+92 300 0000000</span>
              </div>
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt"></i>
                <span class="text-muted">Online Store • Worldwide Shipping</span>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="contact-card p-4 h-100">
              <h5 class="fw-semibold mb-3">Quick Links</h5>
              <div class="d-flex flex-column gap-2">
                <router-link class="link-dark text-decoration-none" to="/men">Men’s Collection</router-link>
                <router-link class="link-dark text-decoration-none" to="/women">Women’s Collection</router-link>
                <router-link class="link-dark text-decoration-none" to="/kids">Kids’ Collection</router-link>
                <router-link class="link-dark text-decoration-none" to="/sales">Sale</router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup></script>

<style scoped>
.about-hero {
  background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
}

.hero-card {
  border: 1px solid #e9ecef;
  border-radius: 16px;
  background: #fff;
}

.icon-circle {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: #111827;
  color: #fff;
  display: grid;
  place-items: center;
  font-size: 18px;
}

.stat {
  border: 1px solid #f1f1f1;
  border-radius: 14px;
  padding: 12px 14px;
  background: #fafafa;
}

.stat-number {
  font-weight: 700;
  font-size: 18px;
}

.stat-label {
  color: #6b7280;
  font-size: 12px;
}

.info-card {
  border: 1px solid #e9ecef;
  border-radius: 16px;
  background: #fff;
}

.info-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: #f0f1f3;
  display: grid;
  place-items: center;
  font-size: 18px;
  color: #111827;
}

.contact-card {
  border: 1px solid #e9ecef;
  border-radius: 16px;
  background: #fff;
}
</style>
