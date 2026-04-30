<script setup lang="ts">
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';
import { useEntryStore } from '../stores/useEntryStore';
import InputNumber from 'primevue/inputnumber';
import { Entry, Expense } from '../types';
import vCurrency from '@/Directives/vCurrency';
import { useCostStore } from '../stores/useCostStore';
import { storeToRefs } from 'pinia';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import SelectButton from 'primevue/selectbutton';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import Checkbox from 'primevue/checkbox';
import { computed } from 'vue';
import Panel from 'primevue/panel';
import IftaLabel from 'primevue/iftalabel';
import Textarea from 'primevue/textarea';

const entryStore = useEntryStore();
const updateEntryPriceOffset = (entryId: string, price_offset: number) => entryStore.update(entryId, { price_offset });
const costStore = useCostStore();
const {
    grossCost,
    expenses,
    totalExpenses,
    iva,
    netCost,
    priceAdjustment,
    computedPriceAdjustment,
    subtotal,
    hasPriceAdjustment,
    priceAdjustmentLabel,
    globalExpensesQuantity
} = storeToRefs(costStore);
const { addExpense, removeExpense, addEmptyExpense } = costStore;
const expenseTotal = computed(() => expenses.value.reduce((sum, e) => sum + e.cost * e.quantity, 0));
const priceAdjusmentOptions = [
    { label: 'No aplica', value: 'none' },
    { label: 'Con descuento', value: 'discount' },
    { label: 'Con cargo', value: 'charge' }
];

function getTotalFrom(fieldName: keyof Entry) {
    const total = entryStore.all.reduce((sum, entry) => {
        const field = entry[fieldName];
        const value = typeof field === 'number' ? field : 0;

        return sum + value;
    }, 0);

    return total;
}

const getTotalFromExpense = (expense: Expense) => expense.cost * expense.quantity;
const totalAmount = (entry: Entry) => (entry.total_cost + entry.price_offset) * entry.quantity;

if (costStore.expenses.length === 0) {
    addExpense({ id: null, concept: 'Hotel', cost: 500, quantity: 0 });
    addExpense({ id: null, concept: 'Kilómetros', cost: 10, quantity: 0 });
}
</script>

<template>
    <Panel header="Resumen de partidas" class="mb-3">
        <table>
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="p-3 font-semibold text-left">Partida</th>
                    <th class="p-3 font-semibold text-left w-72">Concepto</th>
                    <th class="p-3 font-semibold text-left">Precio</th>
                    <th class="p-3 font-semibold text-left">Extras</th>
                    <th class="p-3 font-semibold text-left">Subtotal</th>
                    <th class="p-3 font-semibold text-left w-36">Ajuste</th>
                    <th class="p-3 font-semibold text-left w-60">Notas sobre el ajuste</th>
                    <th class="p-3 font-semibold text-left">Precio unitario</th>
                    <th class="p-3 font-semibold text-left w-28 max-w-28">Cantidad</th>
                    <th class="p-3 font-semibold text-left">Importe</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(entry, index) in entryStore.all"
                    :key="entry.entry_id"
                    class="border-b border-slate-200"
                    >
                    <td class="p-3">{{ index + 1 }}</td>
                    <td class="p-3">{{ entry.concept }}</td>
                    <td class="p-3" v-currency="entry.bundle_price"></td>
                    <td class="p-3" v-currency="entry.extras"></td>
                    <td class="p-3" v-currency="entry.total_cost"></td>
                    <td class="p-3">
                        <InputNumber
                            :model-value="entry.price_offset"
                            mode="currency"
                            currency="MXN"
                            locale="es-MX"
                            fluid
                            size="small"
                            @update:model-value="updateEntryPriceOffset(entry.entry_id, $event)"/>
                    </td>
                    <td class="p-3">
                        <Textarea
                            v-model="entry.price_offset_notes"
                            rows="3"
                            fluid
                            size="small"
                            class="resize-none text-sm"
                            />
                    </td>
                    <td v-currency="entry.total_cost + entry.price_offset" class="p-3"></td>
                    <td class="p-3 w-16 max-w-16">
                        <InputNumber v-model="entry.quantity" :min="1" show-buttons fluid size="small"/>
                    </td>
                    <td v-currency="totalAmount(entry)" class="p-3"></td>
                </tr>
                <tr v-if="costStore.expenses.some((expense) => expense.quantity > 0)" class="border-b border-slate-200">
                    <td></td>
                    <td colspan="6" class="p-3">
                        <InputText v-model="costStore.globalExpensesConcept" fluid/>
                    </td>
                    <td v-currency="expenseTotal" class="p-3"></td>
                    <td class="p-3 w-28 max-w-28">
                        <InputNumber v-model="globalExpensesQuantity" size="small" show-buttons fluid/>
                    </td>
                    <td v-currency="totalExpenses"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-right p-3">Totales</td>
                    <td v-currency="getTotalFrom('bundle_price')" class="p-3"></td>
                    <td v-currency="getTotalFrom('extras')" class="p-3"></td>
                    <td v-currency="getTotalFrom('total_cost')" class="p-3"></td>
                    <td v-currency="getTotalFrom('price_offset')" class="p-3"></td>
                    <td v-currency="grossCost" colspan="4" class="text-right p-3"></td>
                </tr>
            </tfoot>
        </table>
    </Panel>

    <div class="flex flex-col xl:flex-row gap-3">
        <Panel header="Víaticos" class="xl:w-2/3"
        :pt="{
            contentContainer: 'overflow-auto'
        }">
            <Button icon="fa-solid fa-plus" label="Agregar" @click="addEmptyExpense"/>
            <DataTable :value="expenses">
                <Column>
                    <template #body="{index}">
                        <Button
                            icon="fa-solid fa-xmark"
                            severity="secondary"
                            @click="removeExpense(index)"/>
                    </template>
                </Column>
                <Column header="Concepto" class="min-w-96 w-96">
                    <template #body="{index}">
                        <InputText
                            v-model="expenses[index].concept"
                            placeholder="Viático"
                            fluid/>
                    </template>
                </Column>
                <Column header="Costo" class="min-w-36 w-36">
                    <template #body="{index}">
                        <InputNumber
                            v-model="expenses[index].cost"
                            locale="es-MX"
                            mode="currency"
                            currency="MXN"
                            fluid/>
                    </template>
                </Column>
                <Column header="Cantidad" class="min-w-32 w-32">
                    <template #body="{index}">
                        <InputNumber
                            v-model="expenses[index].quantity"
                            :min="0"
                            show-buttons
                            fluid
                            />
                    </template>
                </Column>
                <Column header="Importe">
                    <template #body="{index}">
                        <span v-currency="getTotalFromExpense(expenses[index])"/>
                    </template>
                </Column>
                <ColumnGroup type="footer">
                    <Row>
                        <Column :colspan="3"/>
                        <Column footer="Total"/>
                        <Column>
                            <template #footer><span v-currency="expenseTotal"/></template>
                        </Column>
                    </Row>
                </ColumnGroup>
            </DataTable>
        </Panel>

        <Panel header="Totales" class="xl:w-1/3">
            <div class="mb-4">
                <IftaLabel>
                    <InputText v-model="costStore.paymentMethod" fluid/>
                    <label>Forma de pago</label>
                </IftaLabel>
            </div>
            <div class="flex justify-center">
                <SelectButton
                    v-model="priceAdjustment.type"
                    :options="priceAdjusmentOptions"
                    option-label="label"
                    option-value="value"/>
            </div>
            <div class="py-3">
                <table class="w-full">
                    <tbody class="text-lg font-semibold">
                        <tr class="border-b border-slate-200">
                            <td class="p-2 border-r border-slate-200">Costo bruto</td>
                            <td v-currency="grossCost" class="p-2"/>
                        </tr>
                        <tr v-if="hasPriceAdjustment" class="border-b border-slate-200">
                            <td class="p-2 border-r border-slate-200">
                                <div class="flex w-64 items-center">
                                    <div class="w-2/5">
                                        {{ priceAdjustmentLabel }}
                                    </div>
                                    <InputGroup class="w-3/5">
                                        <InputGroupAddon>
                                            <Checkbox v-model="priceAdjustment.hasPercentage" binary/>
                                        </InputGroupAddon>
                                        <InputNumber
                                            v-model="priceAdjustment.percentage"
                                            :disabled="!priceAdjustment.hasPercentage"
                                            :min="0"/>
                                        <InputGroupAddon>
                                            <i class="fa-solid fa-percentage"/>
                                        </InputGroupAddon>
                                    </InputGroup>
                                </div>
                            </td>
                            <td class="p-2">
                                <span v-if="priceAdjustment.hasPercentage" v-currency="computedPriceAdjustment"/>
                                <InputNumber
                                    v-else
                                    v-model="priceAdjustment.total"
                                    locale="es-MX"
                                    mode="currency"
                                    currency="MXN"
                                    :min="0"
                                    fluid/>
                            </td>
                        </tr>
                        <tr v-if="hasPriceAdjustment" class="border-b border-slate-200">
                            <td class="p-2 border-r border-slate-200">Subtotal</td>
                            <td v-currency="subtotal" class="p-2"/>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="p-2 border-r border-slate-200">IVA</td>
                            <td v-currency="iva" class="p-2"/>
                        </tr>
                        <tr>
                            <td class="p-2 border-r border-slate-200">Costo neto</td>
                            <td v-currency="netCost" class="p-2"/>
                        </tr>
                    </tbody>
                </table>
                <div v-if="hasPriceAdjustment" class="mt-4">
                    <IftaLabel>
                        <Textarea
                            v-model="priceAdjustment.notes"
                            rows="3"
                            fluid
                            class="resize-none"
                            />
                        <label>Notas sobre el {{ priceAdjustmentLabel }}</label>
                    </IftaLabel>
                </div>
            </div>
        </Panel>
    </div>
</template>