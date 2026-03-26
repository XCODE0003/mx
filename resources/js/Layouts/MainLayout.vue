<script setup>
import Header from '../Components/Header.vue';
import Footer from '../Components/Footer.vue';
import ToastContainer from '../Components/ToastContainer.vue';
import { defineProps } from 'vue';

const props = defineProps({
    class: {
        type: String,
        default: ''
    }
});
</script>

<template>
    <div :class="['layout-app', props.class].filter(Boolean)">
        <Header />
        <div class="layout-app__main">
            <slot />
        </div>
        <Footer />
        <ToastContainer />
    </div>
</template>

<style scoped></style>