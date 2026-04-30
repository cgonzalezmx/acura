import { computed, reactive, ref, shallowReactive, toRaw, unref } from 'vue';
import { Entry, Report, Threshold } from '../types';
import { defineStore } from 'pinia';
import { nanoid } from 'nanoid';
import { ulid } from 'ulid';
import { useTree } from '@/Components/Tree/useTree';
import { useRootNodes } from '../composables/useRootNodes';
import { makeNewMap } from '@/utils/map';

export const useEntryStore = defineStore('entries', () => {
    const cache = reactive<Record<string, Entry>>({});
    const all = computed(() => Object.values(cache));
    const get = (entryId: string): Entry => cache[entryId];
    const entriesTotal = computed(() => all.value.reduce((sum, {total_cost, quantity, price_offset}) => {
        return sum + (total_cost + price_offset) * quantity;
    }, 0));

    const createEmptyEntry = (entry_id: string): Entry => ({
        id: null,
        entry_id,
        title: '',
        is_urgent: false,
        form_factor: '',
        result_time_lapse: 12,
        objective: '',
        takes: 1,
        sampling_date: new Date(),
        total_containers: 0,
        sample_container_type: '',
        total_volume: '',
        refrigerated: false,
        observation: '',
        sample_type: '',
        sample_reception_date: new Date(),
        concept: '',
        bundle_price: 0,
        extras: 0,
        price_offset: 0,
        total_cost: 0,
        matrix_id: 0,
        reports: shallowReactive<Record<string, Report>>({}),
        quantityOverrides: shallowReactive<Record<number, number>>({}),
        included_parameters: {},
        sample_temperature: 0,
        quantity: 1,
        price_offset_notes: null
    });

    function add() {
        const entryId = ulid();
        cache[entryId] = createEmptyEntry(entryId);
        return entryId;
    }

    function append(entry: Entry) {
        cache[entry.entry_id] = entry;
    }

    function update(entryId: string, newData: Partial<Entry>) {
        const oldData = get(entryId);

        if (!oldData) {
            throw new Error(`No existe ninguna partida con id ${entryId}`);
        }

        Object.assign(cache[entryId], newData);
    }

    function remove(entryId: string) {
        if (!cache[entryId]) {
            console.warn(`No existe ninguna partida con id ${entryId}`);
            return;
        }

        delete cache[entryId];
    }

    function loadEntries(entries: Entry[]) {
        entries.forEach((entry) => {
            importEntry(entry);
        });
    }

    function importEntry(entry: Entry, isCopy: boolean = false) {
        const entryId = isCopy ? ulid() : entry.entry_id;
        const baseEntry: Entry = {
            ...entry,
            sample_reception_date: new Date(entry.sample_reception_date),
            sampling_date: new Date(entry.sampling_date),
            total_cost: entry.bundle_price + entry.extras,
            entry_id: entryId,
            reports: shallowReactive<Record<string, Report>>({}),
            quantityOverrides: shallowReactive({ ...entry.quantityOverrides }),
            is_loaded: true,
            included_parameters: { ...entry.included_parameters }
        };

        Object.values(entry.reports).forEach((report, index) => {
            const reportId = isCopy ? ulid() : report.report_id;
            const instanceTree = useTree(nanoid());
            const structureTree = useTree(nanoid());

            structureTree.nodes.value = useRootNodes().value;
            structureTree.loadState({
                selection: report.structure?.selection?.value,
                expandedKeys: { ...unref(report.structure.expandedKeys) },
                selectedKeys: { ...unref(report.structure.selectedKeys) }
            });

            instanceTree.nodes.value = structureTree.pathToSelection?.value?.find((node) => node.type === 'regulation')?.regulation?.instances;

            instanceTree.loadState({
                selection: report.instance?.selection?.value,
                expandedKeys: unref(report.instance.expandedKeys),
                selectedKeys: unref(report.instance.selectedKeys)
            });

            baseEntry.reports[reportId] = {
                id: null,
                report_id: reportId,
                structure: structureTree,
                instance: instanceTree,
                is_main_report: index === 0 ? true : false,
                parameters: shallowReactive(makeNewMap<number, { quantity: number, from_system: boolean}>(report.parameters)),
                thresholds: {
                    system: shallowReactive(makeNewMap<number, Threshold>(report.thresholds.system)),
                    custom: reactive(makeNewMap(report.thresholds.custom))
                },
                observation: ref(report.observation)
            }
        });

        cache[entryId] = baseEntry;
    }

    function copy(entry: Entry) {
        importEntry(entry, true);
    }

    return { all, entriesTotal, get, add, update, remove, copy, append, loadEntries };
})
