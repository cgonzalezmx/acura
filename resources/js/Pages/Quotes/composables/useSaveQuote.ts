import { useClientDataStore } from "../stores/useClientDataStore";
import { useEntryStore } from "../stores/useEntryStore";
import { useCostStore } from "../stores/useCostStore";
import { Entry, IncludedParameters, Threshold } from "../types";
import { useRootNodes } from "./useRootNodes";
import { useQuoteDataStore } from "../stores/useQuoteDataStore";
import { useContactDataStore } from "../stores/useContactDataStore";
import { useSamplingSiteDataStore } from "../stores/useSamplingSiteDataStore";
import { router, usePage } from "@inertiajs/vue3";

function getIncludedParamereters(entry: Entry) {
    const included: (IncludedParameters & { parameter_id: number})[] = [];
    Object.entries(entry.included_parameters).forEach(([id, data]) => {
        if (data.quantity && data.quantity > 0) {
            included.push({
                parameter_id: Number(id),
                ...{
                    quantity: data.quantity,
                    expected_quantity: data.expected_quantity,
                    from_system: data.from_system,
                    from_main_report: data.from_main_report
                }
            });
        }
    });

    return included;
}

function reports(entry: Entry) {
    const included = getIncludedParamereters(entry);
    const includedMap: Record<string, true> = {};

    included.forEach((item) => includedMap[item.parameter_id] = true);

    return Object.values(entry.reports).map((report): Record<string, any> => {
            const systemThresholds = report.thresholds.system;
            const customThresholds = report.thresholds.custom;

            const thresholds = () => {
                const thresholdArray: Threshold[] = [];

                Array.from(included)
                    .forEach(({parameter_id: paramId}) => {
                        if (!includedMap[paramId]) {
                            return;
                        }

                        const customThreshold = customThresholds.get(paramId) as Threshold | undefined;
                        const customBoundary = customThreshold?.custom_boundary;
                        const systemThreshold = systemThresholds.get(paramId)

                        if (customThreshold) {
                            if (customBoundary === 'both' || (!systemThreshold && customBoundary)) {
                                thresholdArray.push(customThreshold);
                                return;
                            }

                            if (!systemThreshold) return;

                            if (customBoundary === 'min') {
                                thresholdArray.push({
                                    ...systemThreshold,
                                    min: customThreshold.min,
                                    custom_boundary: customThreshold.custom_boundary
                                });
                            }

                            if (customBoundary === 'max') {
                                thresholdArray.push({
                                    ...systemThreshold,
                                    max: customThreshold.max
                                });
                            }
                        }
                        else {
                            thresholdArray.push(systemThresholds.get(paramId) as Threshold);
                        }
                    });

                    return thresholdArray;
                }

                return {
                    report_id: report.report_id,
                    thresholds: thresholds(),
                    structure_expanded_keys: report.structure.expandedKeys.value,
                    structure_selected_keys: report.structure.selectedKeys.value,
                    instance_expanded_keys: report.instance.expandedKeys.value,
                    instance_selected_keys: report.instance.selectedKeys.value,
                    is_main_report: report.is_main_report,
                    observation: report.observation.value
                };
        });
}

export function useSaveQuote() {
    const quote = usePage().props?.quote as any;
    const quoteDataStore = useQuoteDataStore();
    const clientDataStore = useClientDataStore();
    const entryStore = useEntryStore();
    const costStore = useCostStore();
    const contactDataStore = useContactDataStore()
    const samplingSiteStore = useSamplingSiteDataStore();
    const client: Record<string, any> = {
        client_id: clientDataStore.client_id,
        name: clientDataStore.name,
        industry_sector: clientDataStore.industry_sector,
        address: clientDataStore.address,
        neighborhood: clientDataStore.neighborhood,
        zip_code: clientDataStore.zip_code,
        city: clientDataStore.city,
        state: clientDataStore.state,
    };
    const client_contact = {
        client_contact_id: contactDataStore.client_contact_id,
        name: contactDataStore.name,
        phone: contactDataStore.phone,
        cellphone: contactDataStore.cellphone,
        email: contactDataStore.email,
    };
    const client_sampling_site = {
        id: samplingSiteStore.id,
        client_sampling_site_id: samplingSiteStore.client_sampling_site_id,
        name: samplingSiteStore.name,
        industry_sector: samplingSiteStore.industry_sector,
        address: samplingSiteStore.address,
        neighborhood: samplingSiteStore.neighborhood,
        city: samplingSiteStore.city,
        state: samplingSiteStore.state,
        zip_code: samplingSiteStore.zip_code,
        contact: {
            name: samplingSiteStore.contact_name,
            phone: samplingSiteStore.contact_phone,
            email: samplingSiteStore.contact_email,
        }
    }

    const entries = entryStore.all.map((entry) => {
        const included = getIncludedParamereters(entry);
        const entryReports = reports(entry);

        const data = {
            id: entry.id,
            entry_id: entry.entry_id,
            title: entry.title,
            concept: entry.concept,
            is_urgent: entry.is_urgent,
            form_factor: entry.form_factor,
            objective: entry.objective,
            result_time_lapse: entry.result_time_lapse,
            matrix_id: entry.matrix_id,
            reports: entryReports,
            quantity_overrides: entry.quantityOverrides,
            bundle_price: entry.bundle_price,
            price_offset: entry.price_offset,
            price_offset_notes: entry.price_offset_notes,
            extras: entry.extras,
            included_parameters: included,
            takes: entry.takes,
            quantity: entry.quantity
        };

        if (quoteDataStore.sampleDeliveredByClient) {
            return {
                ...data,
                sample_type: entry.sample_type,
                sampling_date: entry.sampling_date,
                sample_reception_date: entry.sample_reception_date,
                sample_container_type: entry.sample_container_type,
                total_containers: entry.total_containers,
                total_volume: entry.total_volume,
                sample_temperature: entry.sample_temperature,
                refrigerated: entry.refrigerated,
                observation: entry.observation
            };
        }

        return data;
    });

    const costs: Record<string, any> = {
        gross_cost: costStore.grossCost,
        subtotal: costStore.subtotal,
        iva: costStore.iva,
        net_cost: costStore.netCost,
        payment_method: costStore.paymentMethod
    };

    if (costStore.hasPriceAdjustment) {
        costs.price_adjustment = costStore.priceAdjustment.total;
        costs.price_adjustment_notes = costStore.priceAdjustment.notes;

        if (costStore.priceAdjustment.hasPercentage) {
            costs.price_adjustment_percentage = costStore.priceAdjustment.percentage;
        }
    }

    const expenses = costStore.expenses.filter((expense) => expense.quantity > 0);

    if (expenses.length > 0) {
        costs.expenses = expenses;
        costs.global_expenses_concept = costStore.globalExpensesConcept;
        costs.global_expenses_quantity = costStore.globalExpensesQuantity;
    }

    const payload: Record<string, any> = {
        quote: {
            objective: quoteDataStore.objective,
            notes: quoteDataStore.notes,
            validity: quoteDataStore.validity,
            sample_delivered_by_client: quoteDataStore.sampleDeliveredByClient,
            client_data_as_sampling_site: quoteDataStore.clientDataAsSamplingSite,
        },
        price_adjustment_notes: quoteDataStore.priceAdjustmentNotes,
        client,
        contact: client_contact,
        entries,
        tree: useRootNodes().value,
        costs
    }

    if (!payload.quote.client_data_as_sampling_site) {
        payload.site = client_sampling_site
    }

    if (quote?.id) {
        router.patch(route('quotes.update', quote.id), payload);
    }
    else {
        router.post(route('quotes.store'), payload);
    }
}
