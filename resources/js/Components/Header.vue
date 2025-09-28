<script setup>
import { useUserStore } from '../stores/userStore';
import { computed, onMounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useTaskStore } from '../stores/taskStore';

const userStore = useUserStore();
const user = computed(() => userStore.currentUser);
const isMobileMenuOpen = ref(false);

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
};

onMounted(async () => {
    await useTaskStore().getSubjects();
    console.log(useTaskStore().subjects);
})
</script>


<template>
    <header>
        <div class="container">
            <div :class="{ 'autharization': user != null }" class="header_items">
                <div class="header_item">
                    <a href="/" class="header_item_logo"><img src="/assets/img/logo.svg" style="width: 100px;" alt=""></a>
                </div>
                <div class="header_item">
                    <ul class="header_item_navs">
                        <li class="header_item_nav"><a href="/">Главная</a></li>
                        <li class="header_item_nav"><a href="/profile">Создать вариант</a></li>
                        <li class="header_item_nav"><a href="/reviews">Отзывы</a></li>
                        <li class="header_item_nav"><a href="/news">Новости</a></li>
                        <li class="header_item_nav"><a href="/faq">FAQ</a></li>
                        <li class="header_item_nav"><a href="/banks">Банк заданий ФИПИ</a></li>
                    </ul>
                </div>
                <div class="header_item">
                    <div  class="header_item_buttons">
                        <a href="/signup"><button class="header_button">Регистрация</button></a>
                        <a href="/login"><button class="header_button">Вход</button></a>
                    </div>
                    <Link :href="`/profile`"  class="header_item_user">
                        <p class="header_item_user_email">{{ user?.email }}</p>
                        <img src="/assets/img/header_user.svg" alt="" class="header_item_user_icon">
                    </Link>
                </div>
                <svg @click="toggleMobileMenu" class="header_mobile_burger" xmlns="http://www.w3.org/2000/svg" width="26" height="21" viewBox="0 0 26 21" fill="none">
                    <rect width="26" height="5" rx="2.5" fill="#8F70FF" />
                    <rect y="8" width="26" height="5" rx="2.5" fill="#8F70FF" />
                    <rect y="16" width="26" height="5" rx="2.5" fill="#8F70FF" />
                </svg>
                <transition name="mobile-menu">
                    <div v-if="isMobileMenuOpen" class="header_mobile">
                        <div class="header_mobile_menu">
                            <div @click="closeMobileMenu" class="header_mobile_menu_overlay"></div>
                            <div class="header_mobile_menu_rect">
                                <div class="header_mobile_menu_rect_content">
                                    <div class="header_mobile_menu_rect_up">
                                        <a href="/" class="header_item_logo"><img src="/assets/img/logo.svg" style="width: 100px;" alt=""></a>
                                        <svg @click="closeMobileMenu" class="header_mobile_menu_close" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <path d="M15.7002 0.737796C16.6839 -0.245634 18.2785 -0.245683 19.2622 0.737796C20.2459 1.72152 20.2459 3.31707 19.2622 4.3008L13.562 10L19.2622 15.7002C20.2459 16.684 20.2459 18.2785 19.2622 19.2622C18.2785 20.2459 16.6839 20.2459 15.7002 19.2622L9.99993 13.562L4.29969 19.2622C3.31594 20.2458 1.72134 20.2459 0.737652 19.2622C-0.245871 18.2785 -0.245897 16.6839 0.737652 15.7002L6.43789 10L0.737652 4.29981C-0.245777 3.31606 -0.245976 1.72142 0.737652 0.737796C1.72139 -0.245932 3.31694 -0.245932 4.30067 0.737796L9.99993 6.43702L15.7002 0.737796Z" fill="#8F70FF" />
                                        </svg>
                                    </div>

                                    <div class="header_item">
                                        <div v-if="!user" class="header_item_buttons">
                                            <a href="/signup"><button class="header_button">Регистрация</button></a>
                                            <a href="/login"><button class="header_button">Вход</button></a>
                                        </div>
                                        <Link v-if="user" :href="`/profile`" class="header_item_user">
                                            <p class="header_item_user_email">{{ user?.email }}</p>
                                            <img src="/assets/img/header_user.svg" alt="" class="header_item_user_icon">
                                        </Link>
                                    </div>

                                    <ul class="header_item_navs">
                                        <li class="header_item_nav"><a href="/" @click="closeMobileMenu">Главная</a></li>
                                        <li class="header_item_nav"><a href="/profile" @click="closeMobileMenu">Создать вариант</a></li>
                                        <li class="header_item_nav"><a href="/reviews" @click="closeMobileMenu">Отзывы</a></li>
                                        <li class="header_item_nav"><a href="/news" @click="closeMobileMenu">Новости</a></li>
                                        <li class="header_item_nav"><a href="/faq" @click="closeMobileMenu">FAQ</a></li>
                                        <li class="header_item_nav"><a href="/banks" @click="closeMobileMenu">Банк заданий ФИПИ</a></li>
                                    </ul>

                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </header>
</template>

<style scoped>
/* Переопределяем базовые стили для работы с Vue transitions */
.header_mobile_menu {
  display: block !important;
  position: fixed;
  left: 0;
  top: 0;
  z-index: 11;
  width: 100%;
  height: 100vh;
}

.header_mobile_menu_rect {
  width: 317px;
  height: 100%;
  background: #fff;
  position: absolute;
  right: 0;
  top: 0;
  z-index: 11;
  transition: transform 0.3s ease;
}

.header_mobile_menu_overlay {
  width: 100%;
  height: 100%;
  position: fixed;
  left: 0;
  top: 0;
  z-index: 9;
  background: rgba(0, 0, 0, 0.6);
  transition: opacity 0.3s ease;
}

/* Vue transition стили */
.mobile-menu-enter-active {
  transition: all 0.3s ease;
}

.mobile-menu-leave-active {
  transition: all 0.3s ease;
}

.mobile-menu-enter-from {
  opacity: 0;
}

.mobile-menu-enter-from .header_mobile_menu_rect {
  transform: translateX(100%);
}

.mobile-menu-leave-to {
  opacity: 0;
}

.mobile-menu-leave-to .header_mobile_menu_rect {
  transform: translateX(100%);
}
</style>