<script setup>
import MainLayout from '../../Layouts/MainLayout.vue';
import Aside from '../../Components/Profile/Aside.vue';
import AnimatedSelect from '../../Components/UI/AnimatedSelect.vue';
import { onMounted, watch } from 'vue';
import { useTaskStore } from '../../stores/taskStore';
import { storeToRefs } from 'pinia';

const taskStore = useTaskStore();
const { grades, subjects, selectedGrade, selectedSubject } = storeToRefs(taskStore);

onMounted(() => {
    taskStore.getSubjects();
});

watch(() => selectedGrade.value, () => {
    taskStore.getSubjects();
}, { immediate: true });

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

                        <div class="profile_block">
                            <div class="profile_block_rect">
                                <div class="profile_block_rect_content">
                                    <h3 class="profile_block_rect_tittle">Создание варианта</h3>

                                    <div class="profile_constructor_paid">

                                        <div class="profile_constructor_paid_up">
                                            <div class="home_create_setting">
                                                <p class="home_create_setting_tittle">Выберите паралель</p>
                                                <AnimatedSelect v-model="selectedGrade" :options="grades" placeholder="Выберите паралель" />
                                            </div>

                                            <div class="home_create_setting">
                                                <p class="home_create_setting_tittle">Выберите предмет</p>
                                                <AnimatedSelect v-model="selectedSubject" :options="subjects" placeholder="Выберите предмет" />
                                            </div>

                                            <div class="home_create_setting">
                                                <p class="home_create_setting_tittle">Кол-во вариантов</p>
                                                <input type="text" placeholder="Введите кол-во вариантов" class="signin_main_rect_input">
                                            </div>

                                        </div>

                                        <div class="profile_constructor_paid_up_buttons">
                                            <a href="/profile/constructor/auto"><button class="home_create_block_button">Автоматически</button></a>
                                            <a href="/profile/constructor/manual"><button class="home_create_block_button">Вручную</button></a>
                                        </div>




                                        <div class="profile_constructor_paid_texts">
                                            <p class="profile_constructor_paid_text"><span>Ручная сборка</span> - вы самостоятельно выбираете задания для вашей диагностики.</p>
                                            <p class="profile_constructor_paid_text"><span>Автоматическая сборка</span> - мы автоматически сгенерируем диагностику за несколько секунд.</p>


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