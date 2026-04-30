import axios from 'axios';
import { ref } from 'vue';
import SampleOverview from '../Classes/SampleOverview';
import { Sample } from '@/types/sample';
import Take from '../Classes/Take';

export function usePreview() {
    const query = ref('');
    const preview =  ref<SampleOverview | null>(null);

    function load(sample: Sample | null = null) {
        if (!query.value) {
            return;
        }

        if (sample) {
            const takes = sample.takes.map((t) => new Take(t));
            sample.takes = takes;
        }

        axios.get(route('sampling-formats.details', query.value))
            .then((response) => {
                preview.value = new SampleOverview({
                    ...response.data,
                    sample: sample ?? null
                });
            });
    }

    function clear() {
        query.value = '';
        preview.value = null;
    }

    return {
        query,
        preview,
        load,
        clear
    }
}