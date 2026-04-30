import { ref } from 'vue';

export function useErrorMessages() {
    const entries = ref<Record<string, number[]>>({});
    const reports = ref<Record<string, number[]>>({});
}
