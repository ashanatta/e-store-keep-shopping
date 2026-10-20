<template>
  <div class="d-flex flex-column min-vh-100">
    <Header />
    <main class="flex-fill">
      <RouterView v-slot="{ Component, route: viewRoute }">
        <Transition name="page" mode="out-in">
          <component :is="Component" :key="viewRoute.matched[0]?.path" />
        </Transition>
      </RouterView>
    </main>
    <Footer v-if="!isAdminRoute" />
    <ChatWidget v-if="!isAdmin" />
    <ToastContainer />
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
import { useAuth } from "@/composables/useAuth";
import Header from "./components/Header.vue";
import Footer from "./components/Footer.vue";
import ChatWidget from "./components/common/ChatWidget.vue";
import ToastContainer from "./components/common/ToastContainer.vue";

const { isAdmin } = useAuth();
const route = useRoute();
const isAdminRoute = computed(() => route.path.startsWith("/admin"));
</script>
