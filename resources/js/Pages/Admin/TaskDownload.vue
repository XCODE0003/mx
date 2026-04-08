<template>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <div v-if="!isReady" class="space-y-6">
                <div class="flex justify-center">
                    <svg class="animate-spin h-16 w-16 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                
                <div class="space-y-2">
                    <h2 class="text-2xl font-bold text-gray-900">
                        Генерация ZIP файла
                    </h2>
                    <p class="text-gray-600">
                        Пожалуйста, подождите. Файл генерируется...
                    </p>
                    <p class="text-sm text-gray-500">
                        Скачивание начнется автоматически, когда файл будет готов
                    </p>
                </div>
                
                <div class="pt-4">
                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-blue-600 h-2 rounded-full animate-pulse" :style="{ width: progress + '%' }"></div>
                    </div>
                </div>
            </div>
            
            <div v-else class="space-y-6">
                <div class="flex justify-center">
                    <svg class="h-16 w-16 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                
                <div class="space-y-2">
                    <h2 class="text-2xl font-bold text-gray-900">
                        Готово!
                    </h2>
                    <p class="text-gray-600">
                        Файл успешно сгенерирован. Скачивание началось...
                    </p>
                </div>
                
                <div class="pt-4">
                    <a :href="downloadUrl" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Скачать повторно
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    taskId: Number,
    zipFileName: String,
});

const isReady = ref(false);
const downloadUrl = ref(null);
const progress = ref(10);
let checkInterval = null;
let progressInterval = null;

const checkFileStatus = async () => {
    try {
        const response = await axios.get(`/admin/tasks/${props.taskId}/download-status`, {
            params: {
                file: props.zipFileName
            }
        });
        
        if (response.data.ready) {
            isReady.value = true;
            downloadUrl.value = response.data.downloadUrl;
            
            // Очищаем интервалы
            if (checkInterval) {
                clearInterval(checkInterval);
            }
            if (progressInterval) {
                clearInterval(progressInterval);
            }
            
            // Автоматически начинаем скачивание
            window.location.href = response.data.downloadUrl;
        }
    } catch (error) {
        console.error('Ошибка проверки статуса:', error);
    }
};

onMounted(() => {
    // Проверяем статус каждые 2 секунды
    checkFileStatus();
    checkInterval = setInterval(checkFileStatus, 2000);
    
    // Анимация прогресса (визуальная, не реальная)
    progressInterval = setInterval(() => {
        if (progress.value < 90) {
            progress.value += Math.random() * 5;
        }
    }, 500);
});

onUnmounted(() => {
    if (checkInterval) {
        clearInterval(checkInterval);
    }
    if (progressInterval) {
        clearInterval(progressInterval);
    }
});
</script>
