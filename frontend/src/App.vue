<template>
  <div class="app-shell">
    <!-- TOP BAR -->
    <header class="app-header">
      <div class="app-header-inner">
        <div class="app-header-left">
          <img src="/src/assets/logo-lab.svg" alt="eLab" class="logo" />
          <div class="brand-text">
            <span class="brand-main">eLab laboratoria</span>
            <span class="brand-sub">Panel Pacjenta</span>
          </div>
        </div>

        <div class="app-header-right">
          <button class="button-ghost" @click="goHome">
            Strona główna
          </button>
        </div>
      </div>
    </header>

    <!-- CONTENT -->
    <main class="app-main">
      <div class="app-container">
        <router-view />
      </div>
    </main>

    <!-- FOOTER mini -->
    <footer class="app-footer">
      <div class="app-footer-inner">
        <span>© {{ currentYear }} eLab laboratoria – wersja rekrutacyjna</span>
        <span class="app-footer-links">Polityka prywatności · Bezpieczeństwo danych</span>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { useRouter } from "vue-router";
const router = useRouter();

const currentYear = new Date().getFullYear();

function goHome() {
  router.push({ name: "login" });
}
</script>

<style scoped>
.app-header {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(148, 163, 184, 0.25);
}

.app-header-inner {
  max-width: 960px;
  margin: 0 auto;
  padding: 10px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.app-header-left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.logo {
  height: 32px;
}

.brand-text {
  display: flex;
  flex-direction: column;
}

.brand-main {
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--color-primary);
}

.brand-sub {
  font-size: 0.75rem;
  color: var(--color-text-muted);
}

.app-header-right {
  display: flex;
  align-items: center;
  gap: 8px;
}

.app-footer {
  border-top: 1px solid rgba(148, 163, 184, 0.3);
  background: rgba(255, 255, 255, 0.8);
}

.app-footer-inner {
  max-width: 960px;
  margin: 0 auto;
  padding: 8px 16px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  font-size: 0.75rem;
  color: var(--color-text-muted);
}

.app-footer-links {
  opacity: 0.85;
}

@media (min-width: 768px) {
  .app-footer-inner {
    flex-direction: row;
    justify-content: space-between;
  }
}
</style>
