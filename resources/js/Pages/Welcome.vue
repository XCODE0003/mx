<script setup>
import MainLayout from '../Layouts/MainLayout.vue';
import { ref, onUnmounted } from 'vue';
import AnimatedSelect from '../Components/UI/AnimatedSelect.vue';
import Faq from '../Components/Faq.vue';
import { router, usePage, Link } from '@inertiajs/vue3';
import axiosClient from '../api/axios';
import { useScrollAnimation } from '../composables/useScrollAnimation';

const VARIANT_UI_TTL_MS = 30 * 60 * 1000;

const grade = ref('');
const subject = ref('');
const step = ref('start');
const downloadMeta = ref({ variantUuid: null, url: null });
let uiExpiryTimer = null;

// Видео плеер
const videoPlayer = ref(null);
const isPlaying = ref(false);

// Инициализируем анимации при скролле
useScrollAnimation();

function clearUiExpiryTimer() {
    if (uiExpiryTimer) {
        clearTimeout(uiExpiryTimer);
        uiExpiryTimer = null;
    }
}

onUnmounted(() => clearUiExpiryTimer());
const grades = [
    { key: 'oge', value: 'ОГЭ' },
    { key: 'ege', value: 'ЕГЭ' },
];
const subjects = ref([]);

const faqItems = [
    {
        title: 'Почему сайт полностью бесплатный?',
        text: 'Сейчас вы пользуетесь пилотной версией проекта: в ней доступна лишь часть функций и только два предмета. В дальнейшем сайт будет активно развиваться — появятся все предметы ОГЭ и ЕГЭ, банк заданий, возможность вручную составлять варианты, добавлять собственные задания, а также материалы для ВПР, ВсОШ и многое другое.\n\nМы сделали текущую версию бесплатной, чтобы вы уже сейчас могли познакомиться с проектом, попробовать его в работе и пользоваться им, пока он совершенствуется. В будущем вы также получите скидку 50% на покупку.'
    },
    {
        title: 'Какие предметы сейчас доступны?',
        text: 'Сейчас доступны два предмета ОГЭ и ЕГЭ — русский язык и математика. Мы активно работаем над добавлением остальных предметов, чтобы сделать сервис ещё полезнее.'
    },
    {
        title: 'Можно ли сохранить созданные варианты?',
        text: 'Да, все созданные варианты сохраняются в вашем личном кабинете. Вы можете в любой момент скачать их.'
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
    clearUiExpiryTimer();
    step.value = 'forming';
    axiosClient.post('/auto/tasks/download', { subject_id: subject.value })
        .then((response) => {
            const data = response.data && response.data.data ? response.data.data : {};
            downloadMeta.value = {
                variantUuid: data.variant_uuid,
                url: data.download_url,
            };
            const poll = () => {
                if (!downloadMeta.value.variantUuid) {
                    step.value = 'start';
                    return;
                }
                axiosClient.get(`/variants/${downloadMeta.value.variantUuid}/status`)
                    .then((res) => {
                        const ready = !!(res && res.data && res.data.data && res.data.data.ready);
                        if (ready) {
                            step.value = 'ready';
                            clearUiExpiryTimer();
                            uiExpiryTimer = setTimeout(() => {
                                step.value = 'start';
                                downloadMeta.value = { variantUuid: null, url: null };
                            }, VARIANT_UI_TTL_MS);
                            return;
                        }
                        setTimeout(poll, 3000);
                    })
                    .catch((err) => {
                        if (err.response?.status === 410) {
                            step.value = 'start';
                            downloadMeta.value = { variantUuid: null, url: null };
                            return;
                        }
                        setTimeout(poll, 3000);
                    });
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

function toggleVideo() {
    if (!videoPlayer.value) return;

    if (isPlaying.value) {
        videoPlayer.value.pause();
        isPlaying.value = false;
    } else {
        videoPlayer.value.play();
        isPlaying.value = true;
    }
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
                            <h1 class="intro_block_tittle">КИМ 365 — это сервис для
                                составления тренировочных
                                вариантов <span class="intro_accent">ОГЭ</span> и <span class="intro_accent">ЕГЭ</span></h1>
                            <h2 class="intro_block_subtittle">Вы можете собрать уникальный вариант из нашего банка заданий за несколько секунд. Все материалы соответствуют актуальным демоверсиям ФИПИ текущего учебного года.</h2>
                            <Link href="/profile/constructor" class="intro_block_button">
                                <span>Сформировать вариант</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M4.16699 10H15.8337M15.8337 10L10.0003 4.16669M15.8337 10L10.0003 15.8334" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </Link>
                        </div>
                        <div class="intro_block">
                            <img src="/assets/img/intro_image.png" alt="" class="intro_block_image">
                        </div>
                    </div>
                </div>
            </section>

            <section class="home_create scroll-animate">
                <div class="container">
                    <div class="home_create_blocks">
                        <div class="home_create_block h-fit">
                            <div class="home_create_block_content">

                                <h4 class="home_create_tittle">
                                    Начните работу с КИМ 365
                                </h4>

                                <p class="home_create_subtitle">
                                    Посмотрите короткую видеоинструкцию и быстро соберите свой первый вариант
                                </p>

                                <div class="home_create_features">
                                    <p>Неограниченное количество вариантов</p>
                                    <p>Актуальные задания ОГЭ и ЕГЭ</p>
                                    <p>Всё доступно бесплатно</p>
                                </div>

                                <Link href="/profile/constructor" class="home_create_block_button">
                                    Сформировать вариант
                                </Link>
                            </div>
                        </div>
                        <div class="home_create_block home_create_video_card">
                            <div class="home_create_mobile_hint">
                                <p class="home_create_mobile_hint_title">
                                    Начните работу с КИМ 365
                                </p>
                                <p class="home_create_mobile_hint_text">
                                    Посмотрите короткую видеоинструкцию и быстро соберите свой первый вариант
                                </p>
                            </div>

                            <div class="home_create_block_player" @click="toggleVideo">
                                <video ref="videoPlayer" class="home_create_video" poster="/assets/img/intro_image.png" @play="isPlaying = true" @pause="isPlaying = false" @ended="isPlaying = false" controlsList="nodownload nofullscreen noremoteplayback" disablePictureInPicture>
                                    <source src="/assets/media.mp4" type="video/mp4">
                                    Ваш браузер не поддерживает воспроизведение видео.
                                </video>
                                <div v-if="!isPlaying" class="video_play_overlay">
                                    <svg class="video_play_icon" xmlns="http://www.w3.org/2000/svg" width="51" height="64" viewBox="0 0 51 64" fill="none">
                                        <path d="M48.149 26.9529C51.8229 29.3148 51.8229 34.6853 48.149 37.0471L9.24455 62.0571C5.25148 64.624 2.45083e-07 61.757 4.31437e-07 57.01L2.39508e-06 6.98999C2.58144e-06 2.243 5.25148 -0.624051 9.24455 1.94292L48.149 26.9529Z" fill="#D3D7FF" />
                                    </svg>
                                </div>
                            </div>
                            <Link href="/profile/constructor" class="home_create_mobile_button">
                                Сформировать вариант
                            </Link>
                        </div>
                    </div>
                </div>
            </section>



            <Faq title="Остались вопросы?" :items="faqItems">
                <template #subtitle>
                    Если среди часто задаваемых вопросов вы не нашли нужного ответа — напишите нам любым удобным способом через раздел <a href="/contacts">«Контакты».</a>
                </template>
            </Faq>
        </main>
    </MainLayout>
</template>


<style scoped>
.home_create_video_card {
    padding: 0 !important;
    overflow: hidden;
}

.home_create_block_player {
    max-height: 330px;
    position: relative;
    cursor: pointer;
}

.home_create_video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 24px;
    display: block;
}

.home_create_video::-webkit-media-controls {
    display: none !important;
}

.home_create_video::-webkit-media-controls-enclosure {
    display: none !important;
}

.video_play_overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.2);
    cursor: pointer;
    transition: all 0.3s;
    border-radius: 24px;
    pointer-events: auto;
}

.video_play_overlay:hover {
    background: rgba(0, 0, 0, 0.3);
}

.video_play_icon {
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.3));
    transition: transform 0.3s;
}

.video_play_overlay:hover .video_play_icon {
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .home_create_block_player {
        min-height: 300px;
    }

    .home_create_video {
        min-height: 300px;
    }
}
</style>