<script setup>
import { storeToRefs } from 'pinia';
import { useToastStore } from '../stores/toastStore';

const toastStore = useToastStore();
const { toasts } = storeToRefs(toastStore);

function close(id) {
    toastStore.remove(id);
}
</script>

<template>
    <div class="toast-container">
        <div v-for="t in toasts" :key="t.id" class="toast" :class="`toast--${t.type}`">
            <span class="toast__message">{{ t.message }}</span>
            <button class="toast__close" @click="close(t.id)">×</button>
        </div>
    </div>

</template>

<style scoped>
.toast-container {
    position: fixed;
    right: 20px;
    bottom: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.toast {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 12px;
    box-shadow: 0 5px 10px rgba(0,0,0,0.15);
    font-family: "SF Pro Display", sans-serif;
    font-size: 14px;
    color: #393c5b;
    background: #fff;
    border: 1px solid #dfe3ff;
}
.toast__message {
    flex: 1 1 auto;
}
.toast__close {
    border: none;
    background: transparent;
    color: #888a9f;
    cursor: pointer;
    font-size: 18px;
}
.toast--success {
    border-color: #54F645;
}
.toast--error {
    border-color: #ff6b6b;
}
.toast--info {
    border-color: #8f70ff;
}
</style>


