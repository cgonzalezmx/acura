import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useClientDataStore = defineStore('client-data', () => {
    const id = ref<number | null>(null);
    const name = ref('');
    const industry_sector = ref('');
    const neighborhood = ref('');
    const zip_code = ref('');
    const city = ref('');
    const state = ref('');
    const address = ref('');
    const version = ref(0);
    const client_id = ref(0);

    function sync(data: any) {
        id.value = data.id;
        name.value = data.name;
        industry_sector.value = data.industry_sector;
        neighborhood.value = data.neighborhood;
        zip_code.value = data.zip_code;
        city.value = data.city;
        state.value = data.state;
        address.value = data.address;
        version.value = data.version;
        client_id.value = data.client_id;
    }

    return {
        id,
        name,
        industry_sector,
        neighborhood,
        zip_code,
        city,
        state,
        address,
        version,
        client_id,
        sync
    };
});