<template>
  <footer class="site-footer mt-auto">
    <div class="container-xxl footer-wrap">
      <!-- Newsletter / CTA -->
      <div class="footer-cta">
        <div>
          <span class="eyebrow eyebrow-light">Stay in the loop</span>
          <h3 class="cta-title">
            New drops, early sales &amp; <em>style notes</em> — straight to your inbox.
          </h3>
        </div>
        <form class="newsletter" @submit.prevent="subscribe">
          <input
            v-model="email"
            type="email"
            required
            placeholder="Your email address"
            aria-label="Email address"
          />
          <button type="submit" class="btn btn-accent">
            {{ subscribed ? "Subscribed ✓" : "Subscribe" }}
          </button>
        </form>
      </div>

      <div class="row g-5 footer-cols">
        <div class="col-12 col-lg-4">
          <img src="/logo.png" alt="Doller Shope" class="footer-logo" />
          <p class="footer-about">
            Your trusted online clothing store — curated collections for men, women and kids,
            with AI-powered virtual try-on.
          </p>
          <div class="socials">
            <a href="#" aria-label="Instagram" @click.prevent><i class="bi bi-instagram"></i></a>
            <a href="#" aria-label="Facebook" @click.prevent><i class="bi bi-facebook"></i></a>
            <a href="#" aria-label="TikTok" @click.prevent><i class="bi bi-tiktok"></i></a>
            <a href="#" aria-label="X" @click.prevent><i class="bi bi-twitter-x"></i></a>
          </div>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
          <h6>Shop</h6>
          <ul>
            <li><router-link to="/shop">All Products</router-link></li>
            <li><router-link to="/men">Men</router-link></li>
            <li><router-link to="/women">Women</router-link></li>
            <li><router-link to="/kids">Kids</router-link></li>
            <li><router-link to="/sales" class="hot">Sale</router-link></li>
          </ul>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
          <h6>Account</h6>
          <ul>
            <li><router-link to="/profile">My Profile</router-link></li>
            <li><router-link to="/orders">My Orders</router-link></li>
            <li><router-link to="/cart">Cart</router-link></li>
            <li><router-link to="/about">About Us</router-link></li>
          </ul>
        </div>

        <div class="col-12 col-md-4 col-lg-4">
          <h6>Contact</h6>
          <ul class="contact">
            <li>
              <i class="bi bi-envelope"></i>
              <a href="mailto:muhammadashan1714@gmail.com">muhammadashan1714@gmail.com</a>
            </li>
            <li>
              <i class="bi bi-telephone"></i>
              <a href="tel:+923060297685">+92 306 0297685</a>
            </li>
            <li>
              <i class="bi bi-chat-dots"></i>
              <span>Live chat support, 7 days a week</span>
            </li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <span>© {{ year }} Doller Shope. All rights reserved.</span>
        <div class="pay">
          <i class="bi bi-credit-card-2-front"></i>
          <i class="bi bi-paypal"></i>
          <i class="bi bi-stripe"></i>
          <i class="bi bi-cash-coin"></i>
        </div>
      </div>
    </div>

    <div class="footer-wordmark" aria-hidden="true">Doller Shope</div>
  </footer>
</template>

<script setup>
import { ref } from "vue"

const year = new Date().getFullYear()
const email = ref("")
const subscribed = ref(false)

const subscribe = () => {
  subscribed.value = true
  email.value = ""
  setTimeout(() => { subscribed.value = false }, 3000)
}
</script>

<style scoped>
.site-footer {
  position: relative;
  overflow: hidden;
  background: var(--ink);
  color: rgba(255, 255, 255, 0.7);
  padding-top: 72px;
}

.footer-wrap {
  position: relative;
  z-index: 1;
  padding-left: 20px;
  padding-right: 20px;
}

@media (min-width: 992px) {
  .footer-wrap {
    padding-left: 32px;
    padding-right: 32px;
  }
}

.eyebrow-light {
  color: rgba(255, 255, 255, 0.6);
}

.footer-cta {
  display: grid;
  gap: 28px;
  padding-bottom: 56px;
  margin-bottom: 56px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

@media (min-width: 992px) {
  .footer-cta {
    grid-template-columns: 1.3fr 1fr;
    align-items: end;
    gap: 64px;
  }
}

.cta-title {
  color: #fff;
  font-size: clamp(26px, 3.2vw, 40px);
  line-height: 1.12;
  margin: 14px 0 0;
  max-width: 640px;
}

.cta-title em {
  font-family: var(--font-display);
  font-weight: 400;
  color: var(--accent);
}

.newsletter {
  display: flex;
  gap: 6px;
  padding: 6px;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 999px;
}

.newsletter input {
  flex: 1;
  min-width: 0;
  border: 0;
  outline: 0;
  background: transparent;
  color: #fff;
  padding: 0 18px;
  font-size: 15px;
}

.newsletter input::placeholder {
  color: rgba(255, 255, 255, 0.45);
}

.newsletter .btn {
  white-space: nowrap;
}

.footer-logo {
  height: 40px;
  width: auto;
  background: #fff;
  border-radius: 12px;
  padding: 6px 10px;
}

.footer-about {
  margin: 20px 0 24px;
  max-width: 340px;
  font-size: 14.5px;
}

.socials {
  display: flex;
  gap: 10px;
}

.socials a {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.16);
  text-decoration: none;
  transition: all 0.25s var(--ease);
}

.socials a:hover {
  background: var(--accent);
  border-color: var(--accent);
  transform: translateY(-3px);
}

h6 {
  color: #fff;
  font-size: 13px;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  margin-bottom: 18px;
}

ul {
  list-style: none;
  padding: 0;
  margin: 0;
  display: grid;
  gap: 12px;
  font-size: 14.5px;
}

ul a {
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  transition: color 0.2s;
  word-break: break-word;
}

ul a:hover {
  color: #fff;
}

ul a.hot {
  color: var(--accent);
}

.contact li {
  display: flex;
  gap: 12px;
  align-items: flex-start;
}

.contact i {
  color: var(--accent);
  margin-top: 2px;
}

.footer-bottom {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  align-items: center;
  justify-content: space-between;
  margin-top: 64px;
  padding: 24px 0;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  font-size: 13px;
}

.pay {
  display: flex;
  gap: 16px;
  font-size: 22px;
  color: rgba(255, 255, 255, 0.5);
}

.footer-wordmark {
  font-family: var(--font-display);
  font-size: clamp(80px, 17vw, 260px);
  line-height: 0.8;
  text-align: center;
  white-space: nowrap;
  color: transparent;
  -webkit-text-stroke: 1px rgba(255, 255, 255, 0.1);
  margin-bottom: -0.12em;
  user-select: none;
  pointer-events: none;
}
</style>
