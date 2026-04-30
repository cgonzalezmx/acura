import axios from 'axios';
import { shallowRef } from 'vue';

const samplerList = shallowRef([]);
let cached = false;

export function useSamplerList() {
    if (!cached) {
        axios.get(route('samplers'))
            .then((response) => {
                samplerList.value = response.data;
                cached = true;
            });
    }

    return {
        samplerList
    }
}