<template>
    <div class="download-page">
        <div class="container">
            <!-- Карточка -->
            <div class="card">
                <!-- Декоративная полоса сверху -->
                <div class="top-stripe"></div>
                
                <div class="card-content">
                    <!-- Состояние загрузки -->
                    <div v-if="!isReady" class="loading-state">
                        <!-- Иконка загрузки -->
                        <div class="icon-wrapper">
                            <div class="ping-effect"></div>
                            <div class="spinner-circle">
                                <svg class="spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="spinner-track" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="spinner-fill" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Текст -->
                        <div class="text-center">
                            <h2 class="title">Формируем задание</h2>
                            <p class="subtitle">Генерация ZIP файла с заданием и ответами</p>
                            <p class="hint">⏱️ Обычно это занимает 10-20 секунд</p>
                        </div>
                        
                        <!-- Прогресс бар -->
                        <div class="progress-wrapper">
                            <div class="progress-bar">
                                <div class="progress-fill" :style="{ width: progress + '%' }"></div>
                                <div class="progress-shine" :style="{ width: progress + '%' }"></div>
                            </div>
                            <p class="progress-text">{{ Math.round(progress) }}%</p>
                        </div>
                        
                        <!-- Подсказка -->
                        <div class="info-box">
                            <svg class="info-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <p class="info-text">
                                Скачивание начнется автоматически после завершения генерации
                            </p>
                        </div>
                    </div>
                    
                    <!-- Состояние успеха -->
                    <div v-else class="success-state">
                        <!-- Иконка успеха -->
                        <div class="icon-wrapper">
                            <div class="ping-effect success"></div>
                            <div class="success-circle">
                                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Текст успеха -->
                        <div class="text-center">
                            <h2 class="title">Готово!</h2>
                            <p class="subtitle">Файл успешно сгенерирован</p>
                            <p class="success-text">✓ Скачивание началось автоматически</p>
                        </div>
                        
                        <!-- Кнопки -->
                        <div class="button-group">
                            <a :href="downloadUrl" class="btn btn-primary">
                                <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Скачать повторно
                            </a>
                            
                            <button @click="window.close()" class="btn btn-secondary">
                                Закрыть окно
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Информация о содержимом -->
            <div class="footer-info">
                <p>📦 ZIP архив содержит: <strong>задание</strong> и <strong>ответы</strong> в формате PDF</p>
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

<style scoped>
.download-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #e0f2fe 0%, #ddd6fe 50%, #f3e8ff 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.container {
    max-width: 600px;
    width: 100%;
}

.card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    overflow: hidden;
}

.top-stripe {
    height: 8px;
    background: linear-gradient(90deg, #3b82f6 0%, #6366f1 50%, #a855f7 100%);
}

.card-content {
    padding: 60px 40px;
}

.loading-state,
.success-state {
    display: flex;
    flex-direction: column;
    gap: 40px;
}

.icon-wrapper {
    display: flex;
    justify-content: center;
    position: relative;
}

.ping-effect {
    position: absolute;
    inset: 0;
    background: #3b82f6;
    border-radius: 50%;
    opacity: 0.2;
    animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.ping-effect.success {
    background: #10b981;
}

@keyframes ping {
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}

.spinner-circle,
.success-circle {
    position: relative;
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
    border-radius: 50%;
    padding: 24px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.success-circle {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.spinner {
    width: 48px;
    height: 48px;
    color: white;
    animation: spin 1s linear infinite;
}

.checkmark {
    width: 48px;
    height: 48px;
    color: white;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.spinner-track {
    opacity: 0.25;
}

.spinner-fill {
    opacity: 0.75;
}

.text-center {
    text-align: center;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.title {
    font-size: 32px;
    font-weight: bold;
    color: #111827;
    margin: 0;
}

.subtitle {
    font-size: 18px;
    color: #4b5563;
    margin: 0;
}

.hint {
    font-size: 14px;
    color: #6b7280;
    margin: 8px 0 0 0;
}

.success-text {
    font-size: 14px;
    color: #10b981;
    font-weight: 600;
    margin: 8px 0 0 0;
}

.progress-wrapper {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.progress-bar {
    position: relative;
    width: 100%;
    height: 12px;
    background: #e5e7eb;
    border-radius: 999px;
    overflow: hidden;
}

.progress-fill {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background: linear-gradient(90deg, #3b82f6 0%, #6366f1 50%, #a855f7 100%);
    border-radius: 999px;
    transition: width 0.5s ease-out;
}

.progress-shine {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background: white;
    opacity: 0.3;
    border-radius: 999px;
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 0.3;
    }
    50% {
        opacity: 0.5;
    }
}

.progress-text {
    text-align: center;
    font-size: 14px;
    color: #6b7280;
    margin: 0;
}

.info-box {
    background: #eff6ff;
    border-left: 4px solid #3b82f6;
    border-radius: 0 8px 8px 0;
    padding: 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.info-icon {
    width: 20px;
    height: 20px;
    color: #3b82f6;
    flex-shrink: 0;
    margin-top: 2px;
}

.info-text {
    font-size: 14px;
    color: #1e40af;
    margin: 0;
}

.button-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 16px 24px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-primary {
    background: linear-gradient(90deg, #2563eb 0%, #4f46e5 100%);
    color: white;
    box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(90deg, #1d4ed8 0%, #4338ca 100%);
    box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.4);
    transform: translateY(-2px);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-icon {
    width: 20px;
    height: 20px;
    margin-right: 8px;
}

.footer-info {
    margin-top: 24px;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.footer-info p {
    font-size: 14px;
    color: #4b5563;
    text-align: center;
    margin: 0;
}

.footer-info strong {
    font-weight: 600;
    color: #111827;
}
</style>
