<script setup lang="ts">
import AutoComplete, { AutoCompleteCompleteEvent } from 'primevue/autocomplete';
import axios from 'axios';
import { ref } from 'vue';

interface Props {
    url: string;
}

const props = defineProps<Props>();
const model = defineModel({ required: true });
const suggestions = ref();

async function search(event: AutoCompleteCompleteEvent) {
    const response = await axios.get(props.url, { params: { term: event.query } });
    suggestions.value = response.data;
}
</script>

<template>
    <AutoComplete v-model="model" :suggestions @complete="search" size="small"/>
</template>
