<script setup lang="ts">
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import IftaLabel from 'primevue/iftalabel';
import Card from 'primevue/card';
import { useForm, Head } from '@inertiajs/vue3';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';
import Message from 'primevue/message';
import { computed, h } from 'vue';

type Form = {
    alias: string;
    password: string;
    remember: boolean;
}

interface ErrorMessageProps {
    text?: string;
}

interface LoginErrors {
    alias?: string;
    password?: string;
    auth?: string;
}

const { errors } = defineProps<{ errors: LoginErrors }>()

const form = useForm<Form>({
    alias: '',
    password: '',
    remember: false,
});

const isInvalidAlias = computed(() => typeof form.errors.alias !== 'undefined');
const isInvalidPassword = computed(() => typeof form.errors.password !== 'undefined');

function ErrorMessage(props: ErrorMessageProps) {
    if (!props.text) return;

    return h(
        Message,
        {
            severity: 'error',
            variant: 'simple',
            size: 'small'
        },
        {
            default: () => props.text
        }
    );
}

function submit() {
    form.post(route('login'), {
        onFinish: () => form.reset('password')
    });
}
</script>

<template>

    <Head title="Inicio de sesión" />
    <div class="flex flex-col bg-[url(/images/login-bg.avif)] bg-cover bg-center min-h-screen justify-center">
        <Card class="bg-white self-center w-md shadow-lg">
            <template #content>
                <form @submit.prevent="submit">
                    <div class="p-4 flex flex-col gap-6">
                        <div>
                            <IftaLabel>
                                <InputText v-model="form.alias" fluid :invalid="isInvalidAlias" />
                                <label>Usuario</label>
                            </IftaLabel>
                            <ErrorMessage :text="errors.alias" />
                        </div>

                        <div>
                            <IftaLabel>
                                <Password v-model="form.password" toggle-mask fluid :feedback="false"
                                    :invalid="isInvalidPassword" />
                                <label>Contraseña</label>
                            </IftaLabel>
                            <ErrorMessage :text="errors.password" />
                        </div>

                        <ErrorMessage :text="errors.auth" />


                        <div>
                            <Checkbox v-model="form.remember" binary />
                            <span class="pl-2">Recordar sesión</span>
                        </div>
                        <div>
                            <Button type="submit" label="Iniciar sesión" />
                        </div>
                    </div>
                </form>
            </template>
        </Card>
    </div>
</template>
