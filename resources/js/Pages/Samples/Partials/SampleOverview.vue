<script setup lang="ts">
import IftaLabel from 'primevue/iftalabel';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import { ref } from 'vue';
import Select from 'primevue/select';
import Fieldset from 'primevue/fieldset';
import DatePicker from 'primevue/datepicker';
import { SampleOverview, TPageProps } from '../types'
import Take from '../Classes/Take';
import Sample from '../Classes/Sample';
import SampleTakes from './SampleTakes.vue';
import { router, usePage } from '@inertiajs/vue3';

interface Props {
    overview: SampleOverview;
    takes: Take[];
    isPreview: boolean;
}

const props = defineProps<Props>();
const samplers = usePage<TPageProps>().props.samplers;
const sample = ref(props.overview?.sample as Sample);
const totalTakes = props.overview.takes_count;
const takes = ref(props.takes);

function save() {
    const payload = {
        sample: {
            ...sample.value,
            takes: undefined,
            identifier: undefined
        },
        takes: takes.value
    };

    if (props.isPreview) {
        router.post(route('samples.store'), payload as any);
        return;
    }

    router.patch(route('samples.update', sample.value.id), payload as any);
}
</script>

<template>
    <div class="flex flex-col gap-4">
        <Fieldset legend="Cliente y lugar de muestreo">
            <div class="flex gap-20">
                <div class="grid grid-cols-[auto_auto] gap-2">
                    <div class="col-span-2"><b>Cliente</b></div>
                    <div>Nombre</div>
                    <div>{{ overview.client.name }}</div>
                    <div>Dirección</div>
                    <div>{{ overview.client.address }}</div>
                    <div>At'n</div>
                    <div>{{ overview.client.contact_name }}</div>
                    <div>Teléfono</div>
                    <div>{{ overview.client.contact_phone }}</div>
                    <div>Email</div>
                    <div>{{ overview.client.contact_email }}</div>
                </div>
                <div class="grid grid-cols-[auto_1fr] gap-2">
                    <div class="col-span-2"><b>Lugar de muestreo</b></div>
                    <div>Nombre</div>
                    <div>{{ overview.site.name }}</div>
                    <div>Dirección</div>
                    <div>{{ overview.site.address }}</div>
                    <div>At'n</div>
                    <div>{{ overview.site.contact_name }}</div>
                    <div>Teléfono</div>
                    <div>{{ overview.site.contact_phone }}</div>
                    <div>Email</div>
                    <div>{{ overview.site.contact_email }}</div>
                </div>
            </div>
        </Fieldset>
        <Fieldset legend="Información de la muestra">
            <div class="flex gap-4">
                <div class="grid grid-cols-[auto_1fr] w-1/2 gap-2">
                    <template v-if="!isPreview">
                        <div>Clave</div>
                        <div class="font-bold">{{ sample.identifier }}</div>
                    </template>
                    <div>Formato de muestreo</div>
                    <div>{{ overview.format.identifier }}</div>
                    <div class="self-center">Punto<span v-if="isPreview">(s)</span> de muestreo</div>
                    <div>
                        <Textarea v-model="sample.sampling_point" rows="3" class="resize-none"/>
                    </div>
                    <div class="self-center">Muestreador</div>
                    <div>
                        <Select
                            v-model="sample.sampled_by"
                            option-label="name"
                            option-value="id"
                            :options="samplers"
                            size="small"
                            fluid/>
                    </div>
                    <div class="self-center">Temperatura de recepción</div>
                    <div class="self-center">
                        <div v-if="overview.delivered_by_client">{{ sample.sample_temperature }}</div>
                        <InputNumber v-else v-model="sample.sample_temperature" size="small" fluid/>
                    </div>
                    <div class="self-center">Número de contenedores</div>
                    <div class="self-center">
                        <div v-if="overview.delivered_by_client">{{ sample.total_containers }}</div>
                        <InputNumber v-else v-model="sample.total_containers" size="small" fluid/>
                    </div>
                </div>
                <div class="grid grid-cols-[auto_1fr] w-1/2 gap-2">
                    <div class="self-center">Fecha de recepción</div>
                    <DatePicker
                        v-model="sample.reception_date"
                        date-format="dd/mm/yy"
                        show-time
                        hour-format="24"
                        size="small"
                        class="self-center"/>
                    <div>Prioridad</div>
                    <div v-if="overview.is_urgent" class="text-red-500 font-bold">URGENTE</div>
                    <div v-else>Normal</div>
                    <div>Tipo de muestra</div>
                    <div>{{ overview.sample_type }}</div>
                    <div>Matriz</div>
                    <div>{{ overview.matrix }}</div>
                    <div>Refrigerador</div>
                    <div>{{ sample.refrigerator }}</div>
                </div>
            </div>
        </Fieldset>
        <Fieldset legend="Tomas de la muestra">
            <SampleTakes v-model="takes" :total-takes/>
        </Fieldset>
        <IftaLabel>
            <Textarea v-model="sample.observation" rows="5" fluid class="resize-none"/>
            <label>Observaciones</label>
        </IftaLabel>
    </div>
    <div class="flex justify-center">
        <Button icon="fa-solid fa-floppy-disk" label="Guardar" @click="save"/>
    </div>
</template>