<template>
  <div class="contacts-container">
    <div class="contacts-header">
      <button class="return-button"><i class="fas fa-arrow-left"></i></button>
      <div class="search-container">
        <input class="input" @change="search" type="text" placeholder="Pesquisar" v-model="searchText" />
        <button class="search-button" @click="search">Pesquisar</button>
      </div>
      <div class="button-container">
        <button @click="goTocreateContacts()" class="create-button">Criar</button>
      </div>
    </div>
    <hr class="separator">
    <div class="tabela-container">
      <table class="table-contacts">
        <thead>
          <th>
            Nome
          </th>
          <th>
            E-mail
          </th>
          <th>
            Endereço
          </th>
          <th class="action">
            Ações
          </th>
        </thead>
        <tbody>
          <tr v-for="contact in paginatedContacts" v-bind:key="contact.id">
            <td>{{ contact.name }}</td>
            <td align="center">{{ contact.email }}</td>
            <td align="center">{{ contact.address }}</td>
            <td>
              <div class="buttons-container">
                <button @click="goToEditContacts(contact.id)" class="search-button">Editar</button>
                <button @click="deleteContacts(contact.id)" class="remove-button">Remover</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="pagination">
      <button @click="previousPage" :disabled="currentPage === 1" :class="{ active: currentPage !== 1 }">Anterior</button>
      <button v-for="page in totalPages" :key="page" @click="goToPage(page)" :class="{ active: page === currentPage }">{{
        page }}</button>
      <button @click="nextPage" :disabled="currentPage === totalPages"
        :class="{ active: currentPage !== totalPages }">Próxima</button>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { ref, computed } from 'vue';
import router from '@/router';
import http from '@/services/http';
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore();
const contacts = ref([]);
const searchText = ref('');
const currentPage = ref(1);
const pageSize = 9;

const goTocreateContacts = () => {
  router.push({ name: 'createContacts' });
};

const goToEditContacts = (id) => {
  router.push({ path: 'contacts/edit/' + id });
};

const fetchData = async () => {
  try {
    const tokenAuth = 'Bearer ' + auth.token;
    const response = await http.get('/contacts', {
      headers: {
        Authorization: tokenAuth
      }
    });
    contacts.value = response.data;
  } catch (error) {
    console.error('Erro ao fazer requisição:', error);
  }
};

const deleteContacts = async (id) => {
  try {
    const tokenAuth = 'Bearer ' + auth.token;
    await http.delete('/contacts/' + id, {
      headers: {
        Authorization: tokenAuth
      }
    });
    fetchData();
  } catch (error) {
    console.error('Erro ao fazer requisição:', error);
  }
};

const filteredContacts = computed(() => {
  const searchLower = searchText.value.toLowerCase();
  return contacts.value.filter(contact => {
    return contact.name.toLowerCase().includes(searchLower) ||
      contact.email.toLowerCase().includes(searchLower) ||
      contact.address.toLowerCase().includes(searchLower);
  });
});

const paginatedContacts = computed(() => {
  const start = (currentPage.value - 1) * pageSize;
  return filteredContacts.value.slice(start, start + pageSize);
});

const totalPages = computed(() => {
  return Math.ceil(filteredContacts.value.length / pageSize);
});

const previousPage = () => {
  currentPage.value--;
};

const goToPage = (page) => {
  currentPage.value = page;
};

const nextPage = () => {
  currentPage.value++;
};

const search = () => {
  if (searchText.value) {
    filteredContacts();
  } else {
    fetchData();
  }
};

onMounted(() => {
  fetchData();
});

</script>
<style>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
.separator {
  width: 100%;
  border-bottom: 0.1px solid rgba(0, 0, 0, 0.1);
  margin: 5px 0;
}

.button-container {
  display: flex;
  justify-content: center;
  width: 19.5%;
}

.search-container {
  display: flex;
  justify-content: center;
  width: 75.5%;
}

.contacts-container {
  display: grid;
  grid-template-columns: 1fr;
  grid-template-rows: 40px 2px 1fr;
  grid-column-gap: 0px;
  grid-row-gap: 0px;
  height: 87.1%;
  font-family: 'Helvetica Neue', sans-serif;
  width: 98%;
  margin: 1% 0 0 0;
  padding: 0 1% 0 1%;
  background-color: var(--container-background-color);
  box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.contacts-header {
  display: flex;
  justify-content: space-around;
  gap: 0.5%;
  flex-wrap: wrap;
  width: 100%;
}

.input {
  width: 100%;
  padding: 10px;
  border-radius: 5px;
  box-sizing: border-box;
  border: 2px solid var(--edit-button-color);
}

.input:focus {
  outline: none;
  border: 2px solid var(--edit-button-color);
}

.search-button,
.create-button,
.return-button {
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  background-color: var(--edit-button-color);
  color: var(--button-text-color);
  cursor: pointer;
}

.create-button {
  background-color: var(--light-green);
  width: 100%;
}

.create-button:hover {
  background-color: var(--darker-green);
}

.search-button:hover,
.return-button:hover {
  background-color: var(--edit-button-hover-color);
}
.return-button{
  width: 3%;
}
.table-contacts {
  width: 100%;
  border-collapse: collapse;
}

.table-contacts th {
  padding: 10px;
  font-family: 'Helvetica Neue', sans-serif;
  font-weight: 500;
  font-size: 14px;
}

.table-contacts td {
  padding: 10px;
  color: var(--dark-gray);
}

.table-contacts th.action{
  width: 150px;
}

.table-contacts .buttons-container {
    display: flex;
    justify-content: center;
    gap: 5px;
}

.pagination {
  position: fixed;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  padding: 10px 0;
  background-color: var(--container-background-color);
  box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
  width: 100%;
  text-align: center;
}

.pagination button {
  padding: 8px 12px;
  margin: 0 3px;
  border: none;
  border-radius: 4px;
  background-color: var(--edit-button-color);
  color: var(--button-text-color);
  cursor: pointer;
}

.pagination button:hover {
  background-color: var(--edit-button-hover-color);
}

.pagination button.active {
  background-color: var(--pagination-active-color);
  color: var(--button-text-color);
}

</style>