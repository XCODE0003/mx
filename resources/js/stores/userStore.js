import { defineStore } from "pinia";
import axiosClient from "../api/axios";
import { useAuthStore } from "./authStore";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
export const useUserStore = defineStore("user", {
    state: () => ({
        user: computed(() => usePage().props.auth.user),
        loading: false,
        errors: null,
    }),

    getters: {
        currentUser: (state) => state.user,
        isLoading: (state) => state.loading,
        isAuth: (state) => state.user !== null,
    },

    actions: {
        setUser(user) {
            this.user = user;
        },

        clearUser() {
            this.user = null;
        },

        clearErrors() {
            this.errors = null;
        },


        updateUserData(userData) {
            if (this.user) {
                this.user = { ...this.user, ...userData };
            }
        },

        async updatePassword($password_data) {
            try {
                this.loading = true;
                const response = await axiosClient.put(
                    "/account/password/update",
                    $password_data
                );
                this.loading = false;
                return response;
            } catch (error) {
                this.errors = error.response?.data?.errors || {
                    message: "Error update password",
                };
                return error;
            }
        },

        async createPromo(promocode, amount, win_mode) {
            try {
                const response = await axiosClient.post("/promocode/create", {
                    promocode,
                    amount,
                    win_mode,
                });
                showSuccessToast(response.data.message);
                return response.data.promo;
            } catch (error) {
                this.errors = error.response?.data?.errors || {
                    message: "Ошибка при создании промокода",
                };
                renderErrorToast(this.errors);
                return false;
            }
        },
        async deletePromo(promoId) {
            try {
                const response = await axiosClient.post(`/promocode/delete`, {
                    id: promoId,
                });
                if (response.status === 200) {
                    showSuccessToast(response.data.message);
                    return true;
                }
                return false;
            } catch (error) {
                this.errors = error.response?.data?.errors || {
                    message: "Ошибка при удалении промокода",
                };
                renderErrorToast(this.errors);
                return false;
            }
        },

        async saveSettings(settings) {
            this.loading = true;
            try {
                const response = await axiosClient.post("/settings", settings);
                if (response.status === 200) {
                    showSuccessToast(response.data.message);
                    return true;
                }
                return false;
            } catch (error) {
                this.errors = error.response?.data?.errors || {
                    message: "Ошибка при сохранении настроек",
                };
                renderErrorToast(this.errors);
                return false;
            } finally {
                this.loading = false;
            }
        },

        async addDomain(domain) {
            try {
                this.loading = true;
                const response = await axiosClient.post("/domain/create", {
                    domain,
                });
                showSuccessToast(response.data.message);
                return response.data.domain;
            } catch (error) {
                this.errors = error.response?.data?.errors || {
                    message: "Ошибка при добавлении домена",
                };
                renderErrorToast(this.errors);
                return false;
            } finally {
                this.loading = false;
            }
        },
        async deleteDomain(domain_id) {
            try {
                const response = await axiosClient.post("/domain/delete", {
                    domain_id,
                });
                showSuccessToast(response.data.message);
                return true;
            } catch (error) {
                this.errors = error.response?.data?.errors || {
                    message: "Ошибка при удалении домена",
                };
            }
        },
        async changePassword(password_data) {
            try {
                this.loading = true;
                const response = await axiosClient.post("/account/password/update", password_data);
                showSuccessToast(response.data.message);
                return response.data;
            } catch (error) {
                this.errors = error.response?.data?.errors || {
                    message: "Ошибка при изменении пароля",
                };
                renderErrorToast(this.errors.message);
                return false;
            }
            finally {
                this.loading = false;
            }
        },
    },
});
