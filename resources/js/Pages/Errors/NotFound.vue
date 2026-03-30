<script setup>
import MainLayout from '../../Layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Страница не найдена" />

    <MainLayout class="layout-app--notfound">
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
.layout-app__main {
    flex: 0 0 auto;
}

.error404-main {
    min-height: auto;
    flex: none;
}

.page_error--site {
    padding: 80px 0 72px;
    box-sizing: border-box;
}

.page_error--site .page_error_texts {
    max-width: 640px;
    margin: 0 auto;
}
</style>
