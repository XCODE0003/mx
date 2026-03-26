<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: '' },
    /** Классы инпута (например signin_main_rect_input или profile_account_up_item_input) */
    inputClass: { type: String, default: '' },
    autocomplete: { type: String, default: 'current-password' },
    /** id для связки label/accessibility */
    inputId: { type: String, default: undefined },
});

const emit = defineEmits(['update:modelValue']);

const visible = ref(false);

const inputType = computed(() => (visible.value ? 'text' : 'password'));
const toggleLabel = computed(() =>
    visible.value ? 'Скрыть пароль' : 'Показать пароль',
);
</script>

<template>
    <div class="password-input-wrap">
        <input
            :id="inputId"
            :type="inputType"
            :value="modelValue"
            :placeholder="placeholder"
            :class="inputClass"
            :autocomplete="autocomplete"
            @input="emit('update:modelValue', $event.target.value)"
        />
        <button
            type="button"
            class="password-input-toggle"
            :aria-label="toggleLabel"
            :aria-pressed="visible"
            tabindex="0"
            @click="visible = !visible"
        >
            <!-- глаз: скрыт пароль -->
            <svg
                v-if="!visible"
                class="password-input-toggle-icon"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                />
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                />
            </svg>
            <!-- глаз скрыт: пароль виден -->
            <svg
                v-else
                class="password-input-toggle-icon"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"
                />
            </svg>
        </button>
    </div>
</template>

<style scoped>
.password-input-wrap {
    position: relative;
    width: 100%;
}

.password-input-wrap :deep(input) {
    width: 100%;
    padding-right: 44px;
    box-sizing: border-box;
}

.password-input-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 6px;
    border: none;
    background: transparent;
    color: rgba(57, 60, 91, 0.55);
    cursor: pointer;
    border-radius: 8px;
    transition: color 0.2s, background 0.2s;
}

.password-input-toggle:hover {
    color: #8f70ff;
    background: rgba(143, 112, 255, 0.08);
}

.password-input-toggle:focus-visible {
    outline: 2px solid #8f70ff;
    outline-offset: 2px;
}

.password-input-toggle-icon {
    width: 22px;
    height: 22px;
    flex-shrink: 0;
}
</style>
