<script setup>
import MainLayout from '../../Layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Страница не найдена" />

    <MainLayout>
        <main class="error404-main">
            <section class="page_error page_error--site">
                <div class="container">
                    <div class="page_error_texts">
                        <h1 class="page_error_tittle">404</h1>
                        <p class="page_error_subtittle">Страница не найдена</p>
                        <p class="page_error_text">
                            Такой страницы нет, или ссылка устарела. Проверьте адрес или вернитесь на главную.
                        </p>
                        <Link href="/" class="page_error_button">На главную</Link>
                    </div>
                </div>
            </section>
        </main>
    </MainLayout>
</template>

<style scoped>
.error404-main {
    min-height: 0;
}

.page_error--site {
    background-image: none;
    background: linear-gradient(
        165deg,
        rgba(143, 112, 255, 0.12) 0%,
        rgba(132, 140, 228, 0.08) 38%,
        #f5f6fb 100%
    );
    background-size: cover;
    padding: 32px 0 72px;
    min-height: min(72vh, 720px);
    box-sizing: border-box;
}

.page_error--site .page_error_texts {
    max-width: 640px;
    margin: 0 auto;
}
</style>
