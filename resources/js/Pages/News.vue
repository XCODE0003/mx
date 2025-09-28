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
        <main class="news">

            <section class="navigations_main">
                <div class="container">
                    <div class="navigations">
                        <a href="index.html" class="navigation_item_link"><svg xmlns="http://www.w3.org/2000/svg" width="19" height="12" viewBox="0 0 19 12" fill="none">
                                <path d="M0.469669 5.46967C0.176777 5.76256 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.469669 5.46967ZM19 5.25L1 5.25V6.75L19 6.75V5.25Z" fill="#848CE4" />
                            </svg> Назад</a>
                        <p class="navigation_item_text">Главная / Новости</p>
                    </div>
                </div>
            </section>

            <section class="news_main">
                <div class="container">
                    <h3 class="news_main_tittle">Новости</h3>

                    <div class="news_main_blocks">

                        <div class="news_main_block">
                            <img src="/assets/img/news_img_1.png" alt="" class="news_main_block_image">
                            <div class="news_main_block_content">
                                <p class="news_main_block_date">04.01.2025</p>
                                <p class="news_main_block_tittle">Госдума рассмотрит эксперимент по изменению ОГЭ в регионах</p>
                                <a href="news_page1.html"><button class="news_main_block_button">Читать</button></a>
                            </div>
                        </div>


                        <div class="news_main_block">
                            <img src="/assets/img/news_img_2.png" alt="" class="news_main_block_image">
                            <div class="news_main_block_content">
                                <p class="news_main_block_date">04.01.2025</p>
                                <p class="news_main_block_tittle">Госдума рассмотрит эксперимент по изменению ОГЭ в регионах</p>
                                <a href="news_page2.html"><button class="news_main_block_button">Читать</button></a>
                            </div>
                        </div>

                        <div class="news_main_block">
                            <img src="/assets/img/news_img_3.png" alt="" class="news_main_block_image">
                            <div class="news_main_block_content">
                                <p class="news_main_block_date">04.01.2025</p>
                                <p class="news_main_block_tittle">Госдума рассмотрит эксперимент по изменению ОГЭ в регионах</p>
                                <a href="news_page1.html"><button class="news_main_block_button">Читать</button></a>
                            </div>
                        </div>

                        <div class="news_main_block">
                            <img src="/assets/img/news_img_4.png" alt="" class="news_main_block_image">
                            <div class="news_main_block_content">
                                <p class="news_main_block_date">04.01.2025</p>
                                <p class="news_main_block_tittle">Госдума рассмотрит эксперимент по изменению ОГЭ в регионах</p>
                                <a href="news_page2.html"><button class="news_main_block_button">Читать</button></a>
                            </div>
                        </div>

                        <div class="news_main_block">
                            <img src="/assets/img/news_img_5.png" alt="" class="news_main_block_image">
                            <div class="news_main_block_content">
                                <p class="news_main_block_date">04.01.2025</p>
                                <p class="news_main_block_tittle">Госдума рассмотрит эксперимент по изменению ОГЭ в регионах</p>
                                <a href="news_page1.html"><button class="news_main_block_button">Читать</button></a>
                            </div>
                        </div>

                        <div class="news_main_block">
                            <img src="/assets/img/news_img_6.png" alt="" class="news_main_block_image">
                            <div class="news_main_block_content">
                                <p class="news_main_block_date">04.01.2025</p>
                                <p class="news_main_block_tittle">Госдума рассмотрит эксперимент по изменению ОГЭ в регионах</p>
                                <a href="news_page2.html"><button class="news_main_block_button">Читать</button></a>
                            </div>
                        </div>

                        <div class="news_main_block">
                            <img src="/assets/img/news_img_7.png" alt="" class="news_main_block_image">
                            <div class="news_main_block_content">
                                <p class="news_main_block_date">04.01.2025</p>
                                <p class="news_main_block_tittle">Госдума рассмотрит эксперимент по изменению ОГЭ в регионах</p>
                                <a href="news_page1.html"><button class="news_main_block_button">Читать</button></a>
                            </div>
                        </div>

                        <div class="news_main_block">
                            <img src="/assets/img/news_img_8.png" alt="" class="news_main_block_image">
                            <div class="news_main_block_content">
                                <p class="news_main_block_date">04.01.2025</p>
                                <p class="news_main_block_tittle">Госдума рассмотрит эксперимент по изменению ОГЭ в регионах</p>
                                <a href="news_page2.html"><button class="news_main_block_button">Читать</button></a>
                            </div>
                        </div>

                        <div class="news_main_block">
                            <img src="/assets/img/news_img_9.png" alt="" class="news_main_block_image">
                            <div class="news_main_block_content">
                                <p class="news_main_block_date">04.01.2025</p>
                                <p class="news_main_block_tittle">Госдума рассмотрит эксперимент по изменению ОГЭ в регионах</p>
                                <a href="news_page1.html"><button class="news_main_block_button">Читать</button></a>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <section class="paginations">
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

        </main>
    </MainLayout>

</template>