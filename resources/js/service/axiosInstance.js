import axios from "axios";
import config from "../config.js";
const API_BAE_URL = config.API_URL_ROOT;

export const axiosPublic = axios.create({
    baseURL: API_BAE_URL,
    timeout: 60000,
});

export const axiosPrivate = axios.create({
    baseURL: API_BAE_URL,
    timeout: 60000,
});

axiosPrivate.interceptors.response.use(
    function (response) {
        // Do something with response data
        return response;
    },

    function (error) {
        // Do something with response error
        if (error.response && error.response.status == 401) {
            localStorage.removeItem("token");
            location.href = "/login";
        }
        return Promise.reject(error);
    }
);

export const setPrivateHeaders = () => {
    axiosPrivate.defaults.headers.common["Authorization"] =
        "Bearer " + localStorage.getItem("token");
};
