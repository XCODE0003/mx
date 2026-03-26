<script setup>
import MainLayout from '../Layouts/MainLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    news: { type: Object, required: true },
});
</script>

<template>
    <MainLayout>
        <main class="news">

            <section class="navigations_main">
                <div class="container">
                    <div class="navigations">
                        <Link :href="route('home')" class="navigation_item_link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="12" viewBox="0 0 19 12" fill="none">
                                <path d="M0.469669 5.46967C0.176777 5.76256 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.469669 5.46967ZM19 5.25L1 5.25V6.75L19 6.75V5.25Z" fill="#848CE4" />
                            </svg>
                            Назад
                        </Link>
                        <p class="navigation_item_text">Главная / Новости</p>
                    </div>
                </div>
            </section>

            <section class="news_main">
                <div class="container">
                    <h3 class="news_main_tittle">Новости</h3>

                    <div v-if="news.data.length" class="news_main_blocks">
                        <Link
                            v-for="item in news.data"
                            :key="item.id"
                            :href="route('news.show', { slug: item.slug })"
                            class="news_card"
                        >
                            <div class="news_card_img_wrap">
                                <img
                                    v-if="item.thumbnail"
                                    :src="item.thumbnail"
                                    :alt="item.title"
                                    class="news_card_img"
                                >
                                <div v-else class="news_card_img_placeholder">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none">
                                        <path d="M21 19V5C21 3.9 20.1 3 19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19ZM8.5 13.5L11 16.51L14.5 12L19 18H5L8.5 13.5Z" fill="rgba(143,112,255,0.3)"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="news_card_body">
                                <p class="news_card_date">{{ item.published_at }}</p>
                                <p class="news_card_title">{{ item.title }}</p>
                                <p v-if="item.excerpt" class="news_card_excerpt">{{ item.excerpt }}</p>
                                <span class="news_card_btn">Читать →</span>
                            </div>
                        </Link>
                    </div>

                    <div v-else class="news_empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none">
                            <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19ZM13.96 12.29L11.21 15.83L9.25 13.47L6.5 17H17.5L13.96 12.29Z" fill="rgba(143,112,255,0.4)"/>
                        </svg>
                        <p class="news_empty_text">Новостей пока нет</p>
                    </div>
                </div>
            </section>

            <section v-if="news.last_page > 1" class="paginations">
                <div class="container">
                    <div class="paginations_items">
                        <p class="paginations_text">Показано с {{ news.from }} по {{ news.to }} из {{ news.total }}</p>
                        <div class="paginations_pages">
                            <Link
                                v-if="news.prev_page_url"
                                :href="news.prev_page_url"
                                class="paginations_arrow"
                                preserve-scroll
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="9" height="14" viewBox="0 0 9 14" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.315166 7.67216C-0.105055 7.30094 -0.105055 6.69906 0.315166 6.32784L7.16308 0.278417C7.5833 -0.0928057 8.26461 -0.0928057 8.68483 0.278417C9.10505 0.64964 9.10505 1.25151 8.68483 1.62273L2.5978 7L8.68483 12.3773C9.10505 12.7485 9.10505 13.3504 8.68483 13.7216C8.26461 14.0928 7.5833 14.0928 7.16308 13.7216L0.315166 7.67216Z" fill="#393C5B" />
                                </svg>
                            </Link>

                            <template v-for="link in news.links" :key="link.label">
                                <Link
                                    v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                    :href="link.url"
                                    :class="['paginations_page', { paginations_page_active: link.active }]"
                                    v-html="link.label"
                                    preserve-scroll
                                />
                                <span
                                    v-else-if="!link.label.includes('Previous') && !link.label.includes('Next')"
                                    class="paginations_page"
                                    v-html="link.label"
                                />
                            </template>

                            <Link
                                v-if="news.next_page_url"
                                :href="news.next_page_url"
                                class="paginations_arrow"
                                preserve-scroll
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="9" height="14" viewBox="0 0 9 14" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.68483 7.67216C9.10506 7.30094 9.10506 6.69906 8.68483 6.32784L1.83692 0.278417C1.4167 -0.0928057 0.735389 -0.0928057 0.315166 0.278417C-0.105055 0.64964 -0.105055 1.25151 0.315166 1.62273L6.4022 7L0.315166 12.3773C-0.105055 12.7485 -0.105055 13.3504 0.315166 13.7216C0.735389 14.0928 1.4167 14.0928 1.83692 13.7216L8.68483 7.67216Z" fill="#393C5B" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

        </main>
    </MainLayout>
</template>

<style scoped>
.news_main {
    margin-top: 30px;
    padding-bottom: 60px;
}

.news_main_blocks {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-top: 30px;
}

.news_card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
    color: inherit;
}

.news_card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(143, 112, 255, 0.15);
}

.news_card_img_wrap {
    width: 100%;
    height: 220px;
    overflow: hidden;
    flex-shrink: 0;
}

.news_card_img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.news_card:hover .news_card_img {
    transform: scale(1.04);
}

.news_card_img_placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f0ecff 0%, #e8e3ff 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.news_card_body {
    padding: 22px 26px 28px;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.news_card_date {
    color: rgba(57, 60, 91, 0.5);
    font-size: 14px;
    font-weight: 500;
    line-height: 1.4;
}

.news_card_title {
    margin-top: 10px;
    color: #393c5b;
    font-size: 20px;
    font-weight: 600;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.news_card_excerpt {
    margin-top: 10px;
    color: rgba(57, 60, 91, 0.65);
    font-size: 15px;
    font-weight: 400;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.news_card_btn {
    margin-top: auto;
    padding-top: 20px;
    color: #8f70ff;
    font-size: 15px;
    font-weight: 600;
    display: inline-block;
    transition: letter-spacing 0.2s ease;
}

.news_card:hover .news_card_btn {
    letter-spacing: 0.3px;
}

.news_empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 80px 20px;
    gap: 16px;
}

.news_empty_text {
    color: rgba(57, 60, 91, 0.45);
    font-size: 18px;
    font-weight: 500;
}

.paginations {
    padding-bottom: 60px;
}

@media (max-width: 1024px) {
    .news_main_blocks {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 640px) {
    .news_main_blocks {
        grid-template-columns: 1fr;
    }

    .news_card_img_wrap {
        height: 180px;
    }
}
</style>
