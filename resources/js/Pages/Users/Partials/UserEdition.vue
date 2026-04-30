<script setup lang="ts">
import InputText from 'primevue/inputtext';
import IftaLabel from 'primevue/iftalabel';
import Password from 'primevue/password';
import Button from 'primevue/button';
import PickList from 'primevue/picklist';
import FileUpload, { FileUploadSelectEvent } from 'primevue/fileupload';
import { router, useForm } from '@inertiajs/vue3';
import { inject, ref } from 'vue';

interface Props {
    user?: User;
}

type Form = {
    name: string;
    alias: string;
    roles: number[];
    signature: any;
    password: string;
    password_confirmation: string;
}

const { user } = defineProps<Props>();
const emit = defineEmits(['submit', 'success']);
const isNewUser = typeof user === 'undefined';
const activeRoles = inject<Role[]>('activeRoles', []);
const userRolesMap: Record<number, boolean> = {};
user?.roles?.forEach((role) => userRolesMap[role.id] = true);
const rolePickList = ref([[] as Role[], [] as Role[]]);
activeRoles?.forEach((role) => {
    const [availableRoles, currentUserRoles] = rolePickList.value;

    if (userRolesMap[role.id]) {
        currentUserRoles.push(role);
        return;
    }

    availableRoles.push(role);
});
const form = useForm<Form>({
    name: user?.name ?? '',
    alias: user?.alias ?? '',
    roles: [],
    password: '',
    password_confirmation: '',
    signature: null
});

function selectSignature(event: FileUploadSelectEvent) {
    console.log(event.files)
    form.signature = event.files[0];
    console.log(form)
}

function submit() {
    const submitOptions = {
        only: ['activeUsers'],
        onSuccess: () => emit('success')
    };

    form.roles = rolePickList.value.at(1)?.map((role) => role.id) ?? [];

    if (isNewUser) {
        form.post(route('users.store'), submitOptions);
        return;
    }
    
    router.post(route('users.update', user.id), {
        _method: 'patch',
        name: form.name,
        alias: form.alias,
        roles: form.roles,
        signature: form.signature
    }, submitOptions);
}
</script>

<template>
    <form @submit.prevent="submit">
        <IftaLabel class="mb-4">
            <InputText v-model="form.name"/>
            <label>Nombre</label>
        </IftaLabel>
        <IftaLabel class="mb-4">
            <InputText v-model="form.alias"/>
            <label>Alias</label>
        </IftaLabel>
        <template v-if="isNewUser">
            <IftaLabel class="mb-4"> 
                <Password v-model="form.password"/>
                <label>Contraseña</label>
            </IftaLabel>
            <IftaLabel class="mb-4">
                <Password v-model="form.password_confirmation" :feedback="false"/>
                <label>Confirmación de contraseña</label>
            </IftaLabel>
        </template>
        <PickList
            v-model="rolePickList"
            data-key="id"
            class="mb-4"
            :pt="{
                sourceControls: 'hidden',
                targetControls: 'hidden'
            }">
            <template #option="{ option }">{{ option.label }}</template>
            <template #sourceheader><b>Roles disponibles</b></template>
            <template #targetheader><b>Roles de usuario</b></template>
        </PickList>
        <img :src="user?.signature_url" alt="">
        <FileUpload mode="basic" @select="selectSignature" choose-label="Cargar firma"/>
        <Button label="Guardar" icon="fa-solid fa-floppy-disk" type="submit" :disabled="form.processing"/>
    </form>
</template>