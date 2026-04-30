import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useQuoteDataStore = defineStore('additional-quote-data', () => {
    const sampleDeliveredByClient = ref(false);
    const clientDataAsSamplingSite = ref(false);
    const objective = ref('');
    const notes = ref('');
    const validity = ref('30 días');
    const paymentMethod = ref('');
    const priceAdjustmentNotes = ref('');

    function sync(data: Record<string, any>) {
        sampleDeliveredByClient.value = data.sample_delivered_by_client;
        clientDataAsSamplingSite.value = Boolean(data.client_data_as_sampling_site);
        objective.value = data.objective;
        notes.value = data.notes;
        validity.value = data.validity;
        paymentMethod.value = data.payment_method;
        priceAdjustmentNotes.value = data.price_adjustment_notes
    }

    return {
        sampleDeliveredByClient,
        clientDataAsSamplingSite,
        objective,
        notes,
        validity,
        paymentMethod,
        priceAdjustmentNotes,
        sync
    };
});