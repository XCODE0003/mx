<script setup>
import { ref } from 'vue';

const props = defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, required: false, default: '' },
    items: { type: Array, required: true },
});

const openIndex = ref(0);

function toggle(index) {
    openIndex.value = openIndex.value === index ? -1 : index;
}
</script>

<template>
    <section class="home_faq">
        <div class="container">
            <h4 class="home_faq_tittle">{{ title }}</h4>
            <h5 v-if="subtitle" class="home_faq_subtittle">{{ subtitle }}</h5>

            <div class="home_faq_items">
                <div
                    v-for="(item, index) in items"
                    :key="index"
                    class="home_faq_item"
                    :class="{ 'home_faq_item_active': openIndex === index }"
                    @click="toggle(index)"
                >
                    <div class="home_faq_item_content">
                        <p class="home_faq_item_tittle">
                            {{ item.title }}
                            <svg class="home_faq_item_arrow" xmlns="http://www.w3.org/2000/svg" width="21" height="12" viewBox="0 0 21 12" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M20.5824 2.44923L11.5082 11.5798C10.9514 12.1401 10.0486 12.1401 9.49176 11.5798L0.417625 2.44923C-0.139208 1.88894 -0.139208 0.980517 0.417625 0.420221C0.974461 -0.140075 1.87727 -0.140075 2.4341 0.420221L10.5 8.53626L18.5659 0.420222C19.1227 -0.140074 20.0255 -0.140074 20.5824 0.420222C21.1392 0.980518 21.1392 1.88894 20.5824 2.44923Z" fill="#393C5B" />
                            </svg>
                        </p>
                        <transition name="fade">
                            <p v-if="openIndex === index" class="home_faq_item_tittle_text">{{ item.text }}</p>
                        </transition>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.fade-enter-active {
  transition: all 0.3s ease;
  max-height: 200px;
  opacity: 1;
  overflow: hidden;
}

.fade-leave-active {
  transition: all 0s;
  max-height: 0;
  opacity: 0;
  overflow: hidden;
}

.fade-enter-from,
.fade-leave-to {
  max-height: 0;
  opacity: 0;
}

.home_faq_item_arrow {
  transition: transform 0.3s ease;
}

.home_faq_item_active .home_faq_item_arrow {
  transform: rotate(180deg);
}
</style>


