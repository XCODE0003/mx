import axios from 'axios';

const axiosClient = axios.create({
    baseURL: '/',

    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },

    timeout: 10000,
});

// Attach CSRF token from meta tag for Laravel
const csrfTokenTag = document.head.querySelector('meta[name="csrf-token"]');
if (csrfTokenTag && csrfTokenTag.content) {
    axiosClient.defaults.headers.common['X-CSRF-TOKEN'] = csrfTokenTag.content;
}
axiosClient.interceptors.request.use(
    (config) => {

        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

axiosClient.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        if (error.response) {


            if (error.response.status === 404) {
                console.error('Ресурс не найден');
            }
            if (error.response.status === 419) {
                console.error('CSRF token mismatch');
            }
        }

        return Promise.reject(error);
    }
);

export default axiosClient;
