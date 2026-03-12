<template>
  <div class="dashboard">
    <h2 class="mb-4 fw-bold">Dashboard</h2>

    <!-- Quick Access Links -->
    <div class="row g-3 mb-4">
      <div class="col-12">
        <div class="chart-card py-3">
          <h6 class="chart-title mb-3">Quick Access</h6>
          <div class="d-flex flex-wrap gap-2">
            <router-link to="/admin/products"   class="quick-link quick-products">  <i class="bi bi-box-seam me-1"></i>Products</router-link>
            <router-link to="/admin/orders"     class="quick-link quick-orders">    <i class="bi bi-bag me-1"></i>Orders</router-link>
            <router-link to="/admin/categories" class="quick-link quick-categories"><i class="bi bi-tags me-1"></i>Categories</router-link>
            <router-link to="/admin/colors"     class="quick-link quick-colors">    <i class="bi bi-palette me-1"></i>Colors</router-link>
            <router-link to="/admin/sizes"      class="quick-link quick-sizes">     <i class="bi bi-rulers me-1"></i>Sizes</router-link>
            <router-link to="/admin/reviews"    class="quick-link quick-reviews">   <i class="bi bi-star me-1"></i>Reviews</router-link>
            <router-link to="/admin/wishlists"  class="quick-link quick-wishlists"> <i class="bi bi-heart me-1"></i>Wishlists</router-link>
            <router-link to="/admin/carts"      class="quick-link quick-carts">     <i class="bi bi-cart me-1"></i>Carts</router-link>
            <router-link to="/profile"          class="quick-link quick-profile">   <i class="bi bi-person-circle me-1"></i>My Profile</router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4" v-if="stats">
      <div class="col-6 col-xl-3">
        <div class="stat-card stat-users">
          <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.total_users }}</div>
            <div class="stat-label">Total Users</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="stat-card stat-orders">
          <div class="stat-icon"><i class="bi bi-bag-fill"></i></div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.total_orders }}</div>
            <div class="stat-label">Total Orders</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="stat-card stat-products">
          <div class="stat-icon"><i class="bi bi-box-seam-fill"></i></div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.total_products }}</div>
            <div class="stat-label">Total Products</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-xl-3">
        <div class="stat-card stat-sales">
          <div class="stat-icon"><i class="bi bi-currency-dollar"></i></div>
          <div class="stat-info">
            <div class="stat-value">${{ formatCurrency(stats.total_sales) }}</div>
            <div class="stat-label">Total Sales</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Skeleton loader -->
    <div v-if="loading" class="row g-3 mb-4">
      <div v-for="n in 4" :key="n" class="col-6 col-xl-3">
        <div class="stat-card skeleton-card"></div>
      </div>
    </div>

    <!-- Charts -->
    <div class="row g-3" v-if="stats">
      <!-- Revenue Line Chart -->
      <div class="col-lg-8">
        <div class="chart-card">
          <h6 class="chart-title">Revenue — Last 7 Days</h6>
          <canvas ref="revenueChartRef" height="110"></canvas>
        </div>
      </div>

      <!-- Orders by Status Doughnut -->
      <div class="col-lg-4">
        <div class="chart-card">
          <h6 class="chart-title">Orders by Status</h6>
          <div style="position:relative;height:220px;">
            <canvas ref="statusChartRef"></canvas>
          </div>
        </div>
      </div>

      <!-- Orders Bar Chart -->
      <div class="col-12">
        <div class="chart-card">
          <h6 class="chart-title">Orders — Last 7 Days</h6>
          <canvas ref="ordersChartRef" height="80"></canvas>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import axios from 'axios'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const stats = ref(null)
const loading = ref(true)

const revenueChartRef = ref(null)
const statusChartRef  = ref(null)
const ordersChartRef  = ref(null)

const formatCurrency = (val) => {
  const num = parseFloat(val) || 0
  return num >= 1000 ? (num / 1000).toFixed(1) + 'k' : num.toFixed(2)
}

const STATUS_COLORS = {
  pending:    '#f59e0b',
  processing: '#3b82f6',
  shipped:    '#8b5cf6',
  delivered:  '#10b981',
  cancelled:  '#ef4444',
}

const buildCharts = (data) => {
  // Revenue line chart
  const revLabels = data.revenue_by_day.map(d => d.date.slice(5))
  const revData   = data.revenue_by_day.map(d => d.revenue)
  new Chart(revenueChartRef.value, {
    type: 'line',
    data: {
      labels: revLabels,
      datasets: [{
        label: 'Revenue ($)',
        data: revData,
        borderColor: '#111827',
        backgroundColor: 'rgba(17,24,39,0.08)',
        borderWidth: 2,
        pointRadius: 4,
        pointBackgroundColor: '#111827',
        fill: true,
        tension: 0.4,
      }],
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, grid: { color: '#f1f1f1' } },
        x: { grid: { display: false } },
      },
    },
  })

  // Orders by status doughnut
  const statusEntries = Object.entries(data.orders_by_status)
  new Chart(statusChartRef.value, {
    type: 'doughnut',
    data: {
      labels: statusEntries.map(([k]) => k.charAt(0).toUpperCase() + k.slice(1)),
      datasets: [{
        data: statusEntries.map(([, v]) => v),
        backgroundColor: statusEntries.map(([k]) => STATUS_COLORS[k] || '#6b7280'),
        borderWidth: 2,
        borderColor: '#fff',
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { padding: 12, font: { size: 12 } },
        },
      },
      cutout: '65%',
    },
  })

  // Orders bar chart
  const ordLabels = data.orders_by_day.map(d => d.date.slice(5))
  const ordData   = data.orders_by_day.map(d => d.count)
  new Chart(ordersChartRef.value, {
    type: 'bar',
    data: {
      labels: ordLabels,
      datasets: [{
        label: 'Orders',
        data: ordData,
        backgroundColor: '#3b82f6',
        borderRadius: 6,
      }],
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f1f1' } },
        x: { grid: { display: false } },
      },
    },
  })
}

onMounted(async () => {
  try {
    const res = await axios.get('/admin/stats')
    stats.value = res.data
    await nextTick()
    buildCharts(res.data)
  } catch (e) {
    console.error('Failed to load dashboard stats', e)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.dashboard {
  padding: 4px 0;
}

/* Stat Cards */
.stat-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px;
  border-radius: 14px;
  background: #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,.07);
  min-height: 90px;
}

.stat-icon {
  width: 52px;
  height: 52px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  font-size: 22px;
  flex-shrink: 0;
}

.stat-users  .stat-icon { background: #eff6ff; color: #3b82f6; }
.stat-orders .stat-icon { background: #fefce8; color: #ca8a04; }
.stat-products .stat-icon { background: #f0fdf4; color: #16a34a; }
.stat-sales  .stat-icon { background: #fdf4ff; color: #9333ea; }

.stat-value {
  font-size: 22px;
  font-weight: 700;
  color: #111827;
  line-height: 1.2;
}

.stat-label {
  font-size: 12px;
  color: #6b7280;
  margin-top: 2px;
}

.skeleton-card {
  height: 90px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
}

@keyframes shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* Chart Cards */
.chart-card {
  background: #fff;
  border-radius: 14px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,.07);
}

.chart-title {
  font-weight: 600;
  color: #374151;
  margin-bottom: 16px;
}

/* Quick Access Links */
.quick-link {
  display: inline-flex;
  align-items: center;
  padding: 7px 16px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 500;
  text-decoration: none;
  transition: opacity 0.15s, transform 0.15s;
}

.quick-link:hover {
  opacity: 0.85;
  transform: translateY(-1px);
}

.quick-products   { background: #eff6ff; color: #2563eb; }
.quick-orders     { background: #fefce8; color: #b45309; }
.quick-categories { background: #f0fdf4; color: #15803d; }
.quick-colors     { background: #fdf4ff; color: #9333ea; }
.quick-sizes      { background: #fff7ed; color: #c2410c; }
.quick-reviews    { background: #fef9c3; color: #854d0e; }
.quick-wishlists  { background: #fce7f3; color: #be185d; }
.quick-carts      { background: #ecfdf5; color: #047857; }
.quick-profile    { background: #f1f5f9; color: #334155; }
</style>
