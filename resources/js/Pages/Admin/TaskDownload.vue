<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 flex items-center justify-center p-4">
        <div class="max-w-lg w-full">
            <!-- Карточка -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <!-- Декоративная полоса сверху -->
                <div class="h-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>
                
                <div class="p-10">
                    <!-- Состояние загрузки -->
                    <div v-if="!isReady" class="space-y-8">
                        <!-- Иконка загрузки -->
                        <div class="flex justify-center">
                            <div class="relative">
                                <div class="absolute inset-0 bg-blue-500 rounded-full opacity-20 animate-ping"></div>
                                <div class="relative bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full p-6 shadow-lg">
                                    <svg class="h-12 w-12 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Текст -->
                        <div class="text-center space-y-3">
                            <h2 class="text-3xl font-bold text-gray-900">
                                Формируем задание
                            </h2>
                            <p class="text-lg text-gray-600">
                                Генерация ZIP файла с заданием и ответами
                            </p>
                            <p class="text-sm text-gray-500 pt-2">
                                ⏱️ Обычно это занимает 10-20 секунд
                            </p>
                        </div>
                        
                        <!-- Прогресс бар -->
                        <div class="space-y-3">
                            <div class="relative w-full h-3 bg-gray-200 rounded-full overflow-hidden">
                                <div 
                                    class="absolute inset-y-0 left-0 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 rounded-full transition-all duration-500 ease-out"
                                    :style="{ width: progress + '%' }"
                                ></div>
                                <div 
                                    class="absolute inset-y-0 left-0 bg-white opacity-30 rounded-full animate-pulse"
                                    :style="{ width: progress + '%' }"
                                ></div>
                            </div>
                            <p class="text-center text-sm text-gray-500">
                                {{ Math.round(progress) }}%
                            </p>
                        </div>
                        
                        <!-- Подсказка -->
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                            <div class="flex items-start">
                                <svg class="h-5 w-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-blue-800">
                                    Скачивание начнется автоматически после завершения генерации
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Состояние успеха -->
                    <div v-else class="space-y-8">
                        <!-- Иконка успеха -->
                        <div class="flex justify-center">
                            <div class="relative">
                                <div class="absolute inset-0 bg-green-500 rounded-full opacity-20 animate-ping"></div>
                                <div class="relative bg-gradient-to-br from-green-500 to-emerald-600 rounded-full p-6 shadow-lg">
                                    <svg class="h-12 w-12 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Текст успеха -->
                        <div class="text-center space-y-3">
                            <h2 class="text-3xl font-bold text-gray-900">
                                Готово!
                            </h2>
                            <p class="text-lg text-gray-600">
                                Файл успешно сгенерирован
                            </p>
                            <p class="text-sm text-green-600 font-medium pt-2">
                                ✓ Скачивание началось автоматически
                            </p>
                        </div>
                        
                        <!-- Кнопка повторного скачивания -->
                        <div class="flex flex-col gap-3">
                            <a :href="downloadUrl" 
                               class="inline-flex items-center justify-center px-6 py-4 text-base font-semibold rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Скачать повторно
                            </a>
                            
                            <button 
                                @click="window.close()" 
                                class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium rounded-lg text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                                Закрыть окно
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Информация о содержимом -->
            <div class="mt-6 bg-white/80 backdrop-blur-sm rounded-xl p-4 shadow-lg">
                <p class="text-sm text-gray-600 text-center">
                    📦 ZIP архив содержит: <span class="font-semibold">задание</span> и <span class="font-semibold">ответы</span> в формате PDF
                </p>
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
