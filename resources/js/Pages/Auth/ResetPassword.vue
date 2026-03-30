<script setup>
import MainLayout from '../../Layouts/MainLayout.vue';
import PasswordInput from '../../Components/PasswordInput.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    token: { type: String, required: true },
    email: { type: String, default: '' },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onSuccess: () => {
            // Редирект на login с сообщением об успехе происходит на backend
        },
        preserveScroll: true,
    });
};
</script>

<template>
    <MainLayout class="login" :hideFooter="true">
        <main>
            <section class="signin_main">
                <div class="signin_main_rect">
                    <div class="signin_main_rect_content">
                        <h1 class="signin_main_rect_tittle">Новый пароль</h1>
                        <div class="signin_main_rect_inputs">
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="Почта"
                                class="signin_main_rect_input"
                                autocomplete="username"
                                required
                            >
                            <PasswordInput
                                v-model="form.password"
                                placeholder="Новый пароль"
                                input-class="signin_main_rect_input"
                                autocomplete="new-password"
                            />
                            <PasswordInput
                                v-model="form.password_confirmation"
                                placeholder="Повторите пароль"
                                input-class="signin_main_rect_input"
                                autocomplete="new-password"
                            />
                        </div>
                        <div v-if="form.errors.email" style="margin-top: 8px; color: #ff4d4f;">
                            {{ form.errors.email }}
                        </div>
                        <div v-if="form.errors.password" style="margin-top: 8px; color: #ff4d4f;">
                            {{ form.errors.password }}
                        </div>
                        <button
                            type="button"
                            class="signin_main_rect_button"
                            :disabled="form.processing"
                            @click="submit"
                        >
                            {{ form.processing ? 'Сохранение…' : 'Сохранить пароль' }}
                        </button>
                        <a href="/login" class="signin_main_rect_signin" style="margin-top: 16px; display: inline-block;">Вернуться ко входу</a>
                    </div>
                </div>
            </section>
        </main>
    </MainLayout>
</template>

<style scoped></style>
