<script setup lang="ts">
import { Field, ResourceConfig } from './types';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import IftaLabel from 'primevue/iftalabel';
import { useForm } from '@inertiajs/vue3';

interface Props {
    registry?: {
        id: number | undefined;
        [key: string]: any;
    };
    fields: Field[];
    resource: ResourceConfig;
}

const props = defineProps<Props>();
const emit = defineEmits(['success']);
const form = useForm(props.registry ?? { id: undefined });

function submit() {
    const action = form.id ? 'update' : 'create';
    const resourceRoute = route(`${props.resource.route}.${action}`, form.id);
    if (form.id) {
        form.put(resourceRoute, { only: props.resource.only });
        return;
    }

    form.post(resourceRoute, { only: props.resource.only });
}
</script>

<template>
    <form @submit.prevent="submit">
        <IftaLabel v-for="field in fields">
            <label :for="field.labelIdentifier">{{ field.label }}</label>
            <InputText
                v-if="field.type === 'string'"
                v-model="form[field.item]"
                :id="field.labelIdentifier"
                class="w-full"/>
            <Textarea
                v-else
                v-model="form[field.item]"
                rows="3"
                :id="field.labelIdentifier"
                class="w-full resize-none"/>
        </IftaLabel>
    </form>
</template>