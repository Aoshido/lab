// src/axios.js
import axios from 'axios';

const instance = axios.create({
    baseURL: 'http://localhost/api', // Update this URL
    timeout: 10000,
});

export default instance;