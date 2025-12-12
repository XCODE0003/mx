import { defineStore } from 'pinia';
import axiosClient from '../api/axios';
import { useUserStore } from './userStore';
import { router } from '@inertiajs/vue3';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        loading: false,
        errors: null
    }),

    getters: {
        isLoading: (state) => state.loading
    },

    actions: {
        async login(credentials) {
            try {
                this.loading = true;
                this.errors = null;

                const response = await axiosClient.post('/login', credentials);

                if (response.status === 200) {
                    const userStore = useUserStore();
                    userStore.setUser(response.data.user);
                    window.location.href = '/profile';
                }

                return response;
            } catch (error) {
                this.errors = error.response?.data?.errors || error.response?.data || {
                    message: 'Error login'
                };
            } finally {
                this.loading = false;
            }
        },

        async register(credentials) {
            try {
                this.loading = true;
                this.errors = null;

                const response = await axiosClient.post('/register', credentials);

                if (response.status === 201) {
                    router.visit('/login');
                }

                return response;
            } catch (error) {
                this.errors = error.response?.data?.errors || error.response?.data || {
                    message: 'Registration failed'
                };
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            try {
                router.visit('/logout');
            } catch (error) {
                console.error('Logout error:', error);
            }
        },

        clearAuth() {
            this.token = null;
            localStorage.removeItem('token');
        },

        clearErrors() {
            this.errors = null;
        },

        initializeFromProps(auth) {
            if (auth?.user) {
                const userStore = useUserStore();
                userStore.setUser(auth.user);
            }
        }
    }
});