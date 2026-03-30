<script setup>
import MainLayout from '../../Layouts/MainLayout.vue';
import PasswordInput from '../../Components/PasswordInput.vue';
import { ref, computed } from 'vue';
import { useAuthStore } from '../../stores/authStore';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const authStore = useAuthStore();
const email = ref('');
const password = ref('');

const statusMessage = computed(() => page.props.flash?.status || null);

const onLogin = async () => {
    await authStore.login({
        email: email.value,
        password: password.value
    });
};

const errorMessages = computed(() => {
    const errs = authStore.errors;
    if (!errs) return [];

    const messages = [];
    if (typeof errs === 'object') {
        Object.values(errs).forEach((val) => {
            if (Array.isArray(val)) {
                messages.push(...val);
            } else if (typeof val === 'string') {
                messages.push(val);
            }
        });
    } else if (typeof errs === 'string') {
        messages.push(errs);
    }
    return messages;
});
</script>

<template>
    <MainLayout class="login" :hideFooter="true">
        <main>

            <section class="signin_main">
                <div class="signin_main_blocks">
                    <div class="signin_main_block">
                        <div class="signin_main_rect">
                            <div class="signin_main_rect_content">
                                <h1 class="signin_main_rect_tittle">Вход в аккаунт</h1>
                                <p class="signin_subtitle">Введите данные для входа в систему</p>

                                <div class="signin_main_rect_inputs">
                                    <!-- Email -->
                                    <div class="signin_field">
                                        <label class="signin_field_label">Email</label>
                                        <div class="signin_input_wrap">
                                            <svg class="signin_input_icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                            </svg>
                                            <input v-model="email" type="email" placeholder="Введите ваш email" class="signin_main_rect_input signin_input_with_icon" autocomplete="username">
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="signin_field">
                                        <label class="signin_field_label">Пароль</label>
                                        <div class="signin_input_wrap">
                                            <svg class="signin_input_icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                            </svg>
                                            <PasswordInput
                                                v-model="password"
                                                placeholder="Введите пароль"
                                                input-class="signin_main_rect_input signin_input_with_icon"
                                                autocomplete="current-password"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <a href="/forgot-password" class="signin_main_rect_help">Забыли пароль?</a>

                                <div v-if="statusMessage" class="signin_success_message">
                                    {{ statusMessage }}
                                </div>

                                <button class="signin_main_rect_button" @click="onLogin">Войти</button>

                                <div v-if="errorMessages.length" class="signin_main_rect_errors">
                                    <ul>
                                        <li v-for="(msg, idx) in errorMessages" :key="idx">{{ msg }}</li>
                                    </ul>
                                </div>

                                <div class="signin_divider">
                                    <span>Или продолжите с помощью</span>
                                </div>

                                <div class="signin_main_rect_socials">
                                    <a href="/auth/yandex" class="signin_social_btn">
                                        <svg width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.04 12c0-5.523 4.476-10 10-10 5.522 0 10 4.477 10 10s-4.478 10-10 10c-5.524 0-10-4.477-10-10z" fill="#FC3F1D"/><path d="M13.32 7.666h-.924c-1.694 0-2.585.858-2.585 2.123 0 1.43.616 2.1 1.881 2.959l1.045.704-3.003 4.487H7.49l2.695-4.014c-1.55-1.111-2.42-2.19-2.42-4.015 0-2.288 1.595-3.85 4.62-3.85h3.003v11.868H13.32V7.666z" fill="#fff"/></svg>
                                    </a>
                                    <!-- <a href="#!" class="signin_social_btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 50 50" fill="none">
                                            <rect width="50" height="50" rx="25" fill="#EE8208" />
                                            <path d="M25.4858 12.0659C21.8959 12.0659 18.9858 14.9761 18.9858 18.5658C18.9858 22.1556 21.8959 25.0659 25.4858 25.0659C29.0757 25.0659 31.9858 22.1556 31.9858 18.5658C31.9858 14.9761 29.0757 12.0659 25.4858 12.0659ZM25.4858 21.2529C24.0019 21.2529 22.7989 20.0498 22.7989 18.5659C22.7989 17.082 24.0019 15.879 25.4858 15.879C26.9698 15.879 28.1728 17.082 28.1728 18.5659C28.1728 20.0498 26.9698 21.2529 25.4858 21.2529Z" fill="white" />
                                            <path d="M27.95 30.0885C30.6212 29.5533 32.2219 28.3092 32.3066 28.2424C33.0882 27.6259 33.2137 26.5029 32.5869 25.734C31.9601 24.9653 30.8184 24.8418 30.0366 25.4582C30.0201 25.4714 28.3129 26.7595 25.5038 26.7613C22.6949 26.7595 20.9516 25.4714 20.935 25.4582C20.1532 24.8418 19.0115 24.9653 18.3848 25.734C17.7579 26.5029 17.8835 27.6259 18.6651 28.2424C18.751 28.3101 20.4177 29.5867 23.1637 30.1093L19.3367 34.0431C18.6412 34.7525 18.6621 35.8821 19.3833 36.5661C19.7354 36.9 20.1891 37.0659 20.6425 37.0659C21.1178 37.0659 21.5926 36.8833 21.9486 36.5201L25.5039 32.7951L29.4184 36.5442C30.1276 37.2407 31.2761 37.2396 31.984 36.5423C32.6919 35.8449 32.6911 34.7151 31.982 34.0189L27.95 30.0885Z" fill="white" />
                                        </svg>
                                    </a>
                                    <a href="#!" class="signin_social_btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 50 50" fill="none">
                                            <rect width="50" height="50" rx="12" fill="#FC3F1D" />
                                            <path d="M27.4 25.5L33 16H29.2L25 22.9L20.8 16H17L22.6 25.5L16.5 36H20.3L25 28.4L29.7 36H33.5L27.4 25.5Z" fill="white" />
                                        </svg>
                                    </a> -->
                                </div>

                                <p class="signin_register_wrap">
                                    Нет аккаунта?
                                    <a href="/signup" class="signin_main_rect_signin">Зарегистрироваться</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="signin_main_block"></div>
                </div>
            </section>

        </main>
    </MainLayout>
</template>



<style scoped>
.signin_subtitle {
    margin-top: 6px;
    color: rgba(57, 60, 91, 0.55);
    font-family: "SF Pro Display", sans-serif;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.4;
}

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

.signin_main_rect_errors {
    margin-top: 12px;
    color: #ff4d4f;
    font-family: "SF Pro Display", sans-serif;
    font-size: 13px;
}

.signin_main_rect_errors ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.signin_divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 22px;
    color: rgba(57, 60, 91, 0.4);
    font-family: "SF Pro Display", sans-serif;
    font-size: 13px;
    font-weight: 400;
}

.signin_divider::before,
.signin_divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(57, 60, 91, 0.15);
}

.signin_social_btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 46px;
    border-radius: 10px;
    border: 1.5px solid rgba(57, 60, 91, 0.12);
    background: #fff;
    cursor: pointer;
    transition: border-color 0.2s, box-shadow 0.2s;
    text-decoration: none;
}

.signin_social_btn:hover {
    border-color: rgba(57, 60, 91, 0.3);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transform: none;
}

.signin_register_wrap {
    margin-top: 20px;
    text-align: center;
    color: rgba(57, 60, 91, 0.6);
    font-family: "SF Pro Display", sans-serif;
    font-size: 14px;
    font-weight: 400;
}

.signin_register_wrap .signin_main_rect_signin {
    margin-top: 0;
    display: inline;
}
</style>