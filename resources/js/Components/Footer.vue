<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const showScrollToTop = ref(true);
let mainContainer = null;
const THRESHOLD = 300;

function getWindowScrollTop() {
  return window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
}

function updateVisibility() {
  const pageTop = getWindowScrollTop();
  const containerTop = mainContainer ? mainContainer.scrollTop : 0;
  showScrollToTop.value = Math.max(pageTop, containerTop) > THRESHOLD;
}

const handleScroll = () => updateVisibility();

const scrollToTop = () => {
  // 1) Основной документ
  try { window.scrollTo({ top: 0, behavior: 'smooth' }); } catch {}
  const se = document.scrollingElement || document.documentElement;
  if (se) {
    try { se.scrollTo({ top: 0, behavior: 'smooth' }); } catch { se.scrollTop = 0; }
  }

  // 2) Известные контейнеры
  const known = [mainContainer, document.querySelector('main.account')].filter(Boolean);
  known.forEach(el => {
    if (el && el.scrollTop > 0) {
      try { el.scrollTo({ top: 0, behavior: 'smooth' }); } catch { el.scrollTop = 0; }
    }
  });

  // 3) Любые прокручиваемые элементы на странице (overflow:auto/scroll)
  const all = Array.from(document.querySelectorAll('*'));
  all.forEach(el => {
    try {
      const cs = getComputedStyle(el);
      const oy = cs.overflowY;
      if ((oy === 'auto' || oy === 'scroll') && el.scrollHeight > el.clientHeight && el.scrollTop > 0) {
        try { el.scrollTo({ top: 0, behavior: 'smooth' }); } catch { el.scrollTop = 0; }
      }
    } catch {}
  });
};

onMounted(() => {
  mainContainer = document.querySelector('main.account');
  window.addEventListener('scroll', handleScroll);
  if (mainContainer) {
    mainContainer.addEventListener('scroll', handleScroll);
  }
  // Инициализация видимости при загрузке
  updateVisibility();
  // Всегда показываем кнопку по требованию
  showScrollToTop.value = true;
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
  if (mainContainer) {
    mainContainer.removeEventListener('scroll', handleScroll);
    mainContainer = null;
  }
});
</script>

<template>
<footer>
  <div class="container">
    <div class="footer_items">
      <div class="footer_item">
        <a href="/" class="footer_item_logo"><img src="/assets/img/logo.svg" alt="" style="width: 100px;"></a>
        <p class="footer_item_info">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed </p>
      </div>
      <div class="footer_item">
        <div class="footer_item_navigations">
          <ul class="footer_item_navigation">
            <li class="footer_item_navigation_item"><a href="/about">О сайте</a></li>
            <li class="footer_item_navigation_item"><a href="/instructions">Инструкция по работе с сайтом</a></li>
            <li class="footer_item_navigation_item"><a href="/contacts">Контакты</a></li>
          </ul>
          <ul class="footer_item_navigation">
            <li class="footer_item_navigation_item"><a href="/social">Социальные сети</a></li>
            <li class="footer_item_navigation_item"><a href="/banks">Банк ФИПИ</a></li>
            <li class="footer_item_navigation_item"><a href="/policy">Политика конфидециальности</a></li>
          </ul>
        </div>
      </div>
      <div class="footer_item">
        <div class="footer_item_socials">
          <a href="#!" class="footer_item_social"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 35 35" fill="none">
  <path fill-rule="evenodd" clip-rule="evenodd" d="M5.39323 0H29.6068C32.5853 0 35 2.41465 35 5.39323V29.6068C35 32.5853 32.5853 35 29.6068 35H5.39323C2.41465 35 0 32.5853 0 29.6068V5.39323C0 2.41465 2.41465 0 5.39323 0Z" fill="#8F70FF"></path>
  <path d="M21.3086 18C21.3086 19.8244 19.8244 21.3086 18 21.3086C16.1756 21.3086 14.6914 19.8244 14.6914 18C14.6914 16.1756 16.1756 14.6914 18 14.6914C19.8244 14.6914 21.3086 16.1756 21.3086 18ZM18 7C11.9343 7 7 11.9343 7 18C7 24.0657 11.9343 29 18 29C20.222 29 22.3646 28.3384 24.1961 27.0868L24.2276 27.0648L22.7457 25.3425L22.7206 25.3582C21.311 26.2657 19.6783 26.745 18 26.745C13.1781 26.745 9.255 22.8219 9.255 18C9.255 13.1781 13.1781 9.255 18 9.255C22.8219 9.255 26.745 13.1781 26.745 18C26.745 18.6246 26.6751 19.2571 26.5391 19.8794C26.2626 21.0148 25.4674 21.3621 24.8711 21.3165C24.2708 21.2678 23.5684 20.8404 23.5636 19.7938V18.9963V18C23.5636 14.9318 21.0682 12.4364 18 12.4364C14.9318 12.4364 12.4364 14.9318 12.4364 18C12.4364 21.0682 14.9318 23.5636 18 23.5636C19.4905 23.5636 20.8883 22.9814 21.9419 21.9215C22.5548 22.8754 23.5534 23.4733 24.6904 23.5644C24.7878 23.5723 24.8876 23.5762 24.9858 23.5762C25.7864 23.5762 26.5792 23.3083 27.2188 22.8235C27.878 22.3222 28.3706 21.5986 28.6425 20.7288C28.6857 20.5881 28.7659 20.2668 28.7659 20.2644L28.7682 20.2526C28.9285 19.5549 29 18.8596 29 18C29 11.9343 24.0657 7 18 7Z" fill="white"></path>
</svg></a>
 <a href="#!" class="footer_item_social"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 35 35" fill="none">
  <path fill-rule="evenodd" clip-rule="evenodd" d="M5.39323 0H29.6068C32.5853 0 35 2.41465 35 5.39323V29.6068C35 32.5853 32.5853 35 29.6068 35H5.39323C2.41465 35 0 32.5853 0 29.6068V5.39323C0 2.41465 2.41465 0 5.39323 0Z" fill="#8F70FF"></path>
  <path d="M17.5862 7.30371C14.6676 7.30371 12.3018 9.66968 12.3018 12.5881C12.3018 15.5067 14.6676 17.8728 17.5862 17.8728C20.5047 17.8728 22.8706 15.5067 22.8706 12.5881C22.8706 9.66968 20.5047 7.30371 17.5862 7.30371ZM17.5862 14.7728C16.3798 14.7728 15.4017 13.7947 15.4017 12.5882C15.4017 11.3818 16.3798 10.4038 17.5862 10.4038C18.7926 10.4038 19.7707 11.3818 19.7707 12.5882C19.7707 13.7947 18.7926 14.7728 17.5862 14.7728Z" fill="white"></path>
  <path d="M19.4518 22.0777C21.5674 21.6468 22.8351 20.6449 22.9022 20.5911C23.5213 20.0947 23.6207 19.1903 23.1242 18.5712C22.6278 17.9522 21.7236 17.8527 21.1044 18.3491C21.0913 18.3597 19.7392 19.3969 17.5144 19.3985C15.2897 19.3969 13.9089 18.3597 13.8959 18.3491C13.2767 17.8527 12.3724 17.9522 11.876 18.5712C11.3796 19.1903 11.479 20.0947 12.0981 20.5911C12.166 20.6456 13.4861 21.6736 15.661 22.0944L12.6299 25.2622C12.0791 25.8334 12.0956 26.743 12.6669 27.2939C12.9457 27.5627 13.3051 27.6964 13.6641 27.6964C14.0406 27.6964 14.4167 27.5493 14.6986 27.2568L17.5145 24.2572L20.6147 27.2763C21.1765 27.8371 22.0861 27.8362 22.6467 27.2747C23.2074 26.7131 23.2067 25.8033 22.6452 25.2427L19.4518 22.0777Z" fill="white"></path>
</svg></a>
 <a href="#!" class="footer_item_social"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 35 35" fill="none">
  <path fill-rule="evenodd" clip-rule="evenodd" d="M5.39323 0H29.6068C32.5853 0 35 2.41465 35 5.39323V29.6068C35 32.5853 32.5853 35 29.6068 35H5.39323C2.41465 35 0 32.5853 0 29.6068V5.39323C0 2.41465 2.41465 0 5.39323 0Z" fill="#8F70FF"></path>
  <path fill-rule="evenodd" clip-rule="evenodd" d="M16.7425 24.9413H18.177C18.177 24.9413 18.6102 24.8925 18.8317 24.6488C19.0353 24.4248 19.0288 24.0045 19.0288 24.0045C19.0288 24.0045 19.0008 22.0363 19.894 21.7465C20.7748 21.4608 21.9057 23.6486 23.1042 24.4899C24.0106 25.1264 24.6994 24.9871 24.6994 24.9871L27.9045 24.9413C27.9045 24.9413 29.5811 24.8355 28.7861 23.4876C28.721 23.3775 28.3229 22.4906 26.4029 20.6683C24.393 18.761 24.6625 19.0696 27.0833 15.7704C28.5576 13.7612 29.1469 12.5346 28.9628 12.0093C28.7873 11.5088 27.7029 11.641 27.7029 11.641L24.0942 11.6638C24.0942 11.6638 23.8265 11.6266 23.6282 11.7479C23.4342 11.8665 23.3098 12.1438 23.3098 12.1438C23.3098 12.1438 22.7384 13.6984 21.9769 15.0208C20.37 17.8108 19.7274 17.9585 19.4647 17.7849C18.8536 17.3811 19.0063 16.163 19.0063 15.2974C19.0063 12.5935 19.4074 11.4662 18.2253 11.1744C17.8331 11.0776 17.5441 11.0136 16.5409 11.0031C15.2532 10.9897 14.1635 11.0072 13.5464 11.3163C13.1359 11.5218 12.8191 11.9798 13.0122 12.0062C13.2507 12.0387 13.7908 12.1553 14.0771 12.5536C14.447 13.0682 14.4341 14.2234 14.4341 14.2234C14.4341 14.2234 14.6466 17.4063 13.9378 17.8015C13.4514 18.0727 12.7841 17.5191 11.3515 14.9879C10.6176 13.6913 10.0633 12.258 10.0633 12.258C10.0633 12.258 9.95654 11.9902 9.76589 11.8468C9.53466 11.6731 9.21157 11.6181 9.21157 11.6181L5.78225 11.641C5.78225 11.641 5.26757 11.6557 5.07843 11.8846C4.91017 12.0884 5.065 12.5093 5.065 12.5093C5.065 12.5093 7.7496 18.9317 10.7897 22.1683C13.5775 25.1361 16.7425 24.9413 16.7425 24.9413Z" fill="white"></path>
</svg></a>
        </div>
      </div>
    </div>
  </div>

  <!-- Кнопка скролла наверх -->
  <transition name="scroll-to-top">
    <button
      v-if="showScrollToTop"
      @click="scrollToTop"
      class="scroll-to-top-btn"
      aria-label="Прокрутить наверх"
    >
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
        <path d="M12 4L12 20" stroke="white" stroke-width="2" stroke-linecap="round"/>
        <path d="M6 10L12 4L18 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
  </transition>
</footer>
</template>

<style scoped>
.scroll-to-top-btn {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 50px;
  height: 50px;
  background: #8F70FF;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(143, 112, 255, 0.3);
  z-index: 1000;
  transition: all 0.3s ease;
}

.scroll-to-top-btn:hover {
  background: #7A5CE8;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(143, 112, 255, 0.4);
}

.scroll-to-top-btn:active {
  transform: translateY(0);
}

/* Vue transition стили */
.scroll-to-top-enter-active,
.scroll-to-top-leave-active {
  transition: all 0.3s ease;
}

.scroll-to-top-enter-from,
.scroll-to-top-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.8);
}

/* Мобильные стили */
@media (max-width: 768px) {
  .scroll-to-top-btn {
    bottom: 20px;
    right: 20px;
    width: 45px;
    height: 45px;
  }
}
</style>