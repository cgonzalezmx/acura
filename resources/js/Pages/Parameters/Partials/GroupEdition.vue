<script setup lang="ts">
import IftaLabel from 'primevue/iftalabel';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import { inject, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';

interface Props {
    current?: any;
}

const props = defineProps<Props>();

const containers = inject<any[]>('containers');
const preservers = inject<any[]>('preservers');
const labelColors = inject<any[]>('labelColors');
const form = useForm({
    name: props.current?.name ?? '',
    sample_container_id: props.current?.sample_container_id ?? 0,
    sample_preserver_id: props.current?.sample_preserver_id ?? 1,
    label_color_id: props.current?.label_color_id ?? 0,
    required_sample_volume: props.current?.required_sample_volume ?? ''
});
const selectors = [
    {
        options: containers,
        label: 'Recipiente',
        optionsLabel: 'name',
        formField: 'sample_container_id'
    },
    {
        options: preservers,
        label: 'Contenedor',
        optionsLabel: 'name',
        formField: 'sample_preserver_id'
    },
    {
        options: labelColors,
        label: 'Etiqueta',
        optionsLabel: 'color',
        formField: 'label_color_id'
    }
]

function submit() {
    const only = ['groups'];
    if (props.current) {
        form.patch(route('parameter-groups.update', props.current.id), { only });
        return;
    }

    form.post(route('parameter-groups.store'), { only });
}

onMounted(() => {
    console.log(props.current)
})
</script>

<template>
    <form @submit.prevent="submit" class="grid gap-3">
        <IftaLabel>
            <InputText v-model="form.name" fluid/>
            <label>Nombre</label>
        </IftaLabel>
        <IftaLabel
            v-for="sel in selectors" :key="sel.label">
            <Select
                v-model="form[sel.formField as keyof {}]"
                :options="sel.options"
                :option-label="sel.optionsLabel"
                option-value="id"
                fluid/>
            <label>{{ sel.label }}</label>
        </IftaLabel>
        <IftaLabel>
            <InputText v-model="form.required_sample_volume" fluid/>
            <label>Volumen grupal</label>
        </IftaLabel>
        <IftaLabel>
            <Textarea rows="3" fluid class="resize-none"/>
            <label>Descripción</label>
        </IftaLabel>
        <Button type="submit" label="Guardar" icon="fa-solid fa-floppy-disk" />
    </form>
</template>