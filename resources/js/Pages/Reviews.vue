<script setup>
import MainLayout from '../Layouts/MainLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

function getAvatarColor(name, createdAt) {
    const colors = [
        '#8F70FF',
        '#FF7A7A',
        '#4DD599',
        '#FFA94D',
        '#4DA6FF',
        '#FF8A5B',
        '#6DD3CE',
        '#A78BFA',
    ];

    const str = `${name || ''}${createdAt || ''}`;
    let hash = 0;

    for (let i = 0; i < str.length; i++) {
        hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }

    return colors[Math.abs(hash) % colors.length];
}

function getAvatarInitials(name) {
    if (!name) return '??';

    const parts = name.trim().split(/\s+/).filter(Boolean);

    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }

    return name.trim().slice(0, 2).toUpperCase();
}

const props = defineProps({
    reviews: { type: Object, required: true },
});

const form = useForm({
    name: '',
    comment: '',
});

const submit = () => {
    form.post(route('reviews.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('name', 'comment');
        },
    });
};

const formatDate = (dateString) => {
    const date = new Date(dateString);

    const formattedDate = date.toLocaleDateString('ru-RU');
    const formattedTime = date.toLocaleTimeString('ru-RU', {
        hour: '2-digit',
        minute: '2-digit'
    });

    return `${formattedDate} в ${formattedTime}`;
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
                                            <input v-model="form.name" type="text" placeholder="Введите ваше имя" class="reviews_main_block_item_input">
                                            <div v-if="form.errors.name" style="color:#ff4d4f; margin-top:6px;">{{ form.errors.name }}</div>
                                        </div>
                                        <div class="reviews_main_block_item">
                                            <p class="reviews_main_block_item_tittle">Комментарий</p>
                                            <textarea v-model="form.comment" class="reviews_main_block_item_textarea" placeholder="Введите текст..."></textarea>
                                            <div v-if="form.errors.comment" style="margin-top:6px; color:#ff4d4f;">{{ form.errors.comment }}</div>
                                        </div>
                                    </div>
                                    <button class="reviews_main_block_button" @click="submit" :disabled="form.processing">Оставить комментарий</button>
                                </div>
                            </div>



                            <div class="reviews_main_block">
                                <div class="reviews_main_block_reviews">
                                    <div v-if="props.reviews?.data?.length === 0" style="padding: 16px; color: #888;">
                                        Отзывов пока нет. Будьте первым!
                                    </div>
                                    <div v-for="review in props.reviews?.data || []" :key="review.id" class="reviews_main_block_review">
                                        <div class="reviews_main_block_review_content">
                                            <div
                                                class="reviews_main_block_review_avatar reviews_main_block_review_avatar--generated"
                                                :style="{ backgroundColor: getAvatarColor(review.name, review.created_at) }"
                                            >
                                                {{ getAvatarInitials(review.name) }}
                                            </div>
                                                <div class="reviews_main_block_review_info">
                                                <div class="reviews_main_block_review_info_up">
                                                    <p class="reviews_main_block_review_info_up_name">{{ review.name }}</p>
                                                </div>
                                                    <p class="reviews_main_block_review_info_up_date">{{ formatDate(review.created_at) }}</p>
                                                <p class="reviews_main_block_review_info_text">{{ review.comment }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
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