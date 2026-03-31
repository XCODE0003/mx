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
                            <!-- Email -->
                            <div class="signin_field">
                                <label class="signin_field_label">Email</label>
                                <div class="signin_input_wrap">
                                    <svg class="signin_input_icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        placeholder="Введите ваш email"
                                        class="signin_main_rect_input signin_input_with_icon"
                                        autocomplete="username"
                                        required
                                    >
                                </div>
                            </div>

                            <!-- New Password -->
                            <div class="signin_field">
                                <label class="signin_field_label">Новый пароль</label>
                                <div class="signin_input_wrap">
                                    <svg class="signin_input_icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                    <PasswordInput
                                        v-model="form.password"
                                        placeholder="Новый пароль"
                                        input-class="signin_main_rect_input signin_input_with_icon"
                                        autocomplete="new-password"
                                    />
                                </div>
                            </div>

                            <!-- Password Confirmation -->
                            <div class="signin_field">
                                <label class="signin_field_label">Повторите пароль</label>
                                <div class="signin_input_wrap">
                                    <svg class="signin_input_icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                    <PasswordInput
                                        v-model="form.password_confirmation"
                                        placeholder="Повторите пароль"
                                        input-class="signin_main_rect_input signin_input_with_icon"
                                        autocomplete="new-password"
                                    />
                                </div>
                            </div>
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

<style scoped>
.signin_field {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.signin_field_label {
    color: #393c5b;
    font-family: "SF Pro Display", sans-serif;
    font-size: 14px;
    font-weight: 500;
    line-height: 1.2;
}

.signin_input_wrap {
    position: relative;
    width: 100%;
    display: flex;
    align-items: center;
}

.signin_input_icon {
    position: absolute;
    left: 14px;
    width: 18px;
    height: 18px;
    color: rgba(57, 60, 91, 0.45);
    pointer-events: none;
    flex-shrink: 0;
    z-index: 1;
}

.signin_input_with_icon {
    padding-left: 42px !important;
}

.signin_input_wrap :deep(.password-input-wrap) {
    width: 100%;
}

.signin_input_wrap :deep(.password-input-wrap input) {
    padding-left: 42px;
}
</style>
