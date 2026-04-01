<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, required: false, default: '' },
    items: { type: Array, required: true },
});

const openIndex = ref(0);
const isVisible = ref(false);

function toggle(index) {
    openIndex.value = openIndex.value === index ? -1 : index;
}

onMounted(() => {
    setTimeout(() => {
        isVisible.value = true;
    }, 100);
});
</script>

<template>
    <section class="home_faq" :class="{ 'home_faq--visible': isVisible }">
        <div class="container">
            <h4 class="home_faq_tittle fade-in-up">{{ title }}</h4>
            <h5 v-if="$slots.subtitle || subtitle" class="home_faq_subtittle fade-in-up delay-1">
                <slot name="subtitle">{{ subtitle }}</slot>
            </h5>

            <div class="home_faq_items">
                <div
                    v-for="(item, index) in items"
                    :key="index"
                    class="home_faq_item fade-in-up"
                    :class="{ 'home_faq_item_active': openIndex === index }"
                    :style="{ animationDelay: `${0.2 + index * 0.1}s` }"
                >
                    <div class="home_faq_item_header" @click="toggle(index)">
                        <p class="home_faq_item_tittle">
                            {{ item.title }}
                            <svg class="home_faq_item_arrow" xmlns="http://www.w3.org/2000/svg" width="21" height="12" viewBox="0 0 21 12" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M20.5824 2.44923L11.5082 11.5798C10.9514 12.1401 10.0486 12.1401 9.49176 11.5798L0.417625 2.44923C-0.139208 1.88894 -0.139208 0.980517 0.417625 0.420221C0.974461 -0.140075 1.87727 -0.140075 2.4341 0.420221L10.5 8.53626L18.5659 0.420222C19.1227 -0.140074 20.0255 -0.140074 20.5824 0.420222C21.1392 0.980518 21.1392 1.88894 20.5824 2.44923Z" fill="#393C5B" />
                            </svg>
                        </p>
                    </div>

                    <div class="home_faq_item_body">
                        <div class="home_faq_item_content">
                            <p class="home_faq_item_tittle_text">{{ item.text }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(24px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fade-in-up {
  opacity: 0;
  animation: fadeInUp 0.55s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

.delay-1 {
  animation-delay: 0.1s;
}

/* Grid trick: анимирует высоту без JS и без layout-скачков */
.home_faq_item_body {
  display: grid;
  grid-template-rows: 0fr;
  transition: grid-template-rows 0.38s cubic-bezier(0.4, 0, 0.2, 1),
              opacity 0.3s ease;
  opacity: 0;
}

.home_faq_item_active .home_faq_item_body {
  grid-template-rows: 1fr;
  opacity: 1;
}

.home_faq_item_content {
  overflow: hidden;
}

/* Стрелка */
.home_faq_item_arrow {
  transition: transform 0.38s cubic-bezier(0.4, 0, 0.2, 1);
}

.home_faq_item_active .home_faq_item_arrow {
  transform: rotate(180deg);
}
</style>
