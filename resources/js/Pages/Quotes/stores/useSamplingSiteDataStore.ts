import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useSamplingSiteDataStore = defineStore('sampling-site-quote', () => {
    const id = ref<number | null>(null);
    const name = ref('');
    const industry_sector = ref('');
    const address = ref('');
    const neighborhood = ref('');
    const city = ref('');
    const state = ref('');
    const zip_code = ref('');
    const client_sampling_site_id = ref(0);
    const contact_name = ref<string | null>(null);
    const contact_phone = ref<string | null>(null);
    const contact_email = ref<string | null>(null);

    return {
        id,
        name,
        industry_sector,
        address,
        neighborhood,
        city,
        state,
        zip_code,
        contact_name,
        contact_phone,
        contact_email,
        client_sampling_site_id
    }
});
