<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router';
import HelloWorld from './components/HelloWorld.vue';
import axios from 'axios';
import { onMounted } from 'vue';

const callBackendApi = async () => {
  try {
    const response = await axios.get('http://localhost:4000/test/hello');
    console.log('Backend Response:', response.data);
    alert('Backend says: ' + response.data.message); // Simple way to show it's working
  } catch (error) {
    console.error('Error calling backend:', error);
    alert('Error calling backend! Check console.');
  }
};

onMounted(() => {
  // You can call the API when the component is mounted,
  // or attach it to a button click instead.
  // callBackendApi(); // Uncomment this to call on mount
});
</script>

<template>
  <header>
    <img alt="Vue logo" class="logo" src="@/assets/logo.svg" width="125" height="125" />

    <div class="wrapper">
      <HelloWorld msg="You did it!" />

      <nav>
        <RouterLink to="/">Home</RouterLink>
        <RouterLink to="/about">About</RouterLink>
        <button @click="callBackendApi">Test Backend API</button>
      </nav>
    </div>
  </header>

  <RouterView />
</template>

<style scoped>
header {
  line-height: 1.5;
  max-height: 100vh;
}

.logo {
  display: block;
  margin: 0 auto 2rem;
}

nav {
  width: 100%;
  font-size: 12px;
  text-align: center;
  margin-top: 2rem;
}

nav a.router-link-exact-active {
  color: var(--color-text);
}

nav a.router-link-exact-active:hover {
  background-color: transparent;
}

nav a {
  display: inline-block;
  padding: 0 1rem;
  border-left: 1px solid var(--color-border);
}

nav a:first-of-type {
  border: 0;
}

nav button {
  display: inline-block;
  padding: 0.5rem 1rem;
  margin-left: 1rem;
  border: 1px solid var(--color-border);
  border-radius: 5px;
  background-color: #f0f0f0;
  cursor: pointer;
}

nav button:hover {
  background-color: #e0e0e0;
}

@media (min-width: 1024px) {
  header {
    display: flex;
    place-items: center;
    padding-right: calc(var(--section-gap) / 2);
  }

  .logo {
    margin: 0 2rem 0 0;
  }

  header .wrapper {
    display: flex;
    place-items: flex-start;
    flex-wrap: wrap;
  }

  nav {
    text-align: left;
    margin-left: -1rem;
    font-size: 1rem;

    padding: 1rem 0;
    margin-top: 1rem;
  }

  nav button {
    margin-top: 0;
  }
}
</style>
