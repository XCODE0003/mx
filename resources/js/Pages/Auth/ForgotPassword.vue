<script setup>
import MainLayout from '../../Layouts/MainLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: { type: String, default: null },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <MainLayout class="login">
        <main>
            <section class="signin_main">
                <div class="signin_main_rect">
                    <div class="signin_main_rect_content">
                        <h1 class="signin_main_rect_tittle">Восстановление пароля</h1>
                        <p style="margin-top: 12px; color: rgba(57, 60, 91, 0.75); font-size: 15px; line-height: 140%;">
                            Укажите почту — отправим ссылку для сброса пароля.
                        </p>
                        <div class="signin_main_rect_inputs">
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="Введите почту"
                                class="signin_main_rect_input"
                                autocomplete="username"
                                required
                            >
                        </div>
                        <div v-if="status" style="margin-top: 12px; color: #52c41a; font-size: 14px;">
                            {{ status }}
                        </div>
                        <div v-if="form.errors.email" style="margin-top: 12px; color: #ff4d4f;">
                            {{ form.errors.email }}
                        </div>
                        <button
                            type="button"
                            class="signin_main_rect_button"
                            :disabled="form.processing"
                            @click="submit"
                        >
                            {{ form.processing ? 'Отправка…' : 'Отправить ссылку' }}
                        </button>
                        <a href="/login" class="signin_main_rect_signin" style="margin-top: 16px; display: inline-block;">Вернуться ко входу</a>
                    </div>
                </div>
            </section>
        </main>
    </MainLayout>
</template>

<style scoped></style>
