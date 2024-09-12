<template>
  <div class="container">
    <h2 class="header">Patient Serum Records</h2>
    <div v-if="data && data['hydra:member']" class="table-container">
      <table class="styled-table">
        <thead>
        <tr>
          <th>Patient Name</th>
          <th>Number of Serums</th>
          <th>Serum Dates</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="patient in data['hydra:member']" :key="patient['@id']">
          <td>{{ patient.name }}</td>
          <td>{{ patient.serums.length }}</td>
          <td>
            <ul v-if="patient.serums.length > 0" class="serum-list">
              <li v-for="serum in patient.serums" :key="serum.id">
                {{ new Date(serum.date).toLocaleDateString() }}
              </li>
            </ul>
            <p v-else>No serums available</p>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
    <div v-else class="loading-message">
      <p>Loading or no data available...</p>
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
        const response = await axios.get('/patients');
        this.data = response.data;
      } catch (error) {
        console.error('Error fetching data:', error);
      }
    },
  },
};
</script>

<style scoped>
/* General Container */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f9f9f9;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Header Styling */
.header {
  font-size: 28px;
  font-weight: 700;
  text-align: center;
  margin-bottom: 20px;
  color: #2c3e50;
}

/* Table Styling */
.styled-table {
  width: 100%;
  border-collapse: collapse;
  margin: 25px 0;
  font-size: 18px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  background-color: white;
  border-radius: 10px;
  overflow: hidden;
}

/* Table Header */
.styled-table thead tr {
  background-color: #34495e;
  color: white;
  text-align: left;
  font-weight: bold;
}

.styled-table th, .styled-table td {
  padding: 15px;
}

/* Table Rows */
.styled-table tbody tr {
  border-bottom: 1px solid #dddddd;
}

.styled-table tbody tr:nth-of-type(even) {
  background-color: #f3f3f3;
}

.styled-table tbody tr:last-of-type {
  border-bottom: 2px solid #34495e;
}

.styled-table tbody tr:hover {
  background-color: #f1f1f1;
  cursor: pointer;
}

/* Serum List */
.serum-list {
  margin: 0;
  padding: 0;
  list-style-type: none;
}

.serum-list li {
  background: #ecf0f1;
  padding: 8px;
  margin: 4px 0;
  border-radius: 5px;
  font-size: 16px;
  color: #34495e;
}

/* Loading Message */
.loading-message {
  text-align: center;
  font-size: 20px;
  font-style: italic;
  color: #888;
}
</style>
