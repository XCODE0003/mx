<script setup>
import MainLayout from '../Layouts/MainLayout.vue';
import { ref } from 'vue';
import AnimatedSelect from '../Components/UI/AnimatedSelect.vue';
import Faq from '../Components/Faq.vue';
import { router, usePage } from '@inertiajs/vue3';
import axiosClient from '../api/axios';

const grade = ref('');
const subject = ref('');
const step = ref('start');
const downloadMeta = ref({ taskId: null, file: null, url: null });
const grades = [
    { key: 'oge', value: 'ОГЭ' },
    { key: 'ege', value: 'ЕГЭ' },
];
const subjects = ref([]);

const faqItems = [
    {
        title: 'Пример заголовка в первом отзыве',
        text: 'Сервис просто супер! Пользуюсь не первый раз и очень довольна. Советую вем кто хочет подготовить своих учеников к ОГЭ/ЕГЭ. Сервис просто супер! Пользуюсь не первый раз и очень довольна. Советую вем кто хочет подготовить своих учеников к ОГЭ/ЕГЭ.'
    },
    {
        title: 'Пример заголовка в первом отзыве',
        text: 'Сервис просто супер! Пользуюсь не первый раз и очень довольна. Советую вем кто хочет подготовить своих учеников к ОГЭ/ЕГЭ. Сервис просто супер! Пользуюсь не первый раз и очень довольна. Советую вем кто хочет подготовить своих учеников к ОГЭ/ЕГЭ.'
    },
    {
        title: 'Пример заголовка в первом отзыве',
        text: 'Сервис просто супер! Пользуюсь не первый раз и очень довольна. Советую вем кто хочет подготовить своих учеников к ОГЭ/ЕГЭ. Сервис просто супер! Пользуюсь не первый раз и очень довольна. Советую вем кто хочет подготовить своих учеников к ОГЭ/ЕГЭ.'
    },
    {
        title: 'Пример заголовка в первом отзыве',
        text: 'Сервис просто супер! Пользуюсь не первый раз и очень довольна. Советую вем кто хочет подготовить своих учеников к ОГЭ/ЕГЭ. Сервис просто супер! Пользуюсь не первый раз и очень довольна. Советую вем кто хочет подготовить своих учеников к ОГЭ/ЕГЭ.'
    },
    {
        title: 'Пример заголовка в первом отзыве',
        text: 'Сервис просто супер! Пользуюсь не первый раз и очень довольна. Советую вем кто хочет подготовить своих учеников к ОГЭ/ЕГЭ. Сервис просто супер! Пользуюсь не первый раз и очень довольна. Советую вем кто хочет подготовить своих учеников к ОГЭ/ЕГЭ.'
    },
];

function handleCreateClick() {
    const page = usePage();
    const isAuth = !!(page && page.props && page.props.auth && page.props.auth.user);
    if (!isAuth) {
        router.visit('/login');
        return;
    }
    if (!subject.value) {
        return;
    }
    step.value = 'forming';
    axiosClient.post('/auto/tasks/download', { subject_id: subject.value })
        .then((response) => {
            const data = response.data && response.data.data ? response.data.data : {};
            downloadMeta.value = {
                taskId: data.task_id,
                file: data.file,
                url: data.download_url,
            };
            const poll = () => {
                axiosClient.get(`/auto/tasks/status/${downloadMeta.value.taskId}/${encodeURIComponent(downloadMeta.value.file)}`)
                    .then((res) => {
                        const ready = !!(res && res.data && res.data.data && res.data.data.ready);
                        if (ready) {
                            step.value = 'ready';
                            return;
                        }
                        setTimeout(poll, 3000);
                    })
                    .catch(() => setTimeout(poll, 3000));
            };
            poll();
        })
        .catch(() => {
            step.value = 'start';
        });
}

function fetchSubjects() {
    const examType = grade.value || 'oge';
    axiosClient.get(`/profile/subjects/${examType}`)
        .then((res) => {
            const list = Array.isArray(res.data) ? res.data : [];
            const opts = list.map((s) => ({ key: s.id, value: s.name }));
            subjects.value = opts;
            subject.value = (opts[0] && opts[0].key) || '';
        })
        .catch(() => {
            subjects.value = [];
            subject.value = '';
        });
}

function init() {
    grade.value = 'oge';
    fetchSubjects();
}

init();
</script>

<template>
    <MainLayout>

        <main>

            <section class="intro">
                <div class="container">
                    <div class="intro_blocks">
                        <div class="intro_block">
                            <h1 class="intro_block_tittle">KIM365 - Сервис для <br>
                                создания тестовых <br>
                                вариантов ОГЭ / ЕГЭ</h1>
                            <h2 class="intro_block_subtittle">На сайте можно сформировать варианты для проведения диагностики в формате ОГЭ и ЕГЭ. Выберите готовый вариант или создайте свой на основе открытого банка заданий ФИПИ.</h2>
                            <button class="intro_block_button">Сформировать вариант</button>
                        </div>
                        <div class="intro_block">
                            <img src="/assets/img/intro_image.png" alt="" class="intro_block_image">
                        </div>
                    </div>
                </div>
            </section>

            <section class="home_create">
                <div class="container">
                    <div class="home_create_blocks">
                        <div class="home_create_block">
                            <div class="home_create_block_content">
                                <h4 class="home_create_tittle">Создание варианта</h4>


                                <div class="home_create_create">
                                    <div class="home_create_settings">
                                        <div class="home_create_setting">
                                            <p class="home_create_setting_tittle">Выберите паралель</p>
                                            <AnimatedSelect v-model="grade" :options="grades" placeholder="Выберите паралель" @update:modelValue="fetchSubjects" />
                                        </div>
                                        <div class="home_create_setting">
                                            <p class="home_create_setting_tittle">Выберите предмет</p>
                                            <AnimatedSelect v-model="subject" :options="subjects" placeholder="Выберите предмет" />
                                        </div>
                                    </div>

                                    <button class="home_create_block_button" id="createVariant" @click="handleCreateClick">Создать бесплатный вариант</button>
                                </div>

                                <div v-if="step === 'forming'" class="home_create_forming">
                                    <div class="home_create_forming_rect">
                                        <svg class="home_create_forming_icon" xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 96 96" fill="none">
                                            <g clip-path="url(#paint0_angular_172_618_clip_path)" data-figma-skip-parse="true">
                                                <g transform="matrix(0.048 0 0 0.048 48 48)">
                                                    <foreignObject x="-1000" y="-1000" width="2000" height="2000">
                                                        <div xmlns="http://www.w3.org/1999/xhtml" style="background:conic-gradient(from 90deg,rgba(110, 0, 175, 0.0001) 0deg,rgba(148, 86, 255, 1) 359.964deg,rgba(110, 0, 175, 0.0001) 360deg);height:100%;width:100%;opacity:1"></div>
                                                    </foreignObject>
                                                </g>
                                            </g>
                                            <path d="M96 48C96 74.5097 74.5097 96 48 96C21.4903 96 0 74.5097 0 48C0 21.4903 21.4903 0 48 0C74.5097 0 96 21.4903 96 48ZM16.8 48C16.8 65.2313 30.7687 79.2 48 79.2C65.2313 79.2 79.2 65.2313 79.2 48C79.2 30.7687 65.2313 16.8 48 16.8C30.7687 16.8 16.8 30.7687 16.8 48Z" data-figma-gradient-fill="{&quot;type&quot;:&quot;GRADIENT_ANGULAR&quot;,&quot;stops&quot;:[{&quot;color&quot;:{&quot;r&quot;:0.58041667938232422,&quot;g&quot;:0.33749997615814209,&quot;b&quot;:1.0,&quot;a&quot;:1.0},&quot;position&quot;:0.99989998340606689},{&quot;color&quot;:{&quot;r&quot;:0.43464052677154541,&quot;g&quot;:0.0,&quot;b&quot;:0.68627452850341797,&quot;a&quot;:9.9999997473787516e-05},&quot;position&quot;:1.0}],&quot;stopsVar&quot;:[{&quot;color&quot;:{&quot;r&quot;:0.58041667938232422,&quot;g&quot;:0.33749997615814209,&quot;b&quot;:1.0,&quot;a&quot;:1.0},&quot;position&quot;:0.99989998340606689},{&quot;color&quot;:{&quot;r&quot;:0.43464052677154541,&quot;g&quot;:0.0,&quot;b&quot;:0.68627452850341797,&quot;a&quot;:9.9999997473787516e-05},&quot;position&quot;:1.0}],&quot;transform&quot;:{&quot;m00&quot;:96.0,&quot;m01&quot;:0.0,&quot;m02&quot;:0.0,&quot;m10&quot;:0.0,&quot;m11&quot;:96.0,&quot;m12&quot;:0.0},&quot;opacity&quot;:1.0,&quot;blendMode&quot;:&quot;NORMAL&quot;,&quot;visible&quot;:true}" />
                                            <g clip-path="url(#paint1_angular_172_618_clip_path)" data-figma-skip-parse="true">
                                                <g transform="matrix(-2.09815e-09 -0.048 0.048 -2.09815e-09 48 48)">
                                                    <foreignObject x="-1000" y="-1000" width="2000" height="2000">
                                                        <div xmlns="http://www.w3.org/1999/xhtml" style="background:conic-gradient(from 90deg,rgba(110, 0, 175, 0.0001) 0deg,rgba(148, 86, 255, 1) 359.964deg,rgba(110, 0, 175, 0.0001) 360deg);height:100%;width:100%;opacity:1"></div>
                                                    </foreignObject>
                                                </g>
                                            </g>
                                            <path d="M48 -2.09815e-06C54.3034 -2.37368e-06 60.5452 1.24155 66.3688 3.65378C72.1924 6.06601 77.4839 9.60166 81.9411 14.0589C86.3983 18.5161 89.934 23.8076 92.3462 29.6312C94.7584 35.4548 96 41.6965 96 48L79.2 48C79.2 43.9028 78.393 39.8456 76.825 36.0603C75.2571 32.2749 72.9589 28.8355 70.0617 25.9383C67.1645 23.0411 63.7251 20.7429 59.9397 19.175C56.1544 17.607 52.0972 16.8 48 16.8L48 -2.09815e-06Z" data-figma-gradient-fill="{&quot;type&quot;:&quot;GRADIENT_ANGULAR&quot;,&quot;stops&quot;:[{&quot;color&quot;:{&quot;r&quot;:0.58041667938232422,&quot;g&quot;:0.33749997615814209,&quot;b&quot;:1.0,&quot;a&quot;:1.0},&quot;position&quot;:0.99989998340606689},{&quot;color&quot;:{&quot;r&quot;:0.43464052677154541,&quot;g&quot;:0.0,&quot;b&quot;:0.68627452850341797,&quot;a&quot;:9.9999997473787516e-05},&quot;position&quot;:1.0}],&quot;stopsVar&quot;:[{&quot;color&quot;:{&quot;r&quot;:0.58041667938232422,&quot;g&quot;:0.33749997615814209,&quot;b&quot;:1.0,&quot;a&quot;:1.0},&quot;position&quot;:0.99989998340606689},{&quot;color&quot;:{&quot;r&quot;:0.43464052677154541,&quot;g&quot;:0.0,&quot;b&quot;:0.68627452850341797,&quot;a&quot;:9.9999997473787516e-05},&quot;position&quot;:1.0}],&quot;transform&quot;:{&quot;m00&quot;:-4.1962930481531657e-06,&quot;m01&quot;:96.0,&quot;m02&quot;:0.0,&quot;m10&quot;:-96.0,&quot;m11&quot;:-4.1962930481531657e-06,&quot;m12&quot;:96.0},&quot;opacity&quot;:1.0,&quot;blendMode&quot;:&quot;NORMAL&quot;,&quot;visible&quot;:true}" />
                                            <defs>
                                                <clipPath id="paint0_angular_172_618_clip_path">
                                                    <path d="M96 48C96 74.5097 74.5097 96 48 96C21.4903 96 0 74.5097 0 48C0 21.4903 21.4903 0 48 0C74.5097 0 96 21.4903 96 48ZM16.8 48C16.8 65.2313 30.7687 79.2 48 79.2C65.2313 79.2 79.2 65.2313 79.2 48C79.2 30.7687 65.2313 16.8 48 16.8C30.7687 16.8 16.8 30.7687 16.8 48Z" />
                                                </clipPath>
                                                <clipPath id="paint1_angular_172_618_clip_path">
                                                    <path d="M48 -2.09815e-06C54.3034 -2.37368e-06 60.5452 1.24155 66.3688 3.65378C72.1924 6.06601 77.4839 9.60166 81.9411 14.0589C86.3983 18.5161 89.934 23.8076 92.3462 29.6312C94.7584 35.4548 96 41.6965 96 48L79.2 48C79.2 43.9028 78.393 39.8456 76.825 36.0603C75.2571 32.2749 72.9589 28.8355 70.0617 25.9383C67.1645 23.0411 63.7251 20.7429 59.9397 19.175C56.1544 17.607 52.0972 16.8 48 16.8L48 -2.09815e-06Z" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <div class="home_create_forming_texts">
                                        <p class="home_create_block_bottom_text">Для создания вариантов в ручную, вам необходимо приобести платную подписку в которой будет доступен полный функционал</p>
                                        <button class="home_create_block_button" disabled>Идет формирование</button>
                                    </div>
                                </div>


                                <div v-if="step === 'ready'" class="home_create_saved">
                                    <div class="home_create_forming_rect">
                                        <svg class="home_create_saved" xmlns="http://www.w3.org/2000/svg" width="91" height="96" viewBox="0 0 91 96" fill="none">
                                            <path d="M34.3006 62.1951V56.1792H37.8708C39.8761 56.1792 41.0932 57.3151 41.0932 59.1813C41.0932 61.0939 39.9456 62.1951 37.9403 62.1951H34.3006Z" fill="#A8AABF" />
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M72.3689 39.2258H86.2105C88.8447 39.2258 91 41.3811 91 44.0153V69.1121C91 86.2105 79.0742 95.7895 64.3226 95.7895H26.6774C11.9258 95.7895 0 86.2105 0 69.1121V26.6774C0 9.57895 11.9258 0 26.6774 0H46.9847C49.6189 0 51.7742 2.15526 51.7742 4.78947V18.6311C51.7742 29.9821 61.0179 39.2258 72.3689 39.2258ZM19 70.7263H21.5965V63.7252H28.7831V61.5344H21.5965V56.2371H29.4438V54H19V70.7263ZM34.3006 70.7263V64.2931H37.8708L41.2786 70.7263H44.2692L40.5252 63.8874C42.5653 63.2035 43.7708 61.3721 43.7708 59.1118C43.7708 55.9937 41.6264 54 38.1837 54H31.7041V70.7263H34.3006ZM56.9038 68.4776V70.7263H46.2861V54H56.9038V56.2371H48.8826V61.1287H56.4749V63.2847H48.8826V68.4776H56.9038ZM70.1875 70.7263V68.4776H62.1663V63.2847H69.7586V61.1287H62.1663V56.2371H70.1875V54H59.5698V70.7263H70.1875Z" fill="#A8AABF" />
                                        </svg>
                                    </div>
                                    <div class="home_create_forming_texts">
                                        <p class="home_create_block_bottom_text">Для создания вариантов в ручную, вам необходимо приобести платную подписку в которой будет доступен полный функционал</p>
                                        <a :href="downloadMeta.url" class="home_create_block_button" id="createDownload">Скачать бесплатный вариант</a>
                                    </div>
                                </div>








                                <div class="home_create_block_bottom">

                                    <button class="home_create_block_button" id="openPlans">Преобрести подписку</button>

                                    <div class="home_create_block_bottom_texts">
                                        <p class="home_create_block_bottom_text">Приобретая платную подписку вы получите 100% от общего банка заданий и возможность создания варианта вручную</p>
                                        <p class="home_create_block_bottom_text">Ели у вас остались вопросы, советуем вам ознакомиться с видеоинструкцией по работе с сайтом </p>
                                    </div>

                                </div>

                            </div>
                        </div>
                        <div class="home_create_block">
                            <div class="home_create_block_player">
                                <svg class="home_create_block_player_icon" xmlns="http://www.w3.org/2000/svg" width="51" height="64" viewBox="0 0 51 64" fill="none">
                                    <path d="M48.149 26.9529C51.8229 29.3148 51.8229 34.6853 48.149 37.0471L9.24455 62.0571C5.25148 64.624 2.45083e-07 61.757 4.31437e-07 57.01L2.39508e-06 6.98999C2.58144e-06 2.243 5.25148 -0.624051 9.24455 1.94292L48.149 26.9529Z" fill="#D3D7FF" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="home_plans home_plans_mobile">
                <div class="container">
                    <div class="home_plans_rect">
                        <div class="home_plans_rect_content">
                            <h4 class="home_plans_rect_tittle">Выберите план подписки</h4>
                            <div class="home_plans_blocks">
                                <div class="home_plans_block">
                                    <div class="home_plans_block_up">
                                        <div class="home_plans_block_up_content">
                                            <p class="home_plans_block_up_tittle">Basic <span>Бесплатно</span></p>
                                        </div>
                                    </div>
                                    <div class="home_plans_block_content">
                                        <ul class="home_plans_block_items">
                                            <li class="home_plans_block_item">- Полный банк <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                                                    <circle cx="7.5" cy="7.5" r="7.5" fill="#FF4646" />
                                                    <rect x="10.293" y="3.29297" width="2" height="9.89957" rx="0.5" transform="rotate(45 10.293 3.29297)" fill="white" />
                                                    <rect x="3.29297" y="4.70703" width="2" height="9.89957" rx="0.5" transform="rotate(-45 3.29297 4.70703)" fill="white" />
                                                </svg></li>
                                            <li class="home_plans_block_item">- Автоматическое формирование <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewBox="0 0 15 16" fill="none">
                                                    <circle cx="7.5" cy="8" r="7.5" fill="#3BF763" />
                                                    <rect x="11.5054" y="3.97803" width="2" height="9.37598" rx="0.5" transform="rotate(45 11.5054 3.97803)" fill="white" />
                                                    <rect x="2.08057" y="7.81689" width="2" height="5.94688" rx="0.5" transform="rotate(-45 2.08057 7.81689)" fill="white" />
                                                </svg></li>
                                            <li class="home_plans_block_item">- Ручное формирование варианта <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                                                    <circle cx="7.5" cy="7.5" r="7.5" fill="#FF4646" />
                                                    <rect x="10.293" y="3.29297" width="2" height="9.89957" rx="0.5" transform="rotate(45 10.293 3.29297)" fill="white" />
                                                    <rect x="3.29297" y="4.70703" width="2" height="9.89957" rx="0.5" transform="rotate(-45 3.29297 4.70703)" fill="white" />
                                                </svg></li>
                                            <li class="home_plans_block_item">- Неограниченный функционал <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                                                    <circle cx="7.5" cy="7.5" r="7.5" fill="#FF4646" />
                                                    <rect x="10.293" y="3.29297" width="2" height="9.89957" rx="0.5" transform="rotate(45 10.293 3.29297)" fill="white" />
                                                    <rect x="3.29297" y="4.70703" width="2" height="9.89957" rx="0.5" transform="rotate(-45 3.29297 4.70703)" fill="white" />
                                                </svg></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="home_plans_block">
                                    <div class="home_plans_block_up">
                                        <div class="home_plans_block_up_content">
                                            <p class="home_plans_block_up_tittle">Pro <span>2000₽/мес.</span></p>
                                            <button class="home_plans_block_up_button">Купить</button>
                                        </div>
                                    </div>
                                    <div class="home_plans_block_content">
                                        <ul class="home_plans_block_items">
                                            <li class="home_plans_block_item">- Полный банк <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewBox="0 0 15 16" fill="none">
                                                    <circle cx="7.5" cy="8" r="7.5" fill="#3BF763" />
                                                    <rect x="11.5054" y="3.97803" width="2" height="9.37598" rx="0.5" transform="rotate(45 11.5054 3.97803)" fill="white" />
                                                    <rect x="2.08057" y="7.81689" width="2" height="5.94688" rx="0.5" transform="rotate(-45 2.08057 7.81689)" fill="white" />
                                                </svg></li>
                                            <li class="home_plans_block_item">- Автоматическое формирование <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewBox="0 0 15 16" fill="none">
                                                    <circle cx="7.5" cy="8" r="7.5" fill="#3BF763" />
                                                    <rect x="11.5054" y="3.97803" width="2" height="9.37598" rx="0.5" transform="rotate(45 11.5054 3.97803)" fill="white" />
                                                    <rect x="2.08057" y="7.81689" width="2" height="5.94688" rx="0.5" transform="rotate(-45 2.08057 7.81689)" fill="white" />
                                                </svg></li>
                                            <li class="home_plans_block_item">- Ручное формирование варианта <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewBox="0 0 15 16" fill="none">
                                                    <circle cx="7.5" cy="8" r="7.5" fill="#3BF763" />
                                                    <rect x="11.5054" y="3.97803" width="2" height="9.37598" rx="0.5" transform="rotate(45 11.5054 3.97803)" fill="white" />
                                                    <rect x="2.08057" y="7.81689" width="2" height="5.94688" rx="0.5" transform="rotate(-45 2.08057 7.81689)" fill="white" />
                                                </svg></li>
                                            <li class="home_plans_block_item">- Неограниченный функционал <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewBox="0 0 15 16" fill="none">
                                                    <circle cx="7.5" cy="8" r="7.5" fill="#3BF763" />
                                                    <rect x="11.5054" y="3.97803" width="2" height="9.37598" rx="0.5" transform="rotate(45 11.5054 3.97803)" fill="white" />
                                                    <rect x="2.08057" y="7.81689" width="2" height="5.94688" rx="0.5" transform="rotate(-45 2.08057 7.81689)" fill="white" />
                                                </svg></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

            <Faq
                title="Остались вопросы?"
                subtitle="Если в списке часто задаваемых вопросов вы не нашли ответ на свой — напишите нам на почту или в онлайн чат!"
                :items="faqItems"
            />
        </main>
    </MainLayout>
</template>


<style scoped></style>