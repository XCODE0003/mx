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
    form.post(route('password.email'), {
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
                        <h1 class="signin_main_rect_tittle">Восстановление пароля</h1>
                        <p style="margin-top: 12px; color: rgba(57, 60, 91, 0.75); font-size: 15px; line-height: 140%;">
                            Укажите почту — отправим ссылку для сброса пароля.
                        </p>
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
</style>
