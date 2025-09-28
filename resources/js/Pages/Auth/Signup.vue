
<script setup>
import MainLayout from '../../Layouts/MainLayout.vue';
import { ref, computed } from 'vue';
import { useAuthStore } from '../../stores/authStore';

const authStore = useAuthStore();
const email = ref('');
const password = ref('');
const passwordConfirm = ref('');

const onRegister = async () => {
    if (password.value !== passwordConfirm.value) {
        authStore.errors = { password: ['Пароли не совпадают'] };
        return;
    }

    await authStore.register({
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
    <MainLayout class="login">
        <main>

            <section class="signin_main">
                <div class="signin_main_rect">
                    <div class="signin_main_rect_content">
                        <h1 class="signin_main_rect_tittle">Регистация</h1>
                        <div class="signin_main_rect_inputs">
                            <input v-model="email" type="email" placeholder="Введите почту" class="signin_main_rect_input">
                            <input v-model="password" type="password" placeholder="Введите пароль" class="signin_main_rect_input">
                            <input v-model="passwordConfirm" type="password" placeholder="Повторите пароль" class="signin_main_rect_input">
                        </div>
                        <button class="signin_main_rect_button" id="registerBtn" @click="onRegister">Зарегистрироваться</button>
                        <div v-if="errorMessages.length" class="signin_main_rect_errors" style="margin-top: 12px; color: #ff4d4f;">
                            <ul>
                                <li v-for="(msg, idx) in errorMessages" :key="idx">{{ msg }}</li>
                            </ul>
                        </div>
                        <div class="signin_main_rect_socials">
                            <a href="#!" class="signin_main_rect_social"><svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50" fill="none">
                                    <rect width="50" height="50" rx="25" fill="#005FF9" />
                                    <path d="M29.5118 25C29.5118 27.4879 27.4879 29.5118 25 29.5118C22.5121 29.5118 20.4882 27.4879 20.4882 25C20.4882 22.5121 22.5121 20.4882 25 20.4882C27.4879 20.4882 29.5118 22.5121 29.5118 25ZM25 10C16.7286 10 10 16.7286 10 25C10 33.2714 16.7286 40 25 40C28.03 40 30.9518 39.0979 33.4493 37.3911L33.4921 37.3611L31.4714 35.0125L31.4371 35.0339C29.515 36.2714 27.2886 36.925 25 36.925C18.4246 36.925 13.075 31.5754 13.075 25C13.075 18.4246 18.4246 13.075 25 13.075C31.5754 13.075 36.925 18.4246 36.925 25C36.925 25.8518 36.8296 26.7143 36.6443 27.5629C36.2671 29.1111 35.1829 29.5846 34.3696 29.5225C33.5511 29.4561 32.5932 28.8732 32.5868 27.4461V26.3586V25C32.5868 20.8161 29.1839 17.4132 25 17.4132C20.8161 17.4132 17.4132 20.8161 17.4132 25C17.4132 29.1839 20.8161 32.5868 25 32.5868C27.0325 32.5868 28.9386 31.7929 30.3754 30.3475C31.2111 31.6482 32.5729 32.4636 34.1232 32.5879C34.2561 32.5986 34.3921 32.6039 34.5261 32.6039C35.6179 32.6039 36.6989 32.2386 37.5711 31.5775C38.47 30.8939 39.1418 29.9071 39.5125 28.7211C39.5714 28.5293 39.6807 28.0911 39.6807 28.0879L39.6839 28.0718C39.9025 27.1204 40 26.1721 40 25C40 16.7286 33.2714 10 25 10Z" fill="#FF9620" />
                                </svg></a>
                            <a href="#!" class="signin_main_rect_social"><svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50" fill="none">
                                    <rect width="50" height="50" rx="25" fill="#EE8208" />
                                    <path d="M25.4858 12.0659C21.8959 12.0659 18.9858 14.9761 18.9858 18.5658C18.9858 22.1556 21.8959 25.0659 25.4858 25.0659C29.0757 25.0659 31.9858 22.1556 31.9858 18.5658C31.9858 14.9761 29.0757 12.0659 25.4858 12.0659ZM25.4858 21.2529C24.0019 21.2529 22.7989 20.0498 22.7989 18.5659C22.7989 17.082 24.0019 15.879 25.4858 15.879C26.9698 15.879 28.1728 17.082 28.1728 18.5659C28.1728 20.0498 26.9698 21.2529 25.4858 21.2529Z" fill="white" />
                                    <path d="M27.95 30.0885C30.6212 29.5533 32.2219 28.3092 32.3066 28.2424C33.0882 27.6259 33.2137 26.5029 32.5869 25.734C31.9601 24.9653 30.8184 24.8418 30.0366 25.4582C30.0201 25.4714 28.3129 26.7595 25.5038 26.7613C22.6949 26.7595 20.9516 25.4714 20.935 25.4582C20.1532 24.8418 19.0115 24.9653 18.3848 25.734C17.7579 26.5029 17.8835 27.6259 18.6651 28.2424C18.751 28.3101 20.4177 29.5867 23.1637 30.1093L19.3367 34.0431C18.6412 34.7525 18.6621 35.8821 19.3833 36.5661C19.7354 36.9 20.1891 37.0659 20.6425 37.0659C21.1178 37.0659 21.5926 36.8833 21.9486 36.5201L25.5039 32.7951L29.4184 36.5442C30.1276 37.2407 31.2761 37.2396 31.984 36.5423C32.6919 35.8449 32.6911 34.7151 31.982 34.0189L27.95 30.0885Z" fill="white" />
                                </svg></a>
                            <a href="#!" class="signin_main_rect_social"><svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50" fill="none">
                                    <rect width="50" height="50" rx="25" fill="#4D77A2" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M24.6567 33.9245H26.5694C26.5694 33.9245 27.147 33.8617 27.4423 33.5484C27.7138 33.2605 27.7051 32.72 27.7051 32.72C27.7051 32.72 27.6677 30.1896 28.8586 29.8169C30.0331 29.4496 31.5409 32.2625 33.139 33.3442C34.3475 34.1625 35.2659 33.9834 35.2659 33.9834L39.5393 33.9245C39.5393 33.9245 41.7747 33.7885 40.7147 32.0555C40.628 31.914 40.0972 30.7736 37.5372 28.4307C34.8574 25.9784 35.2166 26.3752 38.4444 22.1333C40.4102 19.5501 41.1959 17.9731 40.9504 17.2977C40.7164 16.6542 39.2705 16.8241 39.2705 16.8241L34.4589 16.8535C34.4589 16.8535 34.102 16.8056 33.8376 16.9616C33.579 17.1141 33.413 17.4705 33.413 17.4705C33.413 17.4705 32.6512 19.4694 31.6359 21.1696C29.4934 24.7567 28.6365 24.9466 28.2863 24.7235C27.4715 24.2043 27.6751 22.6382 27.6751 21.5253C27.6751 18.0488 28.2099 16.5994 26.6337 16.2242C26.1107 16.0998 25.7255 16.0174 24.3878 16.004C22.6709 15.9868 21.2181 16.0092 20.3953 16.4066C19.8478 16.6709 19.4255 17.2598 19.6829 17.2937C20.001 17.3354 20.721 17.4853 21.1028 17.9975C21.596 18.6591 21.5788 20.1444 21.5788 20.1444C21.5788 20.1444 21.8622 24.2366 20.9171 24.7448C20.2686 25.0935 19.3789 24.3817 17.4687 21.1273C16.4901 19.4603 15.751 17.6174 15.751 17.6174C15.751 17.6174 15.6087 17.2731 15.3545 17.0888C15.0462 16.8655 14.6154 16.7947 14.6154 16.7947L10.043 16.8241C10.043 16.8241 9.35676 16.843 9.10458 17.1373C8.88023 17.3993 9.08666 17.9405 9.08666 17.9405C9.08666 17.9405 12.6661 26.1979 16.7196 30.3592C20.4366 34.1749 24.6567 33.9245 24.6567 33.9245Z" fill="white" />
                                </svg></a>
                        </div>
                        <a href="/login" class="signin_main_rect_signin">Уже есть аккаунт?</a>
                    </div>
                </div>
            </section>

        </main>
    </MainLayout>
</template>


<style scoped></style>