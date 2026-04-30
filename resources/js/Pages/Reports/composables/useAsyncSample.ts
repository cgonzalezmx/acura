import axios from "axios";
import { ref } from "vue";

export function useAsyncSample(sampleId: number) {
    const analyses = ref<any[] | null>();
    const error = ref();
    const loading = ref(true);

    axios.get<any[]>(route(''))
        .then(({data}) => {
            analyses.value = data;
            loading.value = false;
        })
        .catch((e) => {
            error.value = e;
        });
}
