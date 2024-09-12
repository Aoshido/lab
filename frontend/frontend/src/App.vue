<template>
  <div id="app">
    <Patients/> <!-- Add your new component here -->
  </div>
</template>

<script>
import Patients from './components/Patients.vue'

export default {
  name: 'App',
  components: {
    Patients
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>