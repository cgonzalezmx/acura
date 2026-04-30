import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { Expense } from '../types';
import { roundNumber } from '@/utils/number';
import { useEntryStore } from './useEntryStore';
import { storeToRefs } from 'pinia';

export const useCostStore = defineStore('quote-costs', () => {
    const { entriesTotal } = storeToRefs(useEntryStore());
    const expenses = ref<Expense[]>([]);
    const totalExpenses = computed(() => {
        return expenses.value.reduce((sum, {quantity, cost}) => sum + (quantity > 0 ? cost * quantity : 0), 0) * globalExpensesQuantity.value
    });
    const grossCost = computed(() => {
        let value = entriesTotal.value;
        value += totalExpenses.value;
        return roundNumber(value, { decimals: 2 });
    });
    const priceAdjustment = ref({
        total: 0,
        type: 'none' as 'none' | 'discount' | 'charge',
        hasPercentage: false,
        percentage: 0,
        notes: ''
    });
    const subtotal = computed(() => {
        const { total: adjustment, type } = priceAdjustment.value;
        let value = grossCost.value;

        if (type && type !== 'none') {
            value += adjustment * (type === 'discount' ? -1 : 1);
        }

        return roundNumber(value, { decimals: 2 });
    });
    const iva = computed(() => roundNumber(subtotal.value * 0.16, { decimals: 2 }));
    const netCost = computed(() => subtotal.value + iva.value);

    const computedPriceAdjustment = computed(() => {
        const { percentage, hasPercentage } = priceAdjustment.value;

        if (!hasPercentage) return 0;

        if (percentage > 0) {
            priceAdjustment.value.total = 0;
            priceAdjustment.value.total = subtotal.value * (percentage/100);
        }
        else {
            priceAdjustment.value.total = 0;
        }

        return priceAdjustment.value.total;
    });

    const hasPriceAdjustment = computed(() => priceAdjustment.value.type && priceAdjustment.value.type !== 'none');
    const priceAdjustmentLabel = computed(() => {
        return ({ discount: 'Descuento', charge: 'Cargo' } as Record<string, string>)[priceAdjustment.value.type];
    });

    const paymentMethod = ref('');
    const globalExpensesConcept = ref('Muestreo y viáticos');
    const globalExpensesQuantity = ref(0);

    function addExpense(expense: Expense) {
        expenses.value.push(expense);
    }

    function removeExpense(index: number) {
        expenses.value.splice(index, 1);
    }

    function addEmptyExpense() {
        expenses.value.push({ id: null, concept: '', cost: 0, quantity: 0 });
    }

    return {
        grossCost,
        priceAdjustment,
        computedPriceAdjustment,
        iva,
        expenses,
        totalExpenses,
        netCost,
        subtotal,
        hasPriceAdjustment,
        priceAdjustmentLabel,
        paymentMethod,
        globalExpensesConcept,
        globalExpensesQuantity,
        addExpense,
        removeExpense,
        addEmptyExpense
    }
});