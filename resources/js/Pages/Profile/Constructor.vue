<script setup>
import MainLayout from '../../Layouts/MainLayout.vue';
import Aside from '../../Components/Profile/Aside.vue';
import AnimatedSelect from '../../Components/UI/AnimatedSelect.vue';
import { onMounted, watch, ref } from 'vue';
import { useTaskStore } from '../../stores/taskStore';
import { storeToRefs } from 'pinia';
import { useToastStore } from '../../stores/toastStore';
import { Link } from '@inertiajs/vue3';

const taskStore = useTaskStore();
const toast = useToastStore();
const { grades, subjects, selectedGrade, selectedSubject } = storeToRefs(taskStore);
const creating = ref(false);
const step = ref('start');
const ready = ref(false);
const downloadMeta = ref({ taskId: null, file: null, url: null });

onMounted(() => {
    taskStore.getSubjects();
});

function handleExportPdfAuto() {
    creating.value = true;
    step.value = 'forming';
    taskStore.exportPdfAuto(selectedSubject.value)
        .then(async (data) => {
            toast.success('Формирование началось. Ожидайте готовности.');
            downloadMeta.value = {
                taskId: data.data.task_id,
                file: data.data.file,
                url: data.data.download_url
            };
            const poll = async () => {
                try {
                    const res = await taskStore.getAutoStatus(downloadMeta.value.taskId, downloadMeta.value.file);
                    const ready = !!(res && res.data && res.data.ready);
                    if (ready) {
                        step.value = 'ready';
                        downloadMeta.value.url = downloadMeta.value.url;
                        return;
                    }
                } catch (e) { }
                setTimeout(poll, 3000);
            };
            poll();
        })
        .catch(() => {
            toast.error('Не удалось запустить формирование. Попробуйте позже.');
            step.value = 'start';
        });
}

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
                                <div v-if="step === 'start'" class="profile_block_rect_content">
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

                                            <!-- <div class="home_create_setting">
                                                <p class="home_create_setting_tittle">Кол-во вариантов</p>
                                                <input type="text" placeholder="Введите кол-во вариантов" class="signin_main_rect_input">
                                            </div> -->

                                        </div>

                                        <div class="profile_constructor_paid_up_buttons">
                                            <button @click="handleExportPdfAuto" class="home_create_block_button">Автоматически</button>
                                            <Link href="/profile/constructor/manual"><button class="home_create_block_button">Вручную</button></Link>
                                        </div>




                                        <div class="profile_constructor_paid_texts">
                                            <p class="profile_constructor_paid_text"><span>Ручная сборка</span> - вы самостоятельно выбираете задания для вашей диагностики.</p>
                                            <p class="profile_constructor_paid_text"><span>Автоматическая сборка</span> - мы автоматически сгенерируем диагностику за несколько секунд.</p>


                                        </div>

                                    </div>


                                </div>
                                <div v-if="step !== 'start'" class="profile_block_rect_content">
                                    <h3 class="profile_block_rect_tittle">Создание варианта</h3>


                                    <div v-if="step === 'forming'" class="home_create_forming">
                                        <div class="home_create_forming_rect">
                                            <svg class="home_create_forming_icon" xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 96 96" fill="none">
                                                <g clip-path="url(#paint0_angular_172_618_clip_path)" data-figma-skip-parse="true">
                                                    <g transform="matrix(0.048 0 0 0.048 48 48)">
                                                        <foreignObject x="-1000" y="-1000" width="2000" height="2000">
                                                            <div xmlns="http://www.w3.org/1999/xhtml" style="background:conic-gradient(from 90deg,rgba(110, 0, 175, 0.0001) 0deg,rgba(148, 86, 255, 1) 359.964deg,rgba(110, 0, 175, 0.0001) 360deg);height:100%;width:100%;opacity:1"></div>
                                                        </foreignObject>
                                                    </g>
                                                </g>
                                                <path d="M96 48C96 74.5097 74.5097 96 48 96C21.4903 96 0 74.5097 0 48C0 21.4903 21.4903 0 48 0C74.5097 0 96 21.4903 96 48ZM16.8 48C16.8 65.2313 30.7687 79.2 48 79.2C65.2313 79.2 79.2 65.2313 79.2 48C79.2 30.7687 65.2313 16.8 48 16.8C30.7687 16.8 16.8 30.7687 16.8 48Z" data-figma-gradient-fill="{&quot;type&quot;:&quot;GRADIENT_ANGULAR&quot;,&quot;stops&quot;:[{&quot;color&quot;:{&quot;r&quot;:0.58041667938232422,&quot;g&quot;:0.33749997615814209,&quot;b&quot;:1.0,&quot;a&quot;:1.0},&quot;position&quot;:0.99989998340606689},{&quot;color&quot;:{&quot;r&quot;:0.43464052677154541,&quot;g&quot;:0.0,&quot;b&quot;:0.68627452850341797,&quot;a&quot;:9.9999997473787516e-05},&quot;position&quot;:1.0}],&quot;stopsVar&quot;:[{&quot;color&quot;:{&quot;r&quot;:0.58041667938232422,&quot;g&quot;:0.33749997615814209,&quot;b&quot;:1.0,&quot;a&quot;:1.0},&quot;position&quot;:0.99989998340606689},{&quot;color&quot;:{&quot;r&quot;:0.43464052677154541,&quot;g&quot;:0.0,&quot;b&quot;:0.68627452850341797,&quot;a&quot;:9.9999997473787516e-05},&quot;position&quot;:1.0}],&quot;transform&quot;:{&quot;m00&quot;:96.0,&quot;m01&quot;:0.0,&quot;m02&quot;:0.0,&quot;m10&quot;:0.0,&quot;m11&quot;:96.0,&quot;m12&quot;:0.0},&quot;opacity&quot;:1.0,&quot;blendMode&quot;:&quot;NORMAL&quot;,&quot;visible&quot;:true}"></path>
                                                <g clip-path="url(#paint1_angular_172_618_clip_path)" data-figma-skip-parse="true">
                                                    <g transform="matrix(-2.09815e-09 -0.048 0.048 -2.09815e-09 48 48)">
                                                        <foreignObject x="-1000" y="-1000" width="2000" height="2000">
                                                            <div xmlns="http://www.w3.org/1999/xhtml" style="background:conic-gradient(from 90deg,rgba(110, 0, 175, 0.0001) 0deg,rgba(148, 86, 255, 1) 359.964deg,rgba(110, 0, 175, 0.0001) 360deg);height:100%;width:100%;opacity:1"></div>
                                                        </foreignObject>
                                                    </g>
                                                </g>
                                                <path d="M48 -2.09815e-06C54.3034 -2.37368e-06 60.5452 1.24155 66.3688 3.65378C72.1924 6.06601 77.4839 9.60166 81.9411 14.0589C86.3983 18.5161 89.934 23.8076 92.3462 29.6312C94.7584 35.4548 96 41.6965 96 48L79.2 48C79.2 43.9028 78.393 39.8456 76.825 36.0603C75.2571 32.2749 72.9589 28.8355 70.0617 25.9383C67.1645 23.0411 63.7251 20.7429 59.9397 19.175C56.1544 17.607 52.0972 16.8 48 16.8L48 -2.09815e-06Z" data-figma-gradient-fill="{&quot;type&quot;:&quot;GRADIENT_ANGULAR&quot;,&quot;stops&quot;:[{&quot;color&quot;:{&quot;r&quot;:0.58041667938232422,&quot;g&quot;:0.33749997615814209,&quot;b&quot;:1.0,&quot;a&quot;:1.0},&quot;position&quot;:0.99989998340606689},{&quot;color&quot;:{&quot;r&quot;:0.43464052677154541,&quot;g&quot;:0.0,&quot;b&quot;:0.68627452850341797,&quot;a&quot;:9.9999997473787516e-05},&quot;position&quot;:1.0}],&quot;stopsVar&quot;:[{&quot;color&quot;:{&quot;r&quot;:0.58041667938232422,&quot;g&quot;:0.33749997615814209,&quot;b&quot;:1.0,&quot;a&quot;:1.0},&quot;position&quot;:0.99989998340606689},{&quot;color&quot;:{&quot;r&quot;:0.43464052677154541,&quot;g&quot;:0.0,&quot;b&quot;:0.68627452850341797,&quot;a&quot;:9.9999997473787516e-05},&quot;position&quot;:1.0}],&quot;transform&quot;:{&quot;m00&quot;:-4.1962930481531657e-06,&quot;m01&quot;:96.0,&quot;m02&quot;:0.0,&quot;m10&quot;:-96.0,&quot;m11&quot;:-4.1962930481531657e-06,&quot;m12&quot;:96.0},&quot;opacity&quot;:1.0,&quot;blendMode&quot;:&quot;NORMAL&quot;,&quot;visible&quot;:true}"></path>
                                                <defs>
                                                    <clipPath id="paint0_angular_172_618_clip_path">
                                                        <path d="M96 48C96 74.5097 74.5097 96 48 96C21.4903 96 0 74.5097 0 48C0 21.4903 21.4903 0 48 0C74.5097 0 96 21.4903 96 48ZM16.8 48C16.8 65.2313 30.7687 79.2 48 79.2C65.2313 79.2 79.2 65.2313 79.2 48C79.2 30.7687 65.2313 16.8 48 16.8C30.7687 16.8 16.8 30.7687 16.8 48Z"></path>
                                                    </clipPath>
                                                    <clipPath id="paint1_angular_172_618_clip_path">
                                                        <path d="M48 -2.09815e-06C54.3034 -2.37368e-06 60.5452 1.24155 66.3688 3.65378C72.1924 6.06601 77.4839 9.60166 81.9411 14.0589C86.3983 18.5161 89.934 23.8076 92.3462 29.6312C94.7584 35.4548 96 41.6965 96 48L79.2 48C79.2 43.9028 78.393 39.8456 76.825 36.0603C75.2571 32.2749 72.9589 28.8355 70.0617 25.9383C67.1645 23.0411 63.7251 20.7429 59.9397 19.175C56.1544 17.607 52.0972 16.8 48 16.8L48 -2.09815e-06Z"></path>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                        <div class="home_create_forming_texts">
                                            <p class="home_create_block_bottom_text">Дождитесь окончания формирования варианта или же сформируйте его самостаятельно. </p>
                                            <button class="home_create_block_button" disabled="">Дождитесь формирования варианта</button>
                                        </div>
                                    </div>


                                    <div v-if="step === 'ready'" class="home_create_saved">
                                        <div class="home_create_forming_rect">
                                            <svg class="home_create_saved" xmlns="http://www.w3.org/2000/svg" width="91" height="96" viewBox="0 0 91 96" fill="none" style="display: grid;">
                                                <path d="M34.3006 62.1951V56.1792H37.8708C39.8761 56.1792 41.0932 57.3151 41.0932 59.1813C41.0932 61.0939 39.9456 62.1951 37.9403 62.1951H34.3006Z" fill="#A8AABF"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M72.3689 39.2258H86.2105C88.8447 39.2258 91 41.3811 91 44.0153V69.1121C91 86.2105 79.0742 95.7895 64.3226 95.7895H26.6774C11.9258 95.7895 0 86.2105 0 69.1121V26.6774C0 9.57895 11.9258 0 26.6774 0H46.9847C49.6189 0 51.7742 2.15526 51.7742 4.78947V18.6311C51.7742 29.9821 61.0179 39.2258 72.3689 39.2258ZM19 70.7263H21.5965V63.7252H28.7831V61.5344H21.5965V56.2371H29.4438V54H19V70.7263ZM34.3006 70.7263V64.2931H37.8708L41.2786 70.7263H44.2692L40.5252 63.8874C42.5653 63.2035 43.7708 61.3721 43.7708 59.1118C43.7708 55.9937 41.6264 54 38.1837 54H31.7041V70.7263H34.3006ZM56.9038 68.4776V70.7263H46.2861V54H56.9038V56.2371H48.8826V61.1287H56.4749V63.2847H48.8826V68.4776H56.9038ZM70.1875 70.7263V68.4776H62.1663V63.2847H69.7586V61.1287H62.1663V56.2371H70.1875V54H59.5698V70.7263H70.1875Z" fill="#A8AABF"></path>
                                            </svg>
                                        </div>
                                        <div class="home_create_forming_texts">
                                            <p class="home_create_block_bottom_text">Дождитесь окончания формирования варианта или же сформируйте его самостаятельно. </p>
                                            <div class="profile_constructor_auto_buttons">
                                                <a :href="downloadMeta.url" class="home_create_block_button" id="createDownload">Скачать</a>
                                                <button @click="step = 'start'" class="home_create_block_button">Назад</button>

                                            </div>
                                        </div>
                                    </div>








                                    <div class="home_create_block_bottom">

                                        <Link href="/profile/constructor/manual"><button class="home_create_block_button">Сформировать вариант вручную</button></Link>


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