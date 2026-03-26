<script setup>
import MainLayout from '../Layouts/MainLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    article: { type: Object, required: true },
});
</script>

<template>
    <MainLayout>
        <main class="news_detail">

            <section class="navigations_main">
                <div class="container">
                    <div class="navigations">
                        <Link :href="route('news')" class="navigation_item_link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="12" viewBox="0 0 19 12" fill="none">
                                <path d="M0.469669 5.46967C0.176777 5.76256 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.469669 5.46967ZM19 5.25L1 5.25V6.75L19 6.75V5.25Z" fill="#848CE4" />
                            </svg>
                            Назад
                        </Link>
                        <p class="navigation_item_text">Главная / Новости / {{ article.title }}</p>
                    </div>
                </div>
            </section>

            <article class="nd_article">
                <div class="container">

                    <div class="nd_meta">
                        <span class="nd_date">{{ article.published_at }}</span>
                    </div>

                    <h1 class="nd_title">{{ article.title }}</h1>

                    <p v-if="article.excerpt" class="nd_excerpt">{{ article.excerpt }}</p>

                    <div v-if="article.cover_image" class="nd_cover_wrap">
                        <img :src="article.cover_image" :alt="article.title" class="nd_cover">
                    </div>

                    <div class="nd_content" v-html="article.content" />

                </div>
            </article>

        </main>
    </MainLayout>
</template>

<style scoped>
.news_detail {
    padding-bottom: 80px;
}

.nd_article {
    margin-top: 36px;
}

.nd_meta {
    display: flex;
    align-items: center;
    gap: 12px;
}

.nd_date {
    color: rgba(57, 60, 91, 0.5);
    font-size: 15px;
    font-weight: 500;
    line-height: 1.4;
}

.nd_title {
    margin-top: 14px;
    color: #393c5b;
    font-size: 42px;
    font-weight: 700;
    line-height: 1.15;
}

.nd_excerpt {
    margin-top: 18px;
    color: rgba(57, 60, 91, 0.7);
    font-size: 20px;
    font-weight: 400;
    line-height: 1.6;
    max-width: 800px;
    border-left: 3px solid #8f70ff;
    padding-left: 20px;
}

.nd_cover_wrap {
    margin-top: 32px;
    width: 100%;
    border-radius: 20px;
    overflow: hidden;
    max-height: 520px;
}

.nd_cover {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.nd_content {
    margin-top: 40px;
    max-width: 860px;
}

/* Rich text typography */
.nd_content :deep(p) {
    color: #393c5b;
    font-size: 18px;
    font-weight: 400;
    line-height: 1.75;
    margin-bottom: 20px;
}

.nd_content :deep(h2) {
    color: #393c5b;
    font-size: 30px;
    font-weight: 700;
    line-height: 1.25;
    margin-top: 40px;
    margin-bottom: 16px;
}

.nd_content :deep(h3) {
    color: #393c5b;
    font-size: 24px;
    font-weight: 600;
    line-height: 1.3;
    margin-top: 32px;
    margin-bottom: 12px;
}

.nd_content :deep(ul),
.nd_content :deep(ol) {
    color: #393c5b;
    font-size: 18px;
    line-height: 1.75;
    padding-left: 24px;
    margin-bottom: 20px;
}

.nd_content :deep(li) {
    margin-bottom: 8px;
}

.nd_content :deep(blockquote) {
    border-left: 3px solid #8f70ff;
    padding-left: 20px;
    margin: 28px 0;
    color: rgba(57, 60, 91, 0.7);
    font-size: 18px;
    font-style: italic;
    line-height: 1.7;
}

.nd_content :deep(a) {
    color: #8f70ff;
    text-decoration: underline;
    display: inline;
}

.nd_content :deep(strong) {
    font-weight: 700;
}

.nd_content :deep(em) {
    font-style: italic;
}

.nd_content :deep(img) {
    width: 100%;
    border-radius: 16px;
    margin: 24px 0;
    object-fit: cover;
}

.nd_content :deep(pre) {
    background: #f0ecff;
    border-radius: 12px;
    padding: 20px 24px;
    overflow-x: auto;
    margin-bottom: 20px;
    font-size: 15px;
    line-height: 1.6;
}

.nd_content :deep(code) {
    background: #f0ecff;
    border-radius: 6px;
    padding: 2px 7px;
    font-size: 15px;
    color: #6b45d4;
}

@media (max-width: 768px) {
    .nd_title {
        font-size: 28px;
    }

    .nd_excerpt {
        font-size: 17px;
    }

    .nd_content :deep(p) {
        font-size: 16px;
    }

    .nd_content :deep(h2) {
        font-size: 24px;
    }

    .nd_content :deep(h3) {
        font-size: 20px;
    }
}
</style>
