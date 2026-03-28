<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';

const showScrollToTop = ref(true);
const currentYear = computed(() => new Date().getFullYear());
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

  try { window.scrollTo({ top: 0, behavior: 'smooth' }); } catch {}
  const se = document.scrollingElement || document.documentElement;
  if (se) {
    try { se.scrollTo({ top: 0, behavior: 'smooth' }); } catch { se.scrollTop = 0; }
  }


  const known = [mainContainer, document.querySelector('main.account')].filter(Boolean);
  known.forEach(el => {
    if (el && el.scrollTop > 0) {
      try { el.scrollTo({ top: 0, behavior: 'smooth' }); } catch { el.scrollTop = 0; }
    }
  });


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

  updateVisibility();

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
<footer class="site-footer">
  <div class="container">
    <div class="footer_items">
      <div class="footer_col footer_col--brand">
        <Link href="/" class="footer_item_logo" aria-label="На главную">
          <img src="/assets/img/logo.svg" alt="" width="100">
        </Link>
      </div>
      <nav class="footer_blocks" aria-label="Нижнее меню">
        <div class="footer_block">
          <p class="footer_block_title">О проекте</p>
          <ul class="footer_block_list">
            <li><Link href="/about" class="footer_block_link">О сайте</Link></li>
            <li><Link href="/instructions" class="footer_block_link">Инструкция по работе с сайтом</Link></li>
            <li><Link href="/contacts" class="footer_block_link">Контакты</Link></li>
          </ul>
        </div>
        <div class="footer_block">
          <p class="footer_block_title">Сервис</p>
          <ul class="footer_block_list">
            <li><Link href="/banks" class="footer_block_link">Банк заданий</Link></li>
            <li><Link href="/policy" class="footer_block_link">Политика конфиденциальности</Link></li>
          </ul>
        </div>
      </nav>
    </div>

    <div class="footer_disclaimer" style="text-align: center; margin: auto;">
      <p style="text-align: center; margin: auto;">Сайт является информационным ресурсом и не относится к официальным источникам экзаменационных материалов. Задания формируются на основе данных из открытых источников, включая материалы Министерства образования Российской Федерации и ФИПИ. Официальные контрольные измерительные материалы, используемые при проведении государственной итоговой аттестации, на сайте не размещаются и не предоставляются.</p>
    </div>

    <div style="padding-bottom: 20px;" class="footer_copyright">
      <p>© kim365.ru {{ currentYear }}</p>
    </div>
  </div>

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

.scroll-to-top-enter-active,
.scroll-to-top-leave-active {
  transition: all 0.3s ease;
}

.scroll-to-top-enter-from,
.scroll-to-top-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.8);
}

@media (max-width: 768px) {
  .scroll-to-top-btn {
    bottom: 20px;
    right: 20px;
    width: 45px;
    height: 45px;
  }
}
</style>
