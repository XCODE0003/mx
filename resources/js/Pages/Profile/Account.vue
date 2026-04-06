<script setup>
import MainLayout from '../../Layouts/MainLayout.vue';
import PasswordInput from '../../Components/PasswordInput.vue';
import { ref, computed } from 'vue';
import axiosClient from '../../api/axios';
import { useUserStore } from '../../stores/userStore';
import Aside from '../../Components/Profile/Aside.vue';
import { usePage } from '@inertiajs/vue3';

const userStore = useUserStore();
const currentUser = computed(() => userStore.currentUser);
const page = usePage();
const registrationDiscount = computed(() => page.props.auth?.registration_discount ?? null);

const email = ref(currentUser.value?.email || '');

const currentPassword = ref('');
const newPassword = ref('');
const newPasswordConfirm = ref('');

const profileErrors = ref([]);
const passwordErrors = ref([]);

const profileSaved = ref(false);
const passwordSaved = ref(false);

const onSaveProfile = async () => {
    profileErrors.value = [];
    try {
        const response = await axiosClient.patch('/settings/profile', {
            email: email.value,
        });
        if (response?.data?.user) {
            userStore.updateUserData(response.data.user);
        }
        profileSaved.value = true;
        setTimeout(() => {
            profileSaved.value = false;
        }, 2000);
    } catch (error) {
        const errs = error.response?.data?.errors || error.response?.data || {};
        profileErrors.value = Object.values(errs).flat();
    }
};

const onChangePassword = async () => {
    passwordErrors.value = [];
    if (newPassword.value !== newPasswordConfirm.value) {
        passwordErrors.value = ['Пароли не совпадают'];
        return;
    }
    try {
        await axiosClient.put('/settings/password', {
            current_password: currentPassword.value,
            password: newPassword.value,
            password_confirmation: newPasswordConfirm.value,
        });
        currentPassword.value = '';
        newPassword.value = '';
        newPasswordConfirm.value = '';
        passwordSaved.value = true;
        setTimeout(() => {
            passwordSaved.value = false;
        }, 2000);
    } catch (error) {
        const errs = error.response?.data?.errors || error.response?.data || {};
        passwordErrors.value = Object.values(errs).flat();
    }
};
</script>

<template>
    <MainLayout>
        <main class="account">

            <section class="navigations_main">
                <div class="container">
                    <div class="navigations">
                        <a href="/" class="navigation_item_link"><svg xmlns="http://www.w3.org/2000/svg" width="19" height="12" viewBox="0 0 19 12" fill="none">
                                <path d="M0.469669 5.46967C0.176777 5.76256 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.469669 5.46967ZM19 5.25L1 5.25V6.75L19 6.75V5.25Z" fill="#848CE4" />
                            </svg> Назад</a>
                        <p class="navigation_item_text">Главная / Профиль</p>
                    </div>
                </div>
            </section>



            <section class="profile">
                <div class="container">
                    <h1 class="profile_tittle">Аккаунт</h1>
                    <div class="profile_blocks">

                        <div class="profile_block">
                           <Aside active="account" />
                        </div>

                        <div class="profile_block">
                            <div v-if="registrationDiscount" class="profile_block_rect profile_discount_block">
                                <div class="profile_block_rect_content">
                                    <div class="profile_discount">
                                        <span class="profile_discount_badge">-{{ registrationDiscount.discount_percent }}%</span>
                                        <div class="profile_discount_info">
                                            <p class="profile_discount_title">Ваша скидка на подписку</p>
                                            <p class="profile_discount_desc">Вы зарегистрировались в период акции и получаете скидку {{ registrationDiscount.discount_percent }}% при первой покупке подписки.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="profile_block_rect">
                                <div class="profile_block_rect_content">
                                    <h3 class="profile_block_rect_tittle">Настройки аккаунта</h3>


                                    <div class="profile_account_up">
                                        <div class="profile_account_up_item">
                                            <p class="profile_account_up_item_tittle">Почта</p>
                                            <div class="profile_account_up_item_rect">
                                                <input type="email" placeholder="Введите почту" v-model="email" class="profile_account_up_item_input">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="profile_account_middle">
                                        <h4 class="profile_account_middle_tittle">Смена почты</h4>
                                        <div class="profile_account_middle_change">
                                            <button class="profile_account_middle_change_button" @click="onSaveProfile">{{ profileSaved ? 'Сохранено' : 'Сохранить профиль' }}</button>
                                        </div>
                                        <div v-if="profileErrors.length" style="margin-top: 8px; color: #ff4d4f;">
                                            <ul>
                                                <li v-for="(msg, i) in profileErrors" :key="i">{{ msg }}</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="profile_account_bottom">
                                        <h4 class="profile_account_middle_tittle">Смена пароля</h4>
                                        <div class="profile_account_bottom_items">
                                            <div class="profile_account_bottom_item">
                                                <p class="profile_account_up_item_tittle">Введите пароль</p>
                                                <PasswordInput
                                                    v-model="currentPassword"
                                                    placeholder="Введите текущий пароль"
                                                    input-class="profile_account_up_item_input"
                                                    autocomplete="current-password"
                                                />
                                            </div>
                                            <div class="profile_account_bottom_item">
                                                <p class="profile_account_up_item_tittle">Введите новый пароль</p>
                                                <PasswordInput
                                                    v-model="newPassword"
                                                    placeholder="Введите новый пароль"
                                                    input-class="profile_account_up_item_input"
                                                    autocomplete="new-password"
                                                />
                                            </div>
                                            <div class="profile_account_bottom_item">
                                                <p class="profile_account_up_item_tittle">Повторите новый пароль</p>
                                                <PasswordInput
                                                    v-model="newPasswordConfirm"
                                                    placeholder="Повторите новый пароль"
                                                    input-class="profile_account_up_item_input"
                                                    autocomplete="new-password"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <button class="profile_account_save" @click="onChangePassword">{{ passwordSaved ? 'Сохранено' : 'Сохранить изменения' }}</button>
                                    <div v-if="passwordErrors.length" style="margin-top: 8px; color: #ff4d4f;">
                                        <ul>
                                            <li v-for="(msg, i) in passwordErrors" :key="i">{{ msg }}</li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        </main>
    </MainLayout>
</template>

<style scoped>
.profile_discount_block {
    margin-bottom: 16px;
}

.profile_discount {
    display: flex;
    align-items: center;
    gap: 16px;
}

.profile_discount_badge {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: #8f70ff;
    color: #fff;
    font-size: 20px;
    font-weight: 700;
}

.profile_discount_title {
    font-size: 16px;
    font-weight: 600;
    color: #393c5b;
    margin-bottom: 4px;
}

.profile_discount_desc {
    font-size: 14px;
    color: #6b6e8e;
    margin: 0;
}
</style>
