import axios from 'axios';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

interface Resource {
    id: number;
    [key: string]: any
}

function data<T extends Resource>(resourceRoute: string) {
    const data = ref<T[]>([]);
    axios.get(route(`${resourceRoute}.index`))
        .then((response) => data.value = response.data as T[]);
    return data;
}

export function resource<T extends Resource>(resourceRoute: string, options = {}) {
    return {
        store(resource: T) {
            router.post(route(`${resourceRoute}.store`), resource, options)
        },
        update(resource: T) {
           router.put(route(`${resourceRoute}.update`, resource.id), resource, options)
        },
        destroy(resource: T) {
           router.delete(route(`${resourceRoute}.destroy`, resource.id), options)
        }
    }
}