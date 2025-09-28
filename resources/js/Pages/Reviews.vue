<script setup>
import MainLayout from '../Layouts/MainLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    reviews: { type: Object, required: true },
});

const form = useForm({
    name: '',
    rating: 0,
    comment: '',
});

const hoverRating = ref(0);

const setRating = (index) => {
    form.rating = index;
};

const setHover = (index) => {
    hoverRating.value = index;
};

const clearHover = () => {
    hoverRating.value = 0;
};

const isStarActive = (index) => {
    return hoverRating.value ? index <= hoverRating.value : index <= form.rating;
};

const submit = () => {
    form.post(route('reviews.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('name', 'rating', 'comment');
            hoverRating.value = 0;
        },
    });
};

const formatDate = (dateString) => {
    try {
        return new Date(dateString).toLocaleDateString('ru-RU');
    } catch (e) {
        return dateString;
    }
};
</script>

<template>

    <MainLayout>
        <main class="reviews">

            <section class="navigations_main">
                <div class="container">
                    <div class="navigations">
                        <a href="/" class="navigation_item_link"><svg xmlns="http://www.w3.org/2000/svg" width="19" height="12" viewBox="0 0 19 12" fill="none">
                                <path d="M0.469669 5.46967C0.176777 5.76256 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.469669 5.46967ZM19 5.25L1 5.25V6.75L19 6.75V5.25Z" fill="#848CE4" />
                            </svg> Назад</a>
                        <p class="navigation_item_text">Главная / Отзывы </p>
                    </div>
                </div>
            </section>


            <section class="reviews_main">
                <div class="container">
                    <h4 class="home_faq_tittle">Отзывы </h4>


                    <div class="reviews_main">
                        <div class="reviews_main_blocks">

                            <div class="reviews_main_block">
                                <div class="reviews_main_block_content">
                                    <h4 class="reviews_main_block_tittle">Оставьте свой отзыв</h4>
                                    <div class="reviews_main_block_items">
                                        <div class="reviews_main_block_item">
                                            <p class="reviews_main_block_item_tittle">Имя</p>
                                            <input v-model="form.name" type="text" placeholder="Введите имя" class="reviews_main_block_item_input">
                                            <div v-if="form.errors.name" style="color:#ff4d4f; margin-top:6px;">{{ form.errors.name }}</div>
                                        </div>
                                        <div class="reviews_main_block_item">
                                            <p class="reviews_main_block_item_tittle">Оцените наш севис</p>
                                            <div class="reviews_main_block_item_stars">
                                                <svg
                                                    v-for="idx in 5"
                                                    :key="idx"
                                                    class="reviews_main_block_item_star"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="55" height="52" viewBox="0 0 55 52" fill="none"
                                                    @click="setRating(idx)"
                                                    @mouseenter="setHover(idx)"
                                                    @mouseleave="clearHover"
                                                >
                                                    <path
                                                        d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z"
                                                        :fill="isStarActive(idx) ? '#8F70FF' : '#8F70FF'"
                                                        :fill-opacity="isStarActive(idx) ? 1 : 0.4"
                                                    />
                                                </svg>
                                            </div>
                                            <div v-if="form.errors.rating" style="color:#ff4d4f; margin-top:6px;">{{ form.errors.rating }}</div>
                                        </div>
                                        <div class="reviews_main_block_item">
                                            <p class="reviews_main_block_item_tittle">Коментарий</p>
                                            <textarea v-model="form.comment" class="reviews_main_block_item_textarea" placeholder="Введите текст..."></textarea>
                                            <div v-if="form.errors.comment" style="#ff4d4f; margin-top:6px; color:#ff4d4f;">{{ form.errors.comment }}</div>
                                        </div>
                                    </div>
                                    <button class="reviews_main_block_button" @click="submit" :disabled="form.processing">Оставить коментарий</button>
                                </div>
                            </div>



                            <div class="reviews_main_block">
                                <div class="reviews_main_block_reviews" v-if="false">

                                    <div class="reviews_main_block_review">
                                        <div class="reviews_main_block_review_content">
                                            <img src="/assets/img/reviews_avatar.png" alt="" class="reviews_main_block_review_avatar">
                                            <div class="reviews_main_block_review_info">
                                                <div class="reviews_main_block_review_info_up">
                                                    <p class="reviews_main_block_review_info_up_name">Татьяна</p>
                                                    <div class="reviews_main_block_review_info_up_stars">
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                    </div>
                                                    <p class="reviews_main_block_review_info_up_date">04.01.2025</p>
                                                </div>
                                                <p class="reviews_main_block_review_info_text">Сервис просто супер! Пользуюсь не первый раз и очень довольна. Советую вем кто хочет подготовить своих учеников к ОГЭ/ЕГЭ.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="reviews_main_block_review">
                                        <div class="reviews_main_block_review_content">
                                            <img src="/assets/img/reviews_avatar.png" alt="" class="reviews_main_block_review_avatar">
                                            <div class="reviews_main_block_review_info">
                                                <div class="reviews_main_block_review_info_up">
                                                    <p class="reviews_main_block_review_info_up_name">Татьяна</p>
                                                    <div class="reviews_main_block_review_info_up_stars">
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                    </div>
                                                    <p class="reviews_main_block_review_info_up_date">04.01.2025</p>
                                                </div>
                                                <p class="reviews_main_block_review_info_text">Сервис просто супер! Пользуюсь не первый раз и очень довольна. Советую вем кто хочет подготовить своих учеников к ОГЭ/ЕГЭ. Сервис просто супер! Пользуюсь не первый раз и очень довольна. Советую вем кто хочет подготовить своих учеников к ОГЭ/ЕГЭ.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="reviews_main_block_review">
                                        <div class="reviews_main_block_review_content">
                                            <img src="/assets/img/reviews_avatar.png" alt="" class="reviews_main_block_review_avatar">
                                            <div class="reviews_main_block_review_info">
                                                <div class="reviews_main_block_review_info_up">
                                                    <p class="reviews_main_block_review_info_up_name">Татьяна</p>
                                                    <div class="reviews_main_block_review_info_up_stars">
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                    </div>
                                                    <p class="reviews_main_block_review_info_up_date">04.01.2025</p>
                                                </div>
                                                <p class="reviews_main_block_review_info_text">Сервис просто супер! Пользуюсь не первый раз и очень довольна. Советую вем кто хочет </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="reviews_main_block_review">
                                        <div class="reviews_main_block_review_content">
                                            <img src="/assets/img/reviews_avatar.png" alt="" class="reviews_main_block_review_avatar">
                                            <div class="reviews_main_block_review_info">
                                                <div class="reviews_main_block_review_info_up">
                                                    <p class="reviews_main_block_review_info_up_name">Татьяна</p>
                                                    <div class="reviews_main_block_review_info_up_stars">
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                        <svg class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" fill="#8F70FF" fill-opacity="0.4" />
                                                        </svg>
                                                    </div>
                                                    <p class="reviews_main_block_review_info_up_date">04.01.2025</p>
                                                </div>
                                                <p class="reviews_main_block_review_info_text">Сервис просто супер! Пользуюсь не первый раз и очень довольна. Советую вем кто хочет подготовить своих учеников к ОГЭ/ЕГЭ. Сервис просто супер! Пользуюсь не первый раз и очень довольна. Советую вем кто хочет подготовить своих учеников к ОГЭ/ЕГЭ.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="reviews_main_block_reviews">
                                    <div v-if="props.reviews?.data?.length === 0" style="padding: 16px; color: #888;">
                                        Отзывов пока нет. Будьте первым!
                                    </div>
                                    <div v-for="review in props.reviews?.data || []" :key="review.id" class="reviews_main_block_review">
                                        <div class="reviews_main_block_review_content">
                                            <img src="/assets/img/reviews_avatar.png" alt="" class="reviews_main_block_review_avatar">
                                            <div class="reviews_main_block_review_info">
                                                <div class="reviews_main_block_review_info_up">
                                                    <p class="reviews_main_block_review_info_up_name">{{ review.name }}</p>
                                                    <div class="reviews_main_block_review_info_up_stars">
                                                        <svg v-for="idx in 5" :key="idx" class="reviews_main_block_item_star" xmlns="http://www.w3.org/2000/svg" width="55" height="52" viewBox="0 0 55 52" fill="none">
                                                            <path d="M25.3598 1.59591C26.0328 -0.478202 28.9672 -0.478202 29.6402 1.59591L34.9361 17.918H52.083C54.2619 17.918 55.1686 20.7055 53.4067 21.9874L39.5319 32.082L44.83 48.4104C45.5028 50.4839 43.1288 52.2067 41.3661 50.9242L27.5 40.8359L13.6339 50.9242C11.8712 52.2067 9.49725 50.4839 10.17 48.4104L15.4681 32.082L1.59328 21.9874C-0.168615 20.7055 0.738131 17.918 2.917 17.918H20.0639L25.3598 1.59591Z" :fill="idx <= review.rating ? '#8F70FF' : '#8F70FF'" :fill-opacity="idx <= review.rating ? 1 : 0.4" />
                                                        </svg>
                                                    </div>
                                                    <p class="reviews_main_block_review_info_up_date">{{ formatDate(review.created_at) }}</p>
                                                </div>
                                                <p class="reviews_main_block_review_info_text">{{ review.comment }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <section class="paginations" v-if="false">
                                    <div class="container">
                                        <div class="paginations_items">
                                            <p class="paginations_text">Показано с 9 по 18</p>
                                            <div class="paginations_pages">
                                                <button class="paginations_arrow"><svg xmlns="http://www.w3.org/2000/svg" width="9" height="14" viewBox="0 0 9 14" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.315166 7.67216C-0.105055 7.30094 -0.105055 6.69906 0.315166 6.32784L7.16308 0.278417C7.5833 -0.0928057 8.26461 -0.0928057 8.68483 0.278417C9.10505 0.64964 9.10505 1.25151 8.68483 1.62273L2.5978 7L8.68483 12.3773C9.10505 12.7485 9.10505 13.3504 8.68483 13.7216C8.26461 14.0928 7.5833 14.0928 7.16308 13.7216L0.315166 7.67216Z" fill="#393C5B" />
                                                    </svg></button>
                                                <button class="paginations_page">1</button>
                                                <button class="paginations_page paginations_page_active">2</button>
                                                <button class="paginations_page">3</button>
                                                <button class="paginations_page">4</button>
                                                <button class="paginations_page">5</button>
                                                <button class="paginations_page">...</button>
                                                <button class="paginations_page">10</button>
                                                <button class="paginations_arrow"><svg xmlns="http://www.w3.org/2000/svg" width="9" height="14" viewBox="0 0 9 14" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M8.68483 7.67216C9.10506 7.30094 9.10506 6.69906 8.68483 6.32784L1.83692 0.278417C1.4167 -0.0928057 0.735389 -0.0928057 0.315166 0.278417C-0.105055 0.64964 -0.105055 1.25151 0.315166 1.62273L6.4022 7L0.315166 12.3773C-0.105055 12.7485 -0.105055 13.3504 0.315166 13.7216C0.735389 14.0928 1.4167 14.0928 1.83692 13.7216L8.68483 7.67216Z" fill="#393C5B" />
                                                    </svg></button>
                                            </div>
                                        </div>
                                    </div>

                                </section>

                                <section class="paginations" v-if="props.reviews?.links?.length">
                                    <div class="container">
                                        <div class="paginations_items">
                                            <p class="paginations_text" v-if="props.reviews.from && props.reviews.to">Показано с {{ props.reviews.from }} по {{ props.reviews.to }}</p>
                                            <div class="paginations_pages">
                                                <Link
                                                    v-for="(link, idx) in props.reviews.links"
                                                    :key="idx"
                                                    :href="link.url || '#'"
                                                    class="paginations_page"
                                                    :class="{ 'paginations_page_active': link.active, 'paginations_arrow': link.label.includes('Previous') || link.label.includes('Next') }"
                                                    v-html="link.label"
                                                    preserve-scroll
                                                />
                                            </div>
                                        </div>
                                    </div>

                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </MainLayout>

</template>