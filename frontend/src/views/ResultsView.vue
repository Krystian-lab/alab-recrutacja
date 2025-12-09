<template>
  <div class="results-layout">
    <section class="card results-header">
      <div class="results-header-main">
        <div>
          <div class="badge">
            <span>Wyniki badań</span>
          </div>
          <h1>Panel Pacjenta</h1>
          <p class="header-sub">
            Zobacz swoje aktualne badania laboratoryjne wykonane w sieci eLab.
          </p>
        </div>
        <div class="results-header-actions">
          <button class="button-ghost" @click="handleLogout">Wyloguj</button>
        </div>
      </div>

      <div v-if="loading" class="info">
        Ładowanie danych pacjenta...
      </div>
      <div v-else-if="error" class="text-error">
        {{ error }}
      </div>
    </section>

    <section v-if="data && !loading" class="results-body">
      <!-- KARTA PACJENTA -->
      <article class="card patient-card">
        <h2>Dane pacjenta</h2>
        <div class="patient-grid">
          <div>
            <div class="label">Imię i nazwisko</div>
            <div class="value">
              {{ data.patient.name }} {{ data.patient.surname }}
            </div>
          </div>
          <div>
            <div class="label">Płeć</div>
            <div class="value">
              {{ data.patient.sex === "m" ? "Mężczyzna" : "Kobieta" }}
            </div>
          </div>
          <div>
            <div class="label">Data urodzenia</div>
            <div class="value">
              {{ data.patient.birthDate }}
            </div>
          </div>
          <div>
            <div class="label">ID pacjenta</div>
            <div class="value">#{{ data.patient.id }}</div>
          </div>
        </div>
      </article>

      <!-- ZLECENIA I WYNIKI -->
      <article class="card orders-card">
        <div class="orders-header">
          <h2>Zlecenia i wyniki</h2>
          <span class="orders-count">
            Łącznie zleceń: {{ data.orders.length }}
          </span>
        </div>

        <div v-if="data.orders.length === 0" class="info">
          Brak zleceń dla tego pacjenta.
        </div>

        <div
          v-for="order in data.orders"
          :key="order.orderId"
          class="order-block"
        >
          <div class="order-header">
            <div class="order-id">Zlecenie #{{ order.orderId }}</div>
          </div>

          <div class="results-table-wrapper">
            <table class="results-table">
              <thead>
                <tr>
                  <th>Badanie</th>
                  <th>Wartość</th>
                  <th>Zakres referencyjny</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="res in order.results" :key="res.name + res.value">
                  <td class="cell-name">{{ res.name }}</td>
                  <td class="cell-value">{{ res.value }}</td>
                  <td class="cell-ref">{{ res.reference }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </article>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { getResults } from "../api/resultsApi";

const router = useRouter();

const data = ref(null);
const loading = ref(false);
const error = ref("");

async function loadResults() {
  loading.value = true;
  error.value = "";

  try {
    const response = await getResults();
    data.value = response;
  } catch (e) {
    console.error(e);
    if (e.response && e.response.status === 401) {
      error.value = "Sesja wygasła. Zaloguj się ponownie.";
    } else {
      error.value = "Nie udało się pobrać wyników badań.";
    }
  } finally {
    loading.value = false;
  }
}

function handleLogout() {
  localStorage.removeItem("token");
  router.push({ name: "login" });
}

onMounted(() => {
  loadResults();
});
</script>

<style scoped>
.results-layout {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.results-header h1 {
  margin: 6px 0;
  font-size: 1.5rem;
  color: var(--color-primary);
}

.header-sub {
  font-size: 0.9rem;
  color: var(--color-text-muted);
}

.results-header-main {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  align-items: flex-start;
}

.results-header-actions {
  display: flex;
  gap: 8px;
}

.info {
  font-size: 0.85rem;
  color: var(--color-text-muted);
  margin-top: 6px;
}

/* Patient card */

.patient-card h2,
.orders-card h2 {
  margin-top: 0;
  font-size: 1.1rem;
}

.patient-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 12px 40px;
  margin-top: 12px;
}

.label {
  font-size: 0.75rem;
  color: var(--color-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.value {
  font-size: 0.95rem;
}

/* Orders */

.orders-header {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 12px;
}

.orders-count {
  font-size: 0.8rem;
  color: var(--color-text-muted);
}

.order-block {
  margin-top: 18px;
  border-top: 1px solid rgba(209, 213, 219, 0.8);
  padding-top: 12px;
}

.order-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
}

.order-id {
  font-weight: 600;
  font-size: 0.9rem;
}

.results-table-wrapper {
  overflow-x: auto;
}

.results-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.85rem;
}

.results-table th,
.results-table td {
  padding: 6px 8px;
  text-align: left;
}

.results-table thead tr {
  border-bottom: 1px solid #e5e7eb;
}

.results-table tbody tr:nth-child(odd) {
  background: #f9fafb;
}

.cell-name {
  width: 55%;
}

.cell-value {
  width: 15%;
}

.cell-ref {
  width: 30%;
}

@media (max-width: 640px) {
  .results-header-main {
    flex-direction: column;
  }

  .results-header-actions {
    align-self: flex-end;
  }
}
</style>
