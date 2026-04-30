<script setup lang="ts">
import Menu from 'primevue/menu';
import Button from 'primevue/button';
import { MenuItem } from 'primevue/menuitem';
import { useTemplateRef } from 'vue';

interface Props {
    model: MenuItem[];
    icon?: string;
    severity?: 'secondary';
    outlined?: boolean;
}

const { icon = 'fa-solid fa-ellipsis-vertical' } = defineProps<Props>();
const menu = useTemplateRef('menu');
const togglePopupMenu = (event: Event) => menu.value?.toggle(event);
</script>

<template>
    <Button :icon @click="togglePopupMenu" :outlined :severity/>
    <Menu :model ref="menu" popup/>
</template>