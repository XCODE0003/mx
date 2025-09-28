<script setup>
import MainLayout from '../Layouts/MainLayout.vue';
import { reactive } from 'vue';
import AnimatedSelect from '../Components/UI/AnimatedSelect.vue';
import { onMounted, watch, ref } from 'vue';
import { useTaskStore } from '../stores/taskStore';
import MathRenderer from '../Components/MathRenderer.vue';
import { storeToRefs } from 'pinia';
import { extractNumbersAndSymbols } from '../utils/text';

const taskStore = useTaskStore();
const { grades, subjects, selectedGrade, selectedSubject, groups } = storeToRefs(taskStore);
const selectedGroup = ref(null);
const tasks = ref([]);
const answers = reactive({});
const results = reactive({});
onMounted(async () => {
    await taskStore.getSubjects();
    await taskStore.getGroups();
    selectedGroup.value = groups.value[0];
    tasks.value = JSON.parse(JSON.stringify(await taskStore.getTasks(selectedGroup.value))).tasks;
});
watch(() => selectedGrade.value, async () => {
    await taskStore.getSubjects();
    await taskStore.getGroups();
}, { immediate: true });

watch(() => selectedSubject.value, async () => {
    await taskStore.getGroups();
});

watch(() => selectedGroup.value, async () => {
    tasks.value = JSON.parse(JSON.stringify(await taskStore.getTasks(selectedGroup.value))).tasks;
    console.log(tasks.value);
    // reset answers/results when tasks change
    Object.keys(answers).forEach((k) => delete answers[k]);
    Object.keys(results).forEach((k) => delete results[k]);
});

function normalizeAnswer(value) {
    if (!value && value !== 0) return '';
    return String(value)
        .toLowerCase()
        .replace(/[,\s]+/g, '')
        .replace(/\u00A0|\u202F/g, '')
        .trim();
}

function checkAnswer(task) {
    const user = normalizeAnswer(answers[task.id] ?? '');
    const correct = normalizeAnswer(task.response ?? '');
    if (!user) {
        results[task.id] = { status: 'empty', message: 'Введите ответ' };
        return;
    }
    results[task.id] = user === correct
        ? { status: 'ok', message: 'Верно' }
        : { status: 'bad', message: 'Неверно' };
}
</script>
<template>
    <MainLayout>
        <main class="banks">

            <section class="navigations_main">
                <div class="container">
                    <div class="navigations">
                        <a href="index.html" class="navigation_item_link"><svg xmlns="http://www.w3.org/2000/svg" width="19" height="12" viewBox="0 0 19 12" fill="none">
                                <path d="M0.469669 5.46967C0.176777 5.76256 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.469669 5.46967ZM19 5.25L1 5.25V6.75L19 6.75V5.25Z" fill="#848CE4"></path>
                            </svg> Назад</a>
                        <p class="navigation_item_text">Главная / Банк заданий ФИПИ</p>
                    </div>
                </div>
            </section>



            <section class="banks_main">
                <div class="container">
                    <h1 class="banks_main_tittle">Банк заданий ФИПИ</h1>
                    <div class="banks_main_rect">
                        <div class="banks_main_rect_content">
                            <h3 class="banks_main_rect_tittle">Открытый банк заданий ФИПИ</h3>
                            <div class="banks_main_rect_up">
                                <div class="banks_main_rect_up_item">
                                    <div class="home_create_setting">
                                        <p class="home_create_setting_tittle">Выберите паралель</p>
                                        <AnimatedSelect v-model="selectedGrade" :options="grades" placeholder="Выберите паралель" />
                                    </div>
                                </div>
                                <div class="banks_main_rect_up_item">
                                    <div class="home_create_setting">
                                        <p class="home_create_setting_tittle">Выберите предмет</p>
                                        <AnimatedSelect v-model="selectedSubject" :options="subjects" placeholder="Выберите предмет" />
                                    </div>
                                </div>
                                <div class="banks_main_rect_up_item">
                                    <p class="home_create_setting_tittle">Кол-во заданий в банке</p>
                                    <p class="banks_main_rect_up_item_all">{{ taskStore.totalTasks }}</p>
                                </div>
                                <div class="banks_main_rect_up_item">
                                    <p class="home_create_setting_tittle">Дата обновления банка</p>
                                    <p class="banks_main_rect_up_item_all">22.04.2025</p>
                                </div>
                                <div class="banks_main_rect_up_item">
                                    <button class="banks_main_rect_up_item_button">Обновить</button>
                                </div>
                            </div>



                            <div class="banks_main_rect_bottom" style="display: block;">
                                <div class="banks_main_rect_bottom_pages">
                                    <button @click="selectedGroup = group" :class="{ 'banks_main_rect_bottom_page_active': selectedGroup === group }" v-for="group in groups" :key="group.id" class="banks_main_rect_bottom_page">{{ extractNumbersAndSymbols(group.title) }}</button>
                                </div>

                                <p class="banks_main_rect_bottom_show">Скрыть</p>

                                <div class="banks_main_rect_bottom_tasks">


                                    <div v-for="(task,index) in tasks" :key="task.id" class="banks_main_rect_bottom_task">
                                        <div class="banks_main_rect_bottom_task_up">
                                            <p class="banks_main_rect_bottom_task_up_tittle">Задание {{ index + 1 }} </p>
                                            <p class="banks_main_rect_bottom_task_up_number">Номер задания в ФИПИ : <span> {{ task.article_id }}</span></p>
                                        </div>
                                        <p class="banks_main_rect_bottom_text">
                                            <MathRenderer :content="task.question" />
                                        </p>
                                        <div class="banks_main_rect_bottom_task_up_rect">
                                            <input
                                                v-model="answers[task.id]"
                                                type="text"
                                                placeholder="Введите"
                                                class="banks_main_rect_bottom_task_up_rect_input"
                                            >
                                            <button
                                                class="banks_main_rect_bottom_task_up_rect_button"
                                                @click="checkAnswer(task)"
                                            >Проверить</button>
                                        </div>
                                        <div v-if="results[task.id]" style="margin-top:8px;">
                                            <span v-if="results[task.id].status === 'ok'" style="color:#3BF763;">{{ results[task.id].message }}</span>
                                            <span v-else-if="results[task.id].status === 'bad'" style="color:#FF4646;">{{ results[task.id].message }}</span>
                                            <span v-else style="color:#A8AABF;">{{ results[task.id].message }}</span>
                                        </div>
                                    </div>



                                </div>

                                <div class="paginations">
                                    <div class="container">
                                        <div class="paginations_items">
                                            <p class="paginations_text">Показано с 9 по 18</p>
                                            <div class="paginations_pages">
                                                <button class="paginations_arrow"><svg xmlns="http://www.w3.org/2000/svg" width="9" height="14" viewBox="0 0 9 14" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.315166 7.67216C-0.105055 7.30094 -0.105055 6.69906 0.315166 6.32784L7.16308 0.278417C7.5833 -0.0928057 8.26461 -0.0928057 8.68483 0.278417C9.10505 0.64964 9.10505 1.25151 8.68483 1.62273L2.5978 7L8.68483 12.3773C9.10505 12.7485 9.10505 13.3504 8.68483 13.7216C8.26461 14.0928 7.5833 14.0928 7.16308 13.7216L0.315166 7.67216Z" fill="#393C5B"></path>
                                                    </svg></button>
                                                <button class="paginations_page">1</button>
                                                <button class="paginations_page paginations_page_active">2</button>
                                                <button class="paginations_page">3</button>
                                                <button class="paginations_page">4</button>
                                                <button class="paginations_page">5</button>
                                                <button class="paginations_page">...</button>
                                                <button class="paginations_page">10</button>
                                                <button class="paginations_arrow"><svg xmlns="http://www.w3.org/2000/svg" width="9" height="14" viewBox="0 0 9 14" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M8.68483 7.67216C9.10506 7.30094 9.10506 6.69906 8.68483 6.32784L1.83692 0.278417C1.4167 -0.0928057 0.735389 -0.0928057 0.315166 0.278417C-0.105055 0.64964 -0.105055 1.25151 0.315166 1.62273L6.4022 7L0.315166 12.3773C-0.105055 12.7485 -0.105055 13.3504 0.315166 13.7216C0.735389 14.0928 1.4167 14.0928 1.83692 13.7216L8.68483 7.67216Z" fill="#393C5B"></path>
                                                    </svg></button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>



                        </div>
                    </div>
                </div>
            </section>




        </main>
    </MainLayout>
</template>