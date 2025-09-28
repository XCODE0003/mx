<script setup>
import MainLayout from '../../Layouts/MainLayout.vue';
import Aside from '../../Components/Profile/Aside.vue';
import { ref } from 'vue';
const props = defineProps({
    orders: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <MainLayout>
        <main class="account_history">

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
                            <Aside active="history"></Aside>
                        </div>

                        <div class="profile_block">
                            <div class="profile_block_rect">
                                <div class="profile_block_rect_content">
                                    <h3 class="profile_block_rect_tittle">История заказов</h3>

                                    <div class="profile_history">
                                        <div class="profile_history_tabble">
                                            <table class="profile_history_tabble_main">
                                                <thead>
                                                    <td class="profile_history_tabble_tittle">Дата формирования</td>
                                                    <!-- <td class="profile_history_tabble_tittle">Кол-во вариантов</td> -->
                                                    <td class="profile_history_tabble_tittle">Срок хранения</td>
                                                    <td class="profile_history_tabble_tittle">Название диагностики</td>
                                                    <td class="profile_history_tabble_tittle"></td>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="order in orders" class="profile_history_tabble_line">
                                                        <td class="profile_history_tabble_text">{{ order.created_at }}</td>
                                                        <!-- <td class="profile_history_tabble_text">4</td> -->
                                                        <td class="profile_history_tabble_text">30 дней</td>
                                                        <td class="profile_history_tabble_text">{{ (order.subject?.exam_type === 'oge' ? 'ОГЭ' : 'ЕГЭ') + ' ' + order.subject?.name }}</td>
                                                        <td class="">
                                                            <a :href="order.file_url">
                                                            <svg class="profile_history_tabble_download" data-file="/downloads/report-oge-russian.pdf" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                                                                <rect width="30" height="30" rx="4" fill="#8F70FF" />
                                                                <path d="M23.0526 13.19H20.3147C18.0695 13.19 16.2411 11.26 16.2411 8.89V6C16.2411 5.45 15.8147 5 15.2937 5H11.2768C8.35895 5 6 7 6 10.57V19.43C6 23 8.35895 25 11.2768 25H18.7232C21.6411 25 24 23 24 19.43V14.19C24 13.64 23.5737 13.19 23.0526 13.19ZM15.2653 18.78L13.3705 20.78C13.3042 20.85 13.2189 20.91 13.1337 20.94C13.0484 20.98 12.9632 21 12.8684 21C12.7737 21 12.6884 20.98 12.6032 20.94C12.5274 20.91 12.4516 20.85 12.3947 20.79C12.3853 20.78 12.3758 20.78 12.3758 20.77L10.4811 18.77C10.2063 18.48 10.2063 18 10.4811 17.71C10.7558 17.42 11.2105 17.42 11.4853 17.71L12.1579 18.44V14.25C12.1579 13.84 12.48 13.5 12.8684 13.5C13.2568 13.5 13.5789 13.84 13.5789 14.25V18.44L14.2611 17.72C14.5358 17.43 14.9905 17.43 15.2653 17.72C15.54 18.01 15.54 18.49 15.2653 18.78Z" fill="white" />
                                                                <path d="M20.4254 11.9897C21.2632 12 22.4274 12 23.424 12C23.9267 12 24.1913 11.3108 23.8385 10.8993C22.5685 9.40773 20.2931 6.72289 18.9878 5.20044C18.6262 4.77869 18 5.06672 18 5.65306V9.24315C18 10.745 19.0936 11.9897 20.4254 11.9897Z" fill="white" />
                                                            </svg>
                                                            </a>
                                                        </td>
                                                    </tr>


                                                </tbody>

                                            </table>
                                        </div>
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