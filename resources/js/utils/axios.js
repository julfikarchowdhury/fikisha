import axios from 'axios';
import config from "../config.js";
const axiosIns = axios.create({
    // You can add your headers here
    // ================================
    baseURL: config.API_URL_ROOT,
    timeout: 1000,
    headers: {
        Accept: "application/json",
        apiKey: "123456rx-ecourier123456",
        ContentType: "application/json",
    },
});

export default axiosIns;
