import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useContactDataStore = defineStore('contact-quote', () => {
    const id = ref<number | null>(null);
    const name = ref('');
    const phone = ref('');
    const cellphone = ref('');
    const email = ref('');
    const client_contact_id = ref(0);

    return {
        id,
        name,
        phone,
        cellphone,
        email,
        client_contact_id
    }
});