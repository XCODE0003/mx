<script setup>
import { computed } from 'vue';
import katex from 'katex';

const props = defineProps({
  content: { type: String, required: true }
});

const rendered = computed(() => {
  let html = props.content ?? '';

  // Блочные $$...$$
  html = html.replace(/\$\$([\s\S]*?)\$\$/g, (_, f) => {
    try { return katex.renderToString(f, { displayMode: true }); }
    catch { return _; }
  });

  // Inline $...$
  html = html.replace(/\$([^$]+)\$/g, (_, f) => {
    try { return katex.renderToString(f, { displayMode: false }); }
    catch { return _; }
  });

  return html;
});
</script>

<template>
  <span v-html="rendered"></span>
</template>
