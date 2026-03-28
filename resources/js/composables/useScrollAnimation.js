import { onMounted, onUnmounted } from 'vue';

export function useScrollAnimation() {
    let observer = null;

    const initScrollAnimation = () => {
        const options = {
            root: null,
            rootMargin: '0px 0px -100px 0px',
            threshold: 0.1
        };

        observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, options);

        // Наблюдаем за элементами с классом .scroll-animate
        const elements = document.querySelectorAll('.scroll-animate');
        elements.forEach(el => {
            observer.observe(el);
        });
    };

    onMounted(() => {
        setTimeout(initScrollAnimation, 100);
    });

    onUnmounted(() => {
        if (observer) {
            observer.disconnect();
        }
    });

    return {
        initScrollAnimation
    };
}
