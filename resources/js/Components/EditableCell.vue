<script setup lang="ts">
import { useToggle } from '@vueuse/core';
import { computed, nextTick, useTemplateRef } from 'vue';

const [inputVisble, toggleInput] = useToggle();
const model = defineModel({ required: true });
const inputRef = useTemplateRef('input');
const inputType = computed(() => {
    return typeof model.value === 'number' ? 'number' : 'text';
});

function focusInput() {
    toggleInput(true);
    nextTick(() => {
        inputRef.value?.focus();
    });
}

function handleBlur() {
    setTimeout(() => {
        toggleInput(false);
    }, 200);
}
</script>

<template>
    <div v-if="!inputVisble" class="w-full" @click="focusInput">
        {{ model ? model : '&nbsp;' }}
    </div>
    <input v-show="inputVisble" v-model="model" :type="inputType" @blur="handleBlur" class="w-full" ref="input">
</template>

<style scoped>

</style>
