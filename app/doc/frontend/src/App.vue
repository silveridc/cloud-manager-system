<script setup>
import { ref, onMounted } from 'vue'
import { getList } from './api'
import LoginPage from './components/LoginPage.vue'
import MainLayout from './components/MainLayout.vue'

const authenticated = ref(false)

onMounted(async () => {
  try {
    const data = await getList()
    if (data && data.list) {
      authenticated.value = true
    }
  } catch (e) {
    authenticated.value = false
  }
})
</script>

<template>
  <div class="app-container">
    <LoginPage v-if="!authenticated" @login-success="authenticated = true" />
    <MainLayout v-else />
  </div>
</template>

<style scoped>
.app-container {
  height: 100%;
}
</style>
