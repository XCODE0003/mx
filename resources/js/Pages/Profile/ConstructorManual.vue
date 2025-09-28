<script setup>
import MainLayout from '../../Layouts/MainLayout.vue';
import Aside from '../../Components/Profile/Aside.vue';
import AnimatedSelect from '../../Components/UI/AnimatedSelect.vue';
import { onMounted, onBeforeUnmount, watch, ref, computed } from 'vue';
import { useTaskStore } from '../../stores/taskStore';
import { useToastStore } from '../../stores/toastStore';
import MathRenderer from '../../Components/MathRenderer.vue';
import { storeToRefs } from 'pinia';
import { extractNumbersAndSymbols } from '../../utils/text';
import { formatTaskText } from '../../utils/func';
import { defineProps } from 'vue';

const props = defineProps({
    is_subscribe: {
        type: Boolean,
        default: false
    }
});
const taskStore = useTaskStore();
const toast = useToastStore();
const { grades, subjects, selectedGrade, selectedSubject, groups } = storeToRefs(taskStore);
const selectedGroup = computed({
    get: () => taskStore.selectedGroup,
    set: (val) => taskStore.setSelectedGroup(val)
});
const tasks = computed(() => taskStore.tasks || []);
// Пагинация
const perPage = ref(5);
const currentPage = ref(1);
const totalPages = computed(() => {
    const total = tasks.value?.length ?? 0;
    return Math.max(1, Math.ceil(total / perPage.value));
});
const creating = ref(false);
const isTaskSelected = (task) => taskStore.isTaskSelected(task);
const toggleTaskSelection = (task) => taskStore.toggleTaskSelection(task);
const paginatedTasks = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    const end = start + perPage.value;
    return (tasks.value ?? []).slice(start, end);
});
function goToPage(page) {
    if (page < 1) page = 1;
    if (page > totalPages.value) page = totalPages.value;
    currentPage.value = page;
}
const iframeLoading = ref({});
function handleIframeLoad(taskId) {
    iframeLoading.value = { ...iframeLoading.value, [taskId]: false };
}
const downloadMeta = ref({ taskId: null, file: null, url: null });
function handleExportPdfManual() {
    creating.value = true;
    const selectedCount = Object.keys(taskStore.selectedTaskByGroup || {}).length;
    const groupsCount = groups.value?.length || 0;
    if (selectedCount === 0) {
        toast.info('Выберите по одному заданию в каждой группе');
        return;
    }
    if (selectedCount !== groupsCount) {
        toast.info('Выбраны не все группы. Завершите выбор для каждой группы.');
        return;
    }
    const tasks = Object.values(taskStore.selectedTaskByGroup);
    taskStore.exportPdfManual(tasks)
        .then(async (data) => {
            toast.success('Формирование началось. Ожидайте готовности.');
            downloadMeta.value = {
                taskId: data.data.task_id,
                file: data.data.file,
                url: data.data.download_url
            };
            // Опрос статуса
            const poll = async () => {
                try {
                    const res = await taskStore.getManualStatus(downloadMeta.value.taskId, downloadMeta.value.file);
                    const ready = !!(res && res.data && res.data.ready);
                    if (ready) {
                        creating.value = false;
                        window.location.href = downloadMeta.value.url;
                        return;
                    }
                } catch (e) {}
                setTimeout(poll, 3000);
            };
            poll();
        })
        .catch(() => {
            toast.error('Не удалось запустить формирование. Попробуйте позже.');
            creating.value = false;
        });

}
function prevPage() { goToPage(currentPage.value - 1); }
function nextPage() { goToPage(currentPage.value + 1); }
watch(() => selectedGrade.value, async () => {
    await taskStore.getSubjects();
    await taskStore.getGroups();
    selectedGroup.value = groups.value[0];
}, { immediate: true });

watch(() => selectedSubject.value, async () => {
    await taskStore.getGroups();
    selectedGroup.value = groups.value[0];
});

watch(() => selectedGroup.value, async () => {
    await taskStore.getTasks(selectedGroup.value);
    currentPage.value = 1;
});

watch(paginatedTasks, (list) => {
    const map = {};
    (list || []).forEach((t) => { map[t.id] = true; });
    iframeLoading.value = map;
}, { immediate: true });

// Автонастройка высоты iframe по содержимому (postMessage)
function handleIframeResizeMessage(event) {
    var data = event && event.data;
    if (!data || data.type !== 'TASK_IFRAME_HEIGHT') return;
    var taskId = data.taskId;
    var height = Number(data.height || 0);
    if (!taskId || !height) return;
    const selector = `iframe[data-task-id="${taskId}"]`;
    const iframe = document.querySelector(selector);
    if (!iframe) return;
    // Минимальная высота для визуальной стабильности
    const minHeight = 120;
    iframe.style.height = Math.max(minHeight, height) + 'px';
}

onMounted(() => {
    window.addEventListener('message', handleIframeResizeMessage);
});

onBeforeUnmount(() => {
    window.removeEventListener('message', handleIframeResizeMessage);
});
</script>

<template>
    <MainLayout>
        <main class="account">

            <section class="navigations_main">
                <div class="container">
                    <div class="navigations">
                        <a href="/" class="navigation_item_link"><svg xmlns="http://www.w3.org/2000/svg" width="19" height="12" viewBox="0 0 19 12" fill="none">
                                <path d="M0.469669 5.46967C0.176777 5.76256 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.469669 5.46967ZM19 5.25L1 5.25V6.75L19 6.75V5.25Z" fill="#848CE4" />
                            </svg> Назад</a>
                        <p class="navigation_item_text">Главная / Профиль</p>
                    </div>
                </div>
            </section>



            <section class="profile">
                <div class="container">
                    <h1 class="profile_tittle">Конструктор</h1>
                    <div class="profile_blocks">

                        <div class="profile_block">
                            <Aside active="constructor" />
                        </div>

                        <div class="profile_block" style="position: relative;">
                            <div class="profile_block_rect" >
                                <div class="profile_block_rect_content ">
                                    <h3 class="profile_block_rect_tittle">Создание варианта</h3>




                                    <div class="profile_constructor_manual_up">
                                        <div class="profile_constructor_manual_up_item">
                                            <p class="profile_constructor_manual_up_item_tittle">Настойки создания варианта</p>
                                            <div class="profile_constructor_manual_up_item_settings">
                                                <div class="home_create_setting">
                                                    <p class="home_create_setting_tittle">Выберите паралель</p>
                                                    <AnimatedSelect v-model="selectedGrade" :options="grades" placeholder="Выберите паралель"></AnimatedSelect>
                                                </div>
                                                <div class="home_create_setting">
                                                    <p class="home_create_setting_tittle">Выберите предмет</p>
                                                    <AnimatedSelect v-model="selectedSubject" :options="subjects" placeholder="Выберите предмет"></AnimatedSelect>
                                                </div>
                                                <!-- <div class="home_create_setting">
                                                    <p class="home_create_setting_tittle">Кол-во вариантов</p>
                                                    <input type="text" placeholder="2" class="signin_main_rect_input">

                                                </div> -->

                                            </div>
                                        </div>
                                        <div class="profile_constructor_manual_up_item">
                                            <p class="profile_constructor_manual_up_item_tittle">Предпросмотр вариантов</p>
                                            <p class="home_create_setting_tittle">Варианты</p>

                                            <div class="profile_constructor_manual_slider">
                                                <div class="profile_constructor_manual_sliders">
                                                    <div class="profile_constructor_manual_slide">
                                                        <div class="profile_constructor_manual_slide_content">
                                                            <p class="profile_constructor_manual_slide_up">1 Вариант</p>
                                                            <p class="profile_constructor_manual_slide_tittle">21 / 35</p>
                                                            <a href="profile_constructor_preview.html" class="profile_constructor_manual_slide_show">Показать</a>
                                                        </div>
                                                    </div>

                                                    <div class="profile_constructor_manual_slide">
                                                        <div class="profile_constructor_manual_slide_content">
                                                            <p class="profile_constructor_manual_slide_up">2 Вариант</p>
                                                            <p class="profile_constructor_manual_slide_tittle">22 / 35</p>
                                                            <a href="profile_constructor_preview.html" class="profile_constructor_manual_slide_show">Показать</a>
                                                        </div>
                                                    </div>

                                                    <div class="profile_constructor_manual_slide">
                                                        <div class="profile_constructor_manual_slide_content">
                                                            <p class="profile_constructor_manual_slide_up">3 Вариант</p>
                                                            <p class="profile_constructor_manual_slide_tittle">24 / 35</p>
                                                            <a href="profile_constructor_preview.html" class="profile_constructor_manual_slide_show">Показать</a>
                                                        </div>
                                                    </div>

                                                    <div class="profile_constructor_manual_slide">
                                                        <div class="profile_constructor_manual_slide_content">
                                                            <p class="profile_constructor_manual_slide_up">1 Вариант</p>
                                                            <p class="profile_constructor_manual_slide_tittle">21 / 35</p>
                                                            <a href="profile_constructor_preview.html" class="profile_constructor_manual_slide_show">Показать</a>
                                                        </div>
                                                    </div>

                                                    <div class="profile_constructor_manual_slide">
                                                        <div class="profile_constructor_manual_slide_content">
                                                            <p class="profile_constructor_manual_slide_up">2 Вариант</p>
                                                            <p class="profile_constructor_manual_slide_tittle">22 / 35</p>
                                                            <a href="profile_constructor_preview.html" class="profile_constructor_manual_slide_show">Показать</a>
                                                        </div>
                                                    </div>

                                                    <div class="profile_constructor_manual_slide">
                                                        <div class="profile_constructor_manual_slide_content">
                                                            <p class="profile_constructor_manual_slide_up">3 Вариант</p>
                                                            <p class="profile_constructor_manual_slide_tittle">24 / 35</p>
                                                            <a href="profile_constructor_preview.html" class="profile_constructor_manual_slide_show">Показать</a>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="profile_constructor_manual_slider_navs">
                                                    <svg class="profile_constructor_manual_slider_nav_left" xmlns="http://www.w3.org/2000/svg" width="7" height="10" viewBox="0 0 7 10" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.245129 5.48011C-0.0817098 5.21495 -0.0817098 4.78505 0.245129 4.51989L5.57128 0.198869C5.89812 -0.0662898 6.42803 -0.0662898 6.75487 0.198869C7.08171 0.464029 7.08171 0.893936 6.75487 1.1591L2.02051 5L6.75487 8.8409C7.08171 9.10606 7.08171 9.53597 6.75487 9.80113C6.42803 10.0663 5.89812 10.0663 5.57128 9.80113L0.245129 5.48011Z" fill="#393C5B" />
                                                    </svg>

                                                    <button class="profile_constructor_manual_slider_nav profile_constructor_manual_slider_nav_active"></button>
                                                    <button class="profile_constructor_manual_slider_nav"></button>
                                                    <button class="profile_constructor_manual_slider_nav"></button>
                                                    <button class="profile_constructor_manual_slider_nav"></button>
                                                    <svg class="profile_constructor_manual_slider_nav_right" xmlns="http://www.w3.org/2000/svg" width="7" height="10" viewBox="0 0 7 10" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6.75487 5.48011C7.08171 5.21495 7.08171 4.78505 6.75487 4.51989L1.42872 0.198869C1.10188 -0.0662898 0.571969 -0.0662898 0.24513 0.198869C-0.0817099 0.464029 -0.0817099 0.893936 0.24513 1.1591L4.97949 5L0.24513 8.8409C-0.0817099 9.10606 -0.0817099 9.53597 0.24513 9.80113C0.571969 10.0663 1.10188 10.0663 1.42872 9.80113L6.75487 5.48011Z" fill="#393C5B" />
                                                    </svg>
                                                </div>
                                            </div>



                                        </div>
                                    </div>


                                    <div class="profile_constructor_manual_bottom">
                                        <div class="banks_main_rect_bottom_pages">
                                            <button @click="selectedGroup = group" :class="{ 'banks_main_rect_bottom_page_active': selectedGroup === group, 'selected': taskStore.groupHasSelection(group.id) }" v-for="group in groups" :key="group.id" class="banks_main_rect_bottom_page">{{ extractNumbersAndSymbols(group.title) }}</button>
                                        </div>

                                        <div class="banks_main_rect_bottom_tasks">

                                            <div v-for="(task, index) in paginatedTasks" :key="task.id" class="banks_main_rect_bottom_task">
                                                <div class="banks_main_rect_bottom_task_up">
                                                    <p class="banks_main_rect_bottom_task_up_tittle">Задание {{ (currentPage - 1) * perPage + index + 1 }} </p>
                                                </div>
                                                <div class="banks_main_rect_bottom_text">
                                                    <div v-if="iframeLoading[task.id]" class="iframe_loader"><div class="iframe_spinner"></div></div>
                                                    <iframe @load="handleIframeLoad(task.id)" :data-task-id="task.id" :src="`/tasks/${task.id}/view_tasks`" width="100%" style="width:100%;border:none;display:block;"></iframe>
                                                </div>

                                                <div class="home_create_setting">
                                                    <!-- <p class="home_create_setting_tittle">Выберите вариант</p> -->
                                                    <div class="home_create_setting_items">
                                                        <!-- <div class="home_create_setting_select">
                                                            <div class="home_create_setting_select_content">
                                                                <p class="home_create_setting_selected">1 Вариант <svg xmlns="http://www.w3.org/2000/svg" width="15" height="9" viewBox="0 0 15 9" fill="none">
                                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M14.4351 1.70711L8.07117 8.07107C7.68065 8.46159 7.04748 8.46159 6.65696 8.07107L0.292998 1.70711C-0.0975266 1.31658 -0.0975266 0.683417 0.292998 0.292893C0.683523 -0.0976317 1.31669 -0.0976317 1.70721 0.292893L7.36407 5.94975L13.0209 0.292893C13.4114 -0.0976312 14.0446 -0.0976311 14.4351 0.292893C14.8257 0.683418 14.8257 1.31658 14.4351 1.70711Z" fill="#393C5B" />
                                                                    </svg></p>
                                                                <div class="home_create_setting_select_rect">
                                                                    <div class="home_create_setting_select_rect_content">
                                                                        <div class="home_create_setting_select_rect_text">1 Вариант <span class="home_create_setting_select_rect_text_setting"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" viewBox="0 0 8 12" fill="none">
                                                                                    <g opacity="0.985">
                                                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M2.70199 0C3.56732 0 4.43267 0 5.298 0C5.3082 0.0228151 5.31867 0.0462525 5.32941 0.0703125C5.33987 0.24983 5.34335 0.429518 5.33987 0.609375C6.14244 0.605468 6.94496 0.609375 7.74746 0.621094C7.86072 0.661934 7.94099 0.743965 7.98822 0.867188C7.99869 1.31243 8.00218 1.75774 7.99869 2.20312C5.3329 2.20312 2.66709 2.20312 0.00130759 2.20312C-0.00218027 1.75774 0.00130968 1.31243 0.0117754 0.867188C0.0590122 0.743965 0.139264 0.661934 0.252534 0.621094C1.05504 0.609375 1.85756 0.605468 2.66012 0.609375C2.65665 0.429518 2.66012 0.24983 2.67059 0.0703125C2.68133 0.0462525 2.6918 0.0228151 2.70199 0Z" fill="#8F70FF" />
                                                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6.65881 12C4.88626 12 3.11373 12 1.34118 12C1.09293 11.9054 0.956849 11.7101 0.93294 11.4141C0.813796 8.57773 0.691667 5.74179 0.566568 2.90625C2.85551 2.90625 5.14448 2.90625 7.43343 2.90625C7.31062 5.79731 7.18151 8.68793 7.04612 11.5781C6.97389 11.7879 6.84478 11.9286 6.65881 12ZM2.22048 4.80469C2.38114 4.8008 2.54165 4.80469 2.70199 4.81641C2.77527 4.83593 2.82064 4.88672 2.83808 4.96875C2.85204 6.62501 2.85204 8.28124 2.83808 9.9375C2.81364 10.0273 2.7613 10.0859 2.68106 10.1133C2.54148 10.1289 2.40193 10.1289 2.26235 10.1133C2.1574 10.0857 2.09809 10.0114 2.0844 9.89062C2.07043 8.27344 2.07043 6.65625 2.0844 5.03906C2.0935 4.9323 2.13887 4.85416 2.22048 4.80469ZM3.74877 4.80469C3.9164 4.8008 4.08389 4.80469 4.25122 4.81641C4.30356 4.84376 4.34194 4.88672 4.36637 4.94531C4.38033 6.62501 4.38033 8.30468 4.36637 9.98438C4.32712 10.0474 4.27478 10.0903 4.20935 10.1133C4.06978 10.1289 3.93022 10.1289 3.79064 10.1133C3.72522 10.0903 3.67288 10.0474 3.63363 9.98438C3.61966 8.30468 3.61966 6.62501 3.63363 4.94531C3.6621 4.88618 3.70047 4.8393 3.74877 4.80469ZM5.298 4.80469C5.45866 4.8008 5.61917 4.80469 5.77952 4.81641C5.8637 4.86019 5.90905 4.93441 5.9156 5.03906C5.92957 6.65625 5.92957 8.27344 5.9156 9.89062C5.90191 10.0114 5.8426 10.0857 5.73765 10.1133C5.57548 10.1343 5.41497 10.1265 5.25613 10.0898C5.20777 10.0515 5.17637 10.0007 5.16192 9.9375C5.14796 8.28124 5.14796 6.62501 5.16192 4.96875C5.18323 4.88637 5.2286 4.83169 5.298 4.80469Z" fill="#8F70FF" />
                                                                                    </g>
                                                                                </svg>
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.5382 1.70711L5.1742 8.07107C4.78368 8.46159 4.15051 8.46159 3.75999 8.07107L0.292825 4.83125C-0.0977003 4.44073 -0.0977003 3.80757 0.292825 3.41704C0.683349 3.02652 1.31651 3.02652 1.70704 3.41704L4.46709 5.94975L10.1239 0.292893C10.5145 -0.0976312 11.1476 -0.0976311 11.5382 0.292893C11.9287 0.683418 11.9287 1.31658 11.5382 1.70711Z" fill="#54F645" />
                                                                                </svg></span></div>
                                                                        <div class="home_create_setting_select_rect_text">2 Вариант <span class="home_create_setting_select_rect_text_setting"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" viewBox="0 0 8 12" fill="none">
                                                                                    <g opacity="0.985">
                                                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M2.70199 0C3.56732 0 4.43267 0 5.298 0C5.3082 0.0228151 5.31867 0.0462525 5.32941 0.0703125C5.33987 0.24983 5.34335 0.429518 5.33987 0.609375C6.14244 0.605468 6.94496 0.609375 7.74746 0.621094C7.86072 0.661934 7.94099 0.743965 7.98822 0.867188C7.99869 1.31243 8.00218 1.75774 7.99869 2.20312C5.3329 2.20312 2.66709 2.20312 0.00130759 2.20312C-0.00218027 1.75774 0.00130968 1.31243 0.0117754 0.867188C0.0590122 0.743965 0.139264 0.661934 0.252534 0.621094C1.05504 0.609375 1.85756 0.605468 2.66012 0.609375C2.65665 0.429518 2.66012 0.24983 2.67059 0.0703125C2.68133 0.0462525 2.6918 0.0228151 2.70199 0Z" fill="#8F70FF" />
                                                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6.65881 12C4.88626 12 3.11373 12 1.34118 12C1.09293 11.9054 0.956849 11.7101 0.93294 11.4141C0.813796 8.57773 0.691667 5.74179 0.566568 2.90625C2.85551 2.90625 5.14448 2.90625 7.43343 2.90625C7.31062 5.79731 7.18151 8.68793 7.04612 11.5781C6.97389 11.7879 6.84478 11.9286 6.65881 12ZM2.22048 4.80469C2.38114 4.8008 2.54165 4.80469 2.70199 4.81641C2.77527 4.83593 2.82064 4.88672 2.83808 4.96875C2.85204 6.62501 2.85204 8.28124 2.83808 9.9375C2.81364 10.0273 2.7613 10.0859 2.68106 10.1133C2.54148 10.1289 2.40193 10.1289 2.26235 10.1133C2.1574 10.0857 2.09809 10.0114 2.0844 9.89062C2.07043 8.27344 2.07043 6.65625 2.0844 5.03906C2.0935 4.9323 2.13887 4.85416 2.22048 4.80469ZM3.74877 4.80469C3.9164 4.8008 4.08389 4.80469 4.25122 4.81641C4.30356 4.84376 4.34194 4.88672 4.36637 4.94531C4.38033 6.62501 4.38033 8.30468 4.36637 9.98438C4.32712 10.0474 4.27478 10.0903 4.20935 10.1133C4.06978 10.1289 3.93022 10.1289 3.79064 10.1133C3.72522 10.0903 3.67288 10.0474 3.63363 9.98438C3.61966 8.30468 3.61966 6.62501 3.63363 4.94531C3.6621 4.88618 3.70047 4.8393 3.74877 4.80469ZM5.298 4.80469C5.45866 4.8008 5.61917 4.80469 5.77952 4.81641C5.8637 4.86019 5.90905 4.93441 5.9156 5.03906C5.92957 6.65625 5.92957 8.27344 5.9156 9.89062C5.90191 10.0114 5.8426 10.0857 5.73765 10.1133C5.57548 10.1343 5.41497 10.1265 5.25613 10.0898C5.20777 10.0515 5.17637 10.0007 5.16192 9.9375C5.14796 8.28124 5.14796 6.62501 5.16192 4.96875C5.18323 4.88637 5.2286 4.83169 5.298 4.80469Z" fill="#8F70FF" />
                                                                                    </g>
                                                                                </svg>
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.5382 1.70711L5.1742 8.07107C4.78368 8.46159 4.15051 8.46159 3.75999 8.07107L0.292825 4.83125C-0.0977003 4.44073 -0.0977003 3.80757 0.292825 3.41704C0.683349 3.02652 1.31651 3.02652 1.70704 3.41704L4.46709 5.94975L10.1239 0.292893C10.5145 -0.0976312 11.1476 -0.0976311 11.5382 0.292893C11.9287 0.683418 11.9287 1.31658 11.5382 1.70711Z" fill="#54F645" />
                                                                                </svg></span></div>
                                                                        <div class="home_create_setting_select_rect_text">3 Вариант <span class="home_create_setting_select_rect_text_setting"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" viewBox="0 0 8 12" fill="none">
                                                                                    <g opacity="0.985">
                                                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M2.70199 0C3.56732 0 4.43267 0 5.298 0C5.3082 0.0228151 5.31867 0.0462525 5.32941 0.0703125C5.33987 0.24983 5.34335 0.429518 5.33987 0.609375C6.14244 0.605468 6.94496 0.609375 7.74746 0.621094C7.86072 0.661934 7.94099 0.743965 7.98822 0.867188C7.99869 1.31243 8.00218 1.75774 7.99869 2.20312C5.3329 2.20312 2.66709 2.20312 0.00130759 2.20312C-0.00218027 1.75774 0.00130968 1.31243 0.0117754 0.867188C0.0590122 0.743965 0.139264 0.661934 0.252534 0.621094C1.05504 0.609375 1.85756 0.605468 2.66012 0.609375C2.65665 0.429518 2.66012 0.24983 2.67059 0.0703125C2.68133 0.0462525 2.6918 0.0228151 2.70199 0Z" fill="#8F70FF" />
                                                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6.65881 12C4.88626 12 3.11373 12 1.34118 12C1.09293 11.9054 0.956849 11.7101 0.93294 11.4141C0.813796 8.57773 0.691667 5.74179 0.566568 2.90625C2.85551 2.90625 5.14448 2.90625 7.43343 2.90625C7.31062 5.79731 7.18151 8.68793 7.04612 11.5781C6.97389 11.7879 6.84478 11.9286 6.65881 12ZM2.22048 4.80469C2.38114 4.8008 2.54165 4.80469 2.70199 4.81641C2.77527 4.83593 2.82064 4.88672 2.83808 4.96875C2.85204 6.62501 2.85204 8.28124 2.83808 9.9375C2.81364 10.0273 2.7613 10.0859 2.68106 10.1133C2.54148 10.1289 2.40193 10.1289 2.26235 10.1133C2.1574 10.0857 2.09809 10.0114 2.0844 9.89062C2.07043 8.27344 2.07043 6.65625 2.0844 5.03906C2.0935 4.9323 2.13887 4.85416 2.22048 4.80469ZM3.74877 4.80469C3.9164 4.8008 4.08389 4.80469 4.25122 4.81641C4.30356 4.84376 4.34194 4.88672 4.36637 4.94531C4.38033 6.62501 4.38033 8.30468 4.36637 9.98438C4.32712 10.0474 4.27478 10.0903 4.20935 10.1133C4.06978 10.1289 3.93022 10.1289 3.79064 10.1133C3.72522 10.0903 3.67288 10.0474 3.63363 9.98438C3.61966 8.30468 3.61966 6.62501 3.63363 4.94531C3.6621 4.88618 3.70047 4.8393 3.74877 4.80469ZM5.298 4.80469C5.45866 4.8008 5.61917 4.80469 5.77952 4.81641C5.8637 4.86019 5.90905 4.93441 5.9156 5.03906C5.92957 6.65625 5.92957 8.27344 5.9156 9.89062C5.90191 10.0114 5.8426 10.0857 5.73765 10.1133C5.57548 10.1343 5.41497 10.1265 5.25613 10.0898C5.20777 10.0515 5.17637 10.0007 5.16192 9.9375C5.14796 8.28124 5.14796 6.62501 5.16192 4.96875C5.18323 4.88637 5.2286 4.83169 5.298 4.80469Z" fill="#8F70FF" />
                                                                                    </g>
                                                                                </svg>
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.5382 1.70711L5.1742 8.07107C4.78368 8.46159 4.15051 8.46159 3.75999 8.07107L0.292825 4.83125C-0.0977003 4.44073 -0.0977003 3.80757 0.292825 3.41704C0.683349 3.02652 1.31651 3.02652 1.70704 3.41704L4.46709 5.94975L10.1239 0.292893C10.5145 -0.0976312 11.1476 -0.0976311 11.5382 0.292893C11.9287 0.683418 11.9287 1.31658 11.5382 1.70711Z" fill="#54F645" />
                                                                                </svg></span></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div> -->

                                                        <button @click="toggleTaskSelection(task)" class="home_create_block_button">{{ isTaskSelected(task) ? 'Убрать' : 'Добавить' }}</button>
                                                    </div>

                                                </div>


                                            </div>




                                        </div>

                                        <div class="banks_main_rect_bottom_pages" style="margin-top: 16px; display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                            <button class="banks_main_rect_bottom_page" @click="prevPage" :disabled="currentPage === 1">Назад</button>
                                            <button v-for="page in totalPages" :key="page" class="banks_main_rect_bottom_page" :class="{ 'banks_main_rect_bottom_page_active': page === currentPage }" @click="goToPage(page)">{{ page }}</button>
                                            <button class="banks_main_rect_bottom_page" @click="nextPage" :disabled="currentPage === totalPages">Вперёд</button>
                                            <span style="margin-left: 8px; color: #888a9f;">Стр. {{ currentPage }} / {{ totalPages }}</span>
                                        </div>

                                        <div class="profile_manual_bottom_rect">
                                            <div class="profile_manual_bottom_rect_content">
                                                <div class="profile_manual_bottom_rect_items">
                                                    <div class="hidden profile_manual_bottom_rect_item">
                                                        <button class="home_create_block_button"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M11 22C17.0751 22 22 17.0751 22 11C22 4.92487 17.0751 0 11 0C4.92487 0 0 4.92487 0 11C0 17.0751 4.92487 22 11 22ZM11.7269 5.5H10.0501L10.1704 12.7563H11.6066L11.7269 5.5ZM9.89969 15.4935C9.89969 16.0424 10.3358 16.4635 10.8848 16.4635C11.4487 16.4635 11.8773 16.0424 11.8773 15.4935C11.8773 14.9445 11.4487 14.5234 10.8848 14.5234C10.3358 14.5234 9.89969 14.9445 9.89969 15.4935Z" fill="white" />
                                                            </svg> Миксование вариантов</button>
                                                        <p class="profile_manual_bottom_rect_info"><span>*Миксование</span> - варианты ответов в заданиях будут случайным образом перемешаны (там где это возможно), а также задания будут перемешаны среди вариантов. это функция повышает объективность проведения диагностики и полностью повторяет формирование </p>
                                                    </div>
                                                    <div class="profile_manual_bottom_rect_item">
                                                        <div class="home_create_forming_rect">
                                                            <svg v-if="creating" class="home_create_forming_icon" xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 96 96" fill="none">
                                                                <g clip-path="url(#paint0_angular_172_618_clip_path)" data-figma-skip-parse="true">
                                                                    <g transform="matrix(0.048 0 0 0.048 48 48)">
                                                                        <foreignObject x="-1000" y="-1000" width="2000" height="2000">
                                                                            <div xmlns="http://www.w3.org/1999/xhtml" style="background:conic-gradient(from 90deg,rgba(110, 0, 175, 0.0001) 0deg,rgba(148, 86, 255, 1) 359.964deg,rgba(110, 0, 175, 0.0001) 360deg);height:100%;width:100%;opacity:1"></div>
                                                                        </foreignObject>
                                                                    </g>
                                                                </g>
                                                                <path d="M96 48C96 74.5097 74.5097 96 48 96C21.4903 96 0 74.5097 0 48C0 21.4903 21.4903 0 48 0C74.5097 0 96 21.4903 96 48ZM16.8 48C16.8 65.2313 30.7687 79.2 48 79.2C65.2313 79.2 79.2 65.2313 79.2 48C79.2 30.7687 65.2313 16.8 48 16.8C30.7687 16.8 16.8 30.7687 16.8 48Z" data-figma-gradient-fill="{&quot;type&quot;:&quot;GRADIENT_ANGULAR&quot;,&quot;stops&quot;:[{&quot;color&quot;:{&quot;r&quot;:0.58041667938232422,&quot;g&quot;:0.33749997615814209,&quot;b&quot;:1.0,&quot;a&quot;:1.0},&quot;position&quot;:0.99989998340606689},{&quot;color&quot;:{&quot;r&quot;:0.43464052677154541,&quot;g&quot;:0.0,&quot;b&quot;:0.68627452850341797,&quot;a&quot;:9.9999997473787516e-05},&quot;position&quot;:1.0}],&quot;stopsVar&quot;:[{&quot;color&quot;:{&quot;r&quot;:0.58041667938232422,&quot;g&quot;:0.33749997615814209,&quot;b&quot;:1.0,&quot;a&quot;:1.0},&quot;position&quot;:0.99989998340606689},{&quot;color&quot;:{&quot;r&quot;:0.43464052677154541,&quot;g&quot;:0.0,&quot;b&quot;:0.68627452850341797,&quot;a&quot;:9.9999997473787516e-05},&quot;position&quot;:1.0}],&quot;transform&quot;:{&quot;m00&quot;:96.0,&quot;m01&quot;:0.0,&quot;m02&quot;:0.0,&quot;m10&quot;:0.0,&quot;m11&quot;:96.0,&quot;m12&quot;:0.0},&quot;opacity&quot;:1.0,&quot;blendMode&quot;:&quot;NORMAL&quot;,&quot;visible&quot;:true}" />
                                                                <g clip-path="url(#paint1_angular_172_618_clip_path)" data-figma-skip-parse="true">
                                                                    <g transform="matrix(-2.09815e-09 -0.048 0.048 -2.09815e-09 48 48)">
                                                                        <foreignObject x="-1000" y="-1000" width="2000" height="2000">
                                                                            <div xmlns="http://www.w3.org/1999/xhtml" style="background:conic-gradient(from 90deg,rgba(110, 0, 175, 0.0001) 0deg,rgba(148, 86, 255, 1) 359.964deg,rgba(110, 0, 175, 0.0001) 360deg);height:100%;width:100%;opacity:1"></div>
                                                                        </foreignObject>
                                                                    </g>
                                                                </g>
                                                                <path d="M48 -2.09815e-06C54.3034 -2.37368e-06 60.5452 1.24155 66.3688 3.65378C72.1924 6.06601 77.4839 9.60166 81.9411 14.0589C86.3983 18.5161 89.934 23.8076 92.3462 29.6312C94.7584 35.4548 96 41.6965 96 48L79.2 48C79.2 43.9028 78.393 39.8456 76.825 36.0603C75.2571 32.2749 72.9589 28.8355 70.0617 25.9383C67.1645 23.0411 63.7251 20.7429 59.9397 19.175C56.1544 17.607 52.0972 16.8 48 16.8L48 -2.09815e-06Z" data-figma-gradient-fill="{&quot;type&quot;:&quot;GRADIENT_ANGULAR&quot;,&quot;stops&quot;:[{&quot;color&quot;:{&quot;r&quot;:0.58041667938232422,&quot;g&quot;:0.33749997615814209,&quot;b&quot;:1.0,&quot;a&quot;:1.0},&quot;position&quot;:0.99989998340606689},{&quot;color&quot;:{&quot;r&quot;:0.43464052677154541,&quot;g&quot;:0.0,&quot;b&quot;:0.68627452850341797,&quot;a&quot;:9.9999997473787516e-05},&quot;position&quot;:1.0}],&quot;stopsVar&quot;:[{&quot;color&quot;:{&quot;r&quot;:0.58041667938232422,&quot;g&quot;:0.33749997615814209,&quot;b&quot;:1.0,&quot;a&quot;:1.0},&quot;position&quot;:0.99989998340606689},{&quot;color&quot;:{&quot;r&quot;:0.43464052677154541,&quot;g&quot;:0.0,&quot;b&quot;:0.68627452850341797,&quot;a&quot;:9.9999997473787516e-05},&quot;position&quot;:1.0}],&quot;transform&quot;:{&quot;m00&quot;:-4.1962930481531657e-06,&quot;m01&quot;:96.0,&quot;m02&quot;:0.0,&quot;m10&quot;:-96.0,&quot;m11&quot;:-4.1962930481531657e-06,&quot;m12&quot;:96.0},&quot;opacity&quot;:1.0,&quot;blendMode&quot;:&quot;NORMAL&quot;,&quot;visible&quot;:true}" />
                                                                <defs>
                                                                    <clipPath id="paint0_angular_172_618_clip_path">
                                                                        <path d="M96 48C96 74.5097 74.5097 96 48 96C21.4903 96 0 74.5097 0 48C0 21.4903 21.4903 0 48 0C74.5097 0 96 21.4903 96 48ZM16.8 48C16.8 65.2313 30.7687 79.2 48 79.2C65.2313 79.2 79.2 65.2313 79.2 48C79.2 30.7687 65.2313 16.8 48 16.8C30.7687 16.8 16.8 30.7687 16.8 48Z" />
                                                                    </clipPath>
                                                                    <clipPath id="paint1_angular_172_618_clip_path">
                                                                        <path d="M48 -2.09815e-06C54.3034 -2.37368e-06 60.5452 1.24155 66.3688 3.65378C72.1924 6.06601 77.4839 9.60166 81.9411 14.0589C86.3983 18.5161 89.934 23.8076 92.3462 29.6312C94.7584 35.4548 96 41.6965 96 48L79.2 48C79.2 43.9028 78.393 39.8456 76.825 36.0603C75.2571 32.2749 72.9589 28.8355 70.0617 25.9383C67.1645 23.0411 63.7251 20.7429 59.9397 19.175C56.1544 17.607 52.0972 16.8 48 16.8L48 -2.09815e-06Z" />
                                                                    </clipPath>
                                                                </defs>
                                                            </svg>
                                                            <svg v-else width="120" height="120" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g opacity="0.5"> <path fill-rule="evenodd" clip-rule="evenodd" d="M14 22H10C6.22876 22 4.34315 22 3.17157 20.8284C2 19.6569 2 17.7712 2 14V10C2 6.22876 2 4.34315 3.17157 3.17157C4.34315 2 6.23869 2 10.0298 2C10.6358 2 11.1214 2 11.53 2.01666C11.5166 2.09659 11.5095 2.17813 11.5092 2.26057L11.5 5.09497C11.4999 6.19207 11.4998 7.16164 11.6049 7.94316C11.7188 8.79028 11.9803 9.63726 12.6716 10.3285C13.3628 11.0198 14.2098 11.2813 15.0569 11.3952C15.8385 11.5003 16.808 11.5002 17.9051 11.5001L18 11.5001H21.9574C22 12.0344 22 12.6901 22 13.5629V14C22 17.7712 22 19.6569 20.8284 20.8284C19.6569 22 17.7712 22 14 22Z" fill="#8f70ff"></path> </g> <path d="M6 13.75C5.58579 13.75 5.25 14.0858 5.25 14.5C5.25 14.9142 5.58579 15.25 6 15.25H14C14.4142 15.25 14.75 14.9142 14.75 14.5C14.75 14.0858 14.4142 13.75 14 13.75H6Z" fill="#8f70ff"></path> <path d="M6 17.25C5.58579 17.25 5.25 17.5858 5.25 18C5.25 18.4142 5.58579 18.75 6 18.75H11.5C11.9142 18.75 12.25 18.4142 12.25 18C12.25 17.5858 11.9142 17.25 11.5 17.25H6Z" fill="#8f70ff"></path> <path d="M11.5092 2.2601L11.5 5.0945C11.4999 6.1916 11.4998 7.16117 11.6049 7.94269C11.7188 8.78981 11.9803 9.6368 12.6716 10.3281C13.3629 11.0193 14.2098 11.2808 15.057 11.3947C15.8385 11.4998 16.808 11.4997 17.9051 11.4996L21.9574 11.4996C21.9698 11.6552 21.9786 11.821 21.9848 11.9995H22C22 11.732 22 11.5983 21.9901 11.4408C21.9335 10.5463 21.5617 9.52125 21.0315 8.79853C20.9382 8.6713 20.8743 8.59493 20.7467 8.44218C19.9542 7.49359 18.911 6.31193 18 5.49953C17.1892 4.77645 16.0787 3.98536 15.1101 3.3385C14.2781 2.78275 13.862 2.50487 13.2915 2.29834C13.1403 2.24359 12.9408 2.18311 12.7846 2.14466C12.4006 2.05013 12.0268 2.01725 11.5 2.00586L11.5092 2.2601Z" fill="#8f70ff"></path> </g></svg>
                                                        </div>

                                                    </div>

                                                    <div class="profile_manual_bottom_rect_item">
                                                        <p class="profile_manual_bottom_rect_item_text">Дождитесь окончания формирования вашего варианта. Каждый вариант уникален и формируется из множества заданий</p>
                                                        <button @click="handleExportPdfManual" :class="{ 'home_create_forming': creating }" class="home_create_block_button">{{ creating ? "Идет формирование" : 'Начать формирование' }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>




                                </div>
                                <div v-if="!is_subscribe" class="overlay_no_subscribe">
                                    <div class="overlay_no_subscribe_content">
                                        <h3 >Для создания вариантов в ручную, вам необходимо приобести платную подписку в которой будет доступен полный функционал</h3>
                                        <button class="profile_account_middle_change_button">Приобрести подписку</button>
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
<style>
iframe {
    width: 100%;
    height: 100%;
    border: none;
}
.banks_main_rect_bottom_text { position: relative; }
.iframe_loader {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.6);
    z-index: 1;
}
.iframe_spinner {
    width: 36px;
    height: 36px;
    border: 3px solid #e5e7eb;
    border-top-color: #8f70ff;
    border-radius: 50%;
    animation: spin 0.9s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>