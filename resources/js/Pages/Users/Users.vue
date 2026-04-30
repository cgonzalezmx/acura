<script setup lang="ts">
import HomeLayout from '@/Layouts/HomeLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import PopupMenu from '@/Components/PopupMenu.vue';
import UserEdition from './Partials/UserEdition.vue';
import Dialog from 'primevue/dialog';
import { router } from '@inertiajs/vue3';
import { inject, onMounted, ref } from 'vue';
import { useToggle } from '@vueuse/core';

interface Props {
    activeUsers: User[];
}

defineOptions({ layout: HomeLayout });
const props = defineProps<Props>();
const [ showUserEdition, toggleUserEdition ] = useToggle()
const currentUser = ref<User>();
const activeRoles = inject<Role[]>('activeRoles', []);
const roleMap = new Map(activeRoles?.map((role) => [role.id, role]));

function createUser() {
    currentUser.value = undefined;
    toggleUserEdition();
}

function editUser(data: User) {
    toggleUserEdition();
    currentUser.value = data;
}

function assignPermissions(id: number) {
}

function deleteUser(id: number) {
    router.delete(route('users.destroy', id));
}

function listRoles(roles: any[]) {
    return roles
        .map(({label}) => label)
        .join(', ');
}

function onSuccess() {
    toggleUserEdition();
    currentUser.value = undefined;
}

onMounted(() => {
    console.log(props.activeUsers);
})
</script>

<template>
    <DataTable :value="activeUsers">
        <template #header>
            <Button label="Nuevo" @click="createUser()"/>
        </template>
        <Column header="nombre" field="name"/>
        <Column header="Alias" field="alias"/>
        <Column header="Roles">
            <template #body="{ data }">
                {{ listRoles(data.roles) }}
            </template>
        </Column>
        <Column>
            <template #body="{ data }">
                <PopupMenu
                    outlined
                    severity="secondary"
                    :model="[
                        {
                            label: 'Editar',
                            icon: 'fa-solid fa-pen',
                            command: () => editUser(data)
                        },
                        {
                            label: 'Permisos',
                            icon: 'fa-solid fa-shield',
                            command: () => assignPermissions(data.id)
                        },
                        {
                            label: 'Borrar',
                            icon: 'fa-regular fa-trash-can',
                            command: () => deleteUser(data.id)
                        }
                    ]"
                />
            </template>
        </Column>
    </DataTable>
    <Dialog
        v-model:visible="showUserEdition"
        modal
        :draggable="false">
        <UserEdition :user="currentUser" @success="onSuccess()"/>
    </Dialog>
</template>