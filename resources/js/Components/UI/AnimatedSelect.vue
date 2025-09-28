<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';

const props = defineProps({
    // Accepts either Array (['A','B'] or [{label, value}]) OR Object ({ key: label })
    options: { type: [Array, Object], default: () => [] },
    modelValue: { type: [String, Number, Boolean, Object, null], default: null },
    placeholder: { type: String, default: 'Выберите' },
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const root = ref(null);

const normalizedOptions = computed(() => {
    // Always return array of { value, label }
    if (Array.isArray(props.options)) {
        return props.options.map((o) => {
            if (o && typeof o === 'object') {
                // Support { key, value } shape → store key, show value
                if ('key' in o && 'value' in o) {
                    return { value: o.key, label: String(o.value) };
                }
                // Fallbacks for { value, label } or mixed shapes
                const normalizedValue = o.value ?? o.label ?? o.key;
                const normalizedLabel = o.label ?? o.value ?? String(o.key ?? '');
                return { value: normalizedValue, label: normalizedLabel };
            }
            return { value: o, label: String(o) };
        });
    }
    // Object map: { key: label }
    return Object.entries(props.options).map(([key, label]) => ({ value: key, label }));
});

const selectedLabel = computed(() => {
    const value = props.modelValue;
    if (value === null || value === undefined || value === '') {
        return props.placeholder;
    }
    const found = normalizedOptions.value.find((o) => o.value === value || String(o.value) === String(value));
    return found ? found.label : String(value);
});

const toggle = () => {
    isOpen.value = !isOpen.value;
};

const onSelect = (opt) => {
    emit('update:modelValue', opt.value);
    isOpen.value = false;
};

const onDocClick = (e) => {
    if (!root.value || root.value.contains(e.target)) return;
    isOpen.value = false;
};

// React to dynamic changes in options
watch(
    () => props.options,
    () => {
        // Close dropdown to avoid visual glitches when options update
        isOpen.value = false;
        // If current modelValue no longer exists in options, reset selection
        const hasCurrent = normalizedOptions.value.some((o) => String(o.value) === String(props.modelValue));
        if (!hasCurrent && props.modelValue !== null && props.modelValue !== undefined && props.modelValue !== '') {
            emit('update:modelValue', null);
        }
    },
    { deep: true }
);

onMounted(() => {
    document.addEventListener('click', onDocClick);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', onDocClick);
});
</script>

<template>
    <div class="home_create_setting_select">
        <div class="home_create_setting_select_content" :class="{ open: isOpen }" ref="root">
            <p class="home_create_setting_selected" @click="toggle">
                {{ selectedLabel }}
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="9" viewBox="0 0 15 9" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.4351 1.70711L8.07117 8.07107C7.68065 8.46159 7.04748 8.46159 6.65696 8.07107L0.292998 1.70711C-0.0975266 1.31658 -0.0975266 0.683417 0.292998 0.292893C0.683523 -0.0976317 1.31669 -0.0976317 1.70721 0.292893L7.36407 5.94975L13.0209 0.292893C13.4114 -0.0976312 14.0446 -0.0976311 14.4351 0.292893C14.8257 0.683418 14.8257 1.31658 14.4351 1.70711Z" fill="#393C5B" />
                </svg>
            </p>
            <div class="home_create_setting_select_rect" :class="{ open: isOpen }">
                <div class="home_create_setting_select_rect_content">
                    <p v-for="(opt, i) in normalizedOptions" :key="opt.value" class="home_create_setting_select_rect_text" @click="onSelect(opt)">
                        {{ opt.label }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.home_create_setting_select {
    position: relative;
}
.home_create_setting_selected svg {
    transition: transform 0.2s ease;
}
.home_create_setting_select_content.open .home_create_setting_selected svg {
    transform: rotate(180deg);
}
.home_create_setting_select_rect {
    display: block !important; /* override global display:none to allow animation */
    max-height: 0;
    opacity: 0;
    overflow: hidden;
    transition: max-height 0.25s ease, opacity 0.2s ease;
    z-index: 10;
}
.home_create_setting_select_rect.open {
    max-height: 220px;
    opacity: 1;
}
</style>

