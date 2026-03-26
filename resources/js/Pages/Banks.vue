<script setup>
import MainLayout from '../Layouts/MainLayout.vue';
import AnimatedSelect from '../Components/UI/AnimatedSelect.vue';
import axiosClient from '../api/axios';
import { onBeforeUnmount, onMounted, ref, watch, computed } from 'vue';
import { extractNumbersAndSymbols } from '../utils/text';
import { subtaskNumberFromGroupTitle, taskAudioUrls } from '../utils/banksTask';

const grades = [
    { key: 'oge', value: 'ОГЭ' },
    { key: 'ege', value: 'ЕГЭ' },
];

const selectedGrade = ref('oge');
const subjects = ref([]);
const selectedSubject = ref(null);
const groups = ref([]);
const selectedGroup = ref(null);

/** Размер страницы синхронизирован с запросом к banks-api (per_page). */
const PER_PAGE = 5;
const currentPage = ref(1);
const tasksOnPage = ref([]);
const tasksTotal = ref(0);
const tasksLoading = ref(false);

const iframeLoading = ref({});

function handleIframeLoad(taskId) {
    iframeLoading.value = { ...iframeLoading.value, [taskId]: false };
}

function handleIframeResizeMessage(event) {
    const data = event && event.data;
    if (!data || data.type !== 'TASK_IFRAME_HEIGHT') return;
    const taskId = data.taskId;
    const height = Number(data.height || 0);
    if (!taskId || !height) return;
    const iframe = document.querySelector(`iframe[data-task-id="${taskId}"]`);
    if (!iframe) return;
    const minHeight = 120;
    iframe.style.height = `${Math.max(minHeight, height)}px`;
}

const totalBankTasks = computed(() =>
    groups.value.reduce((s, g) => s + (g.tasks_count || 0), 0),
);

const totalPages = computed(() =>
    Math.max(1, Math.ceil(tasksTotal.value / PER_PAGE)),
);

const paginatedTasks = computed(() => tasksOnPage.value);

const paginationItems = computed(() => {
    const last = totalPages.value;
    const cur = currentPage.value;
    if (last <= 1) return [];
    const delta = 2;
    const range = [];
    for (let i = 1; i <= last; i++) {
        if (i === 1 || i === last || (i >= cur - delta && i <= cur + delta)) {
            range.push(i);
        }
    }
    const out = [];
    let prev = 0;
    for (const i of range) {
        if (prev && i - prev > 1) {
            out.push({ type: 'ellipsis' });
        }
        out.push({ type: 'page', value: i });
        prev = i;
    }
    return out;
});

function goToPage(page) {
    let p = page;
    if (p < 1) p = 1;
    if (p > totalPages.value) p = totalPages.value;
    currentPage.value = p;
}

function prevPage() {
    goToPage(currentPage.value - 1);
}

function nextPage() {
    goToPage(currentPage.value + 1);
}

function indexInFullGroup(idxOnPage) {
    return (currentPage.value - 1) * PER_PAGE + idxOnPage;
}

function bundleHeading() {
    if (!selectedGroup.value) return '';
    return extractNumbersAndSymbols(selectedGroup.value.title) || selectedGroup.value.title;
}

function subHeading(idxOnPage) {
    if (tasksTotal.value <= 1) return '';
    const gtitle = selectedGroup.value?.title || '';
    const n = subtaskNumberFromGroupTitle(gtitle, indexInFullGroup(idxOnPage));
    return `Задание ${n}`;
}

async function fetchSubjects() {
    const res = await axiosClient.get(`/banks-api/subjects/${selectedGrade.value}`);
    const list = res.data || [];
    subjects.value = list.map((s) => ({
        key: s.subject_id ?? s.id,
        value: s.name,
    }));
    selectedSubject.value = subjects.value[0]?.key ?? null;
}

async function fetchGroups() {
    if (selectedSubject.value == null) {
        groups.value = [];
        selectedGroup.value = null;
        return;
    }
    const res = await axiosClient.get(
        `/banks-api/groups/${selectedGrade.value}/${selectedSubject.value}`,
    );
    groups.value = res.data.groups || [];
    selectedGroup.value = groups.value[0] ?? null;
}

async function loadGroupTasks() {
    if (!selectedGroup.value?.id) {
        tasksOnPage.value = [];
        tasksTotal.value = 0;
        return;
    }

    tasksOnPage.value = [];
    tasksLoading.value = true;
    try {
        const res = await axiosClient.get(`/banks-api/tasks/${selectedGroup.value.id}`, {
            params: { page: currentPage.value, per_page: PER_PAGE },
        });
        const list = res.data.tasks || [];
        tasksTotal.value = Number(res.data.total) || 0;
        const last = Math.max(1, Number(res.data.last_page) || 1);
        if (currentPage.value > last) {
            currentPage.value = last;
            tasksOnPage.value = [];
            return;
        }
        tasksOnPage.value = list;
    } catch (e) {
        console.error(e);
        tasksOnPage.value = [];
        tasksTotal.value = 0;
    } finally {
        tasksLoading.value = false;
    }
}

onMounted(async () => {
    window.addEventListener('message', handleIframeResizeMessage);
    try {
        await fetchSubjects();
        await fetchGroups();
    } catch (e) {
        console.error(e);
    }
});

watch(selectedGrade, async () => {
    try {
        await fetchSubjects();
        await fetchGroups();
    } catch (e) {
        console.error(e);
    }
});

watch(selectedSubject, async () => {
    try {
        await fetchGroups();
    } catch (e) {
        console.error(e);
    }
});

watch(
    () => selectedGroup.value?.id,
    (newId, oldId) => {
        if (newId !== oldId) {
            currentPage.value = 1;
        }
    },
);

watch(
    [selectedGroup, currentPage],
    async () => {
        if (!selectedGroup.value?.id) {
            tasksOnPage.value = [];
            tasksTotal.value = 0;
            return;
        }
        await loadGroupTasks();
    },
    { immediate: true },
);

watch(
    paginatedTasks,
    (list) => {
        const map = {};
        (list || []).forEach((t) => {
            map[t.id] = true;
        });
        iframeLoading.value = map;
    },
    { immediate: true },
);

onBeforeUnmount(() => {
    window.removeEventListener('message', handleIframeResizeMessage);
});
</script>

<template>
    <MainLayout>
        <main class="banks">
            <section class="navigations_main">
                <div class="container">
                    <div class="navigations">
                        <a href="/" class="navigation_item_link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="12" viewBox="0 0 19 12" fill="none">
                                <path d="M0.469669 5.46967C0.176777 5.76256 0.176777 6.23744 0.469669 6.53033L5.24264 11.3033C5.53553 11.5962 6.01041 11.5962 6.3033 11.3033C6.59619 11.0104 6.59619 10.5355 6.3033 10.2426L2.06066 6L6.3033 1.75736C6.59619 1.46447 6.59619 0.989593 6.3033 0.696699C6.01041 0.403806 5.53553 0.403806 5.24264 0.696699L0.469669 5.46967ZM19 5.25L1 5.25V6.75L19 6.75V5.25Z" fill="#848CE4" />
                            </svg>
                            Назад
                        </a>
                        <p class="navigation_item_text">Главная / Банк заданий</p>
                    </div>
                </div>
            </section>

            <section class="banks_main">
                <div class="container">
                    <h1 class="banks_main_tittle">Банк заданий</h1>
                    <div class="banks_main_rect">
                        <div class="banks_main_rect_content">
                            <h3 class="banks_main_rect_tittle">Открытый банк заданий</h3>
                            <div class="banks_main_rect_up">
                                <div class="banks_main_rect_up_item">
                                    <div class="home_create_setting">
                                        <p class="home_create_setting_tittle">Выберите параллель</p>
                                        <AnimatedSelect v-model="selectedGrade" :options="grades" placeholder="Выберите параллель" />
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
                                    <p class="banks_main_rect_up_item_all">{{ totalBankTasks }}</p>
                                </div>
                                <div class="banks_main_rect_up_item">
                                    <p class="home_create_setting_tittle">Дата обновления банка</p>
                                    <p class="banks_main_rect_up_item_all">—</p>
                                </div>
                            </div>

                            <div class="banks_main_rect_bottom" style="display: block;">
                                <div class="banks_main_rect_bottom_pages">
                                    <button
                                        v-for="group in groups"
                                        :key="group.id"
                                        type="button"
                                        :class="{ banks_main_rect_bottom_page_active: selectedGroup?.id === group.id }"
                                        class="banks_main_rect_bottom_page"
                                        @click="selectedGroup = group"
                                    >
                                        {{ extractNumbersAndSymbols(group.title) }}
                                    </button>
                                </div>

                                <div class="banks-task-bundle">
                                    <h3 v-if="selectedGroup && tasksTotal" class="banks-task-bundle__title">
                                        {{ bundleHeading() }}
                                    </h3>

                                    <p v-if="tasksLoading" class="banks-task-bundle__loading">Загрузка заданий…</p>

                                    <div
                                        v-for="(task, idx) in paginatedTasks"
                                        :key="task.id"
                                        class="banks_main_rect_bottom_task banks-task-bundle__item"
                                    >
                                        <div class="banks_main_rect_bottom_task_up">
                                            <p class="banks_main_rect_bottom_task_up_tittle">
                                                {{ subHeading(idx) || `Задание ${indexInFullGroup(idx) + 1}` }}
                                            </p>
                                            <p class="banks_main_rect_bottom_task_up_number">
                                                Номер задания в базе: <span>{{ task.id }}</span>
                                            </p>
                                        </div>

                                        <div v-if="taskAudioUrls(task).length" class="banks-audio">
                                            <audio
                                                v-for="(url, ai) in taskAudioUrls(task)"
                                                :key="ai"
                                                class="banks-audio__el"
                                                controls
                                                preload="metadata"
                                                :src="url"
                                            />
                                        </div>

                                        <div class="banks_main_rect_bottom_text">
                                            <div v-if="iframeLoading[task.id]" class="iframe_loader">
                                                <div class="iframe_spinner"></div>
                                            </div>
                                            <iframe
                                                :data-task-id="task.id"
                                                :src="`/tasks/${task.id}/view_tasks?bank=1`"
                                                width="100%"
                                                style="width: 100%; border: none; display: block"
                                                @load="handleIframeLoad(task.id)"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div v-if="tasksTotal > PER_PAGE" class="banks-paginations">
                                    <p class="banks-paginations__meta">
                                        Показано
                                        {{ (currentPage - 1) * PER_PAGE + 1 }}–{{ Math.min(currentPage * PER_PAGE, tasksTotal) }}
                                        из {{ tasksTotal }} · стр. {{ currentPage }} / {{ totalPages }}
                                    </p>
                                    <div class="banks-paginations__controls">
                                        <button
                                            type="button"
                                            class="banks-paginations__arrow"
                                            :disabled="currentPage === 1 || tasksLoading"
                                            aria-label="Предыдущая страница"
                                            @click="prevPage"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="9" height="14" viewBox="0 0 9 14" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.315166 7.67216C-0.105055 7.30094 -0.105055 6.69906 0.315166 6.32784L7.16308 0.278417C7.5833 -0.0928057 8.26461 -0.0928057 8.68483 0.278417C9.10505 0.64964 9.10505 1.25151 8.68483 1.62273L2.5978 7L8.68483 12.3773C9.10505 12.7485 9.10505 13.3504 8.68483 13.7216C8.26461 14.0928 7.5833 14.0928 7.16308 13.7216L0.315166 7.67216Z" fill="#393C5B" />
                                            </svg>
                                        </button>
                                        <div class="banks-paginations__pages" role="navigation" aria-label="Номера страниц">
                                            <template v-for="(item, pi) in paginationItems" :key="'pg-' + pi + (item.type === 'page' ? '-' + item.value : '-e')">
                                                <span v-if="item.type === 'ellipsis'" class="banks-paginations__ellipsis" aria-hidden="true">…</span>
                                                <button
                                                    v-else
                                                    type="button"
                                                    class="banks-paginations__page"
                                                    :class="{ 'banks-paginations__page--active': item.value === currentPage }"
                                                    :disabled="tasksLoading"
                                                    @click="goToPage(item.value)"
                                                >
                                                    {{ item.value }}
                                                </button>
                                            </template>
                                        </div>
                                        <button
                                            type="button"
                                            class="banks-paginations__arrow"
                                            :disabled="currentPage === totalPages || tasksLoading"
                                            aria-label="Следующая страница"
                                            @click="nextPage"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="9" height="14" viewBox="0 0 9 14" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.68483 7.67216C9.10506 7.30094 9.10506 6.69906 8.68483 6.32784L1.83692 0.278417C1.4167 -0.0928057 0.735389 -0.0928057 0.315166 0.278417C-0.105055 0.64964 -0.105055 1.25151 0.315166 1.62273L6.4022 7L0.315166 12.3773C-0.105055 12.7485 -0.105055 13.3504 0.315166 13.7216C0.735389 14.0928 1.4167 14.0928 1.83692 13.7216L8.68483 7.67216Z" fill="#393C5B" />
                                            </svg>
                                        </button>
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

<style scoped>
.banks-task-bundle__title {
    margin: 24px 0 16px;
    font-size: 1.35rem;
    font-weight: 600;
    color: #393c5b;
    font-family: 'SF Pro Display', system-ui, sans-serif;
}

.banks-task-bundle__item + .banks-task-bundle__item {
    margin-top: 28px;
    padding-top: 24px;
    border-top: 1px solid rgba(57, 60, 91, 0.12);
}

.banks_main_rect_bottom_text {
    position: relative;
    min-height: 200px;
}

.banks_main_rect_bottom_text :deep(iframe) {
    min-height: 240px;
    display: block;
}

.iframe_loader {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.6);
    z-index: 1;
}

.iframe_spinner {
    width: 36px;
    height: 36px;
    border: 3px solid #e5e7eb;
    border-top-color: #8f70ff;
    border-radius: 50%;
    animation: banks-iframe-spin 0.9s linear infinite;
}

@keyframes banks-iframe-spin {
    to {
        transform: rotate(360deg);
    }
}

.banks-audio {
    margin: 12px 0 16px;
}

.banks-audio__el {
    display: block;
    width: 100%;
    max-width: 480px;
    margin-bottom: 8px;
}

.banks-task-bundle__loading {
    margin: 12px 0 20px;
    font-size: 15px;
    color: rgba(57, 60, 91, 0.75);
    font-family: 'SF Pro Display', system-ui, sans-serif;
}

.banks-paginations {
    margin-top: 28px;
    padding-top: 20px;
    border-top: 1px solid rgba(57, 60, 91, 0.12);
    max-width: 100%;
    box-sizing: border-box;
}

.banks-paginations__meta {
    margin: 0 0 12px;
    color: #393c5b;
    font-family: 'SF Pro Display', system-ui, sans-serif;
    font-size: 14px;
    line-height: 1.45;
    word-break: break-word;
}

.banks-paginations__controls {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px 10px;
    max-width: 100%;
    min-width: 0;
}

.banks-paginations__pages {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 4px 6px;
    min-width: 0;
    flex: 1 1 0;
    justify-content: center;
    max-width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    padding: 4px 2px;
    scrollbar-width: thin;
}

.banks-paginations__arrow {
    flex-shrink: 0;
    cursor: pointer;
    outline: none;
    border: none;
    background: transparent;
    padding: 6px 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s;
}

.banks-paginations__arrow:hover:not(:disabled) {
    transform: scale(1.05);
}

.banks-paginations__arrow:disabled {
    opacity: 0.35;
    cursor: not-allowed;
}

.banks-paginations__page {
    flex-shrink: 0;
    min-width: 2.25rem;
    padding: 6px 10px;
    color: #393c5b;
    font-family: 'SF Pro Display', system-ui, sans-serif;
    font-size: 15px;
    border: none;
    background: transparent;
    cursor: pointer;
    border-radius: 10px;
    line-height: 1.2;
}

.banks-paginations__page:hover:not(:disabled):not(.banks-paginations__page--active) {
    background: rgba(143, 112, 255, 0.1);
}

.banks-paginations__page--active {
    background: #8f70ff;
    color: #fff;
}

.banks-paginations__page:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.banks-paginations__ellipsis {
    flex-shrink: 0;
    padding: 0 4px;
    color: rgba(57, 60, 91, 0.45);
    user-select: none;
}

@media (min-width: 640px) {
    .banks-paginations {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .banks-paginations__meta {
        margin-bottom: 0;
        flex: 1 1 200px;
        min-width: 0;
        max-width: 100%;
    }

    .banks-paginations__controls {
        flex: 0 1 auto;
        justify-content: flex-end;
        max-width: min(100%, 520px);
    }
}
</style>
