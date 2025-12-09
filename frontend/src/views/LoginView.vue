<template>
  <div class="login-layout">
    <!-- lewa część: tekst + karta logowania -->
    <div class="login-main card">
      <div class="login-main-left">
        <div class="badge">
          <span>Bez skierowania</span>
        </div>
        <h1>Wyniki badań dla Ciebie i Twoich bliskich</h1>
        <p class="lead">
          Zaloguj się do Panelu Pacjenta, aby sprawdzić wyniki swoich badań
          wykonanych w sieci eLab. Bezpiecznie, szybko i w jednym miejscu.
        </p>

        <ul class="features">
          <li>• Dostęp do historii badań</li>
          <li>• Dane pacjenta i zleceń w jednym widoku</li>
          <li>• Szybki podgląd wartości referencyjnych</li>
        </ul>
      </div>

      <div class="login-main-right">
        <div class="login-card">
          <h2>Zaloguj się</h2>
          <p class="login-hint">
            Login: <strong>imię + nazwisko</strong> (np. <code>PiotrKowalski</code>)<br />
            Hasło: <strong>data urodzenia</strong> (np. <code>1983-04-12</code>)
          </p>

          <form @submit.prevent="handleSubmit" class="login-form">
            <div class="field">
              <label for="login">Login</label>
              <input
                id="login"
                v-model="form.login"
                type="text"
                autocomplete="username"
                placeholder="Np. PiotrKowalski"
              />
            </div>

            <div class="field">
              <label for="password">Data urodzenia</label>
              <input
                id="password"
                v-model="form.password"
                type="text"
                autocomplete="current-password"
                placeholder="YYYY-MM-DD"
              />
            </div>

            <p v-if="error" class="text-error">
              {{ error }}
            </p>

            <button type="submit" class="button-primary" :disabled="loading">
              <span v-if="!loading">Zaloguj się do wyników</span>
              <span v-else>Logowanie...</span>
            </button>
          </form>

          <p class="small-text">
            Masz problem z logowaniem? Skontaktuj się z punktem pobrań lub
            administratorem systemu (wersja demo).
          </p>
        </div>
      </div>
    </div>

    <!-- prawa "ilustracyjna" kolumna na szerszych ekranach -->
    <div class="login-side">
      <div class="login-side-card">
        <p class="login-side-title">Dlaczego warto robić badania regularnie?</p>
        <p class="login-side-text">
          Wczesne wykrycie nieprawidłowości w wynikach badań pozwala szybciej
          reagować i lepiej zadbać o zdrowie. Panel Pacjenta ułatwia
          śledzenie zmian w czasie.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { login } from "../api/authApi";

const router = useRouter();

const form = ref({
  login: "",
  password: "",
});

const loading = ref(false);
const error = ref("");

async function handleSubmit() {
  error.value = "";
  if (!form.value.login || !form.value.password) {
    error.value = "Uzupełnij login i hasło.";
    return;
  }

  loading.value = true;

  try {
    const data = await login(form.value.login, form.value.password);
    localStorage.setItem("token", data.token);
    router.push({ name: "results" });
  } catch (e) {
    console.error(e);
    error.value = "Nieprawidłowy login lub hasło.";
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.login-layout {
  display: grid;
  grid-template-columns: 1.1fr 0.9fr;
  gap: 24px;
}

.login-main {
  display: grid;
  grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr);
  gap: 24px;
  align-items: stretch;
}

.login-main-left h1 {
  font-size: 1.7rem;
  margin: 12px 0 8px;
  color: var(--color-primary);
}

.lead {
  font-size: 0.95rem;
  color: var(--color-text-muted);
  margin-bottom: 12px;
}

.features {
  list-style: none;
  padding: 0;
  margin: 0;
  font-size: 0.85rem;
  color: var(--color-text-muted);
}

.login-main-right {
  display: flex;
  align-items: stretch;
}

.login-card {
  background: linear-gradient(135deg, #ffffff, #f3f7ff);
  border-radius: var(--radius-lg);
  box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
  padding: 20px 18px 18px;
  width: 100%;
}

.login-card h2 {
  margin: 0 0 6px;
  font-size: 1.2rem;
}

.login-hint {
  font-size: 0.8rem;
  color: var(--color-text-muted);
  margin-bottom: 14px;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 8px;
}

.small-text {
  font-size: 0.75rem;
  color: var(--color-text-muted);
}

/* prawa kolumna */

.login-side {
  display: none;
}

.login-side-card {
  height: 100%;
  border-radius: var(--radius-lg);
  padding: 24px;
  background: radial-gradient(circle at top left, #00a0dc 0, #00508c 55%, #003353 100%);
  color: #e5f4ff;
  box-shadow: var(--shadow-soft);
}

.login-side-title {
  font-weight: 600;
  margin-bottom: 8px;
}

.login-side-text {
  font-size: 0.9rem;
  opacity: 0.95;
}

@media (max-width: 900px) {
  .login-layout {
    grid-template-columns: 1fr;
  }

  .login-side {
    display: none;
  }

  .login-main {
    grid-template-columns: 1fr;
  }
}

@media (min-width: 901px) {
  .login-side {
    display: block;
  }
}
</style>
