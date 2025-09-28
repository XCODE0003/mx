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
    <div :class="class">
        <Header />
        <slot />
        <Footer />
        <ToastContainer />
    </div>
</template>

<style scoped></style>