<template>
  <div>
    <h2>Patients Data</h2>
    <div v-if="data && data['hydra:member']">
      <ul>
        <li v-for="patient in data['hydra:member']" :key="patient['@id']">
          {{ patient.name }}
        </li>
      </ul>
    </div>
    <div v-else>
      Loading or no data available...
    </div>
  </div>
</template>

<script>
import axios from '../axios'; // Ensure the path is correct

export default {
  name: 'Patients',
  data() {
    return {
      data: null,
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    async fetchData() {
      try {
        const response = await axios.get('/patients'); // Verify endpoint
        console.log('API Response:', response.data); // Check the structure of the response
        this.data = response.data;
      } catch (error) {
        console.error('There was an error!', error);
      }
    },
  },
};
</script>

<style scoped>
/* Add component-specific styles here */
</style>
