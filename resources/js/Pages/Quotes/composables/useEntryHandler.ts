import { computed, reactive, ref, shallowReactive, shallowRef, watch, watchEffect } from 'vue';
import { Entry, IncludedParameters, Parameter, Report, Threshold } from '../types';
import { nanoid } from 'nanoid';
import { ulid } from  'ulid';
import { useTree } from '@/Components/Tree/useTree';
import { useRootNodes } from '../composables/useRootNodes';
import { TreeNode } from 'primevue/treenode';
import { useMatrixParameters } from './useMatrixParameters';

function withRegulationInPath(path: TreeNode[], callback: (regulation: TreeNode) => void) {
    const regulation = path.find((node) => node.type === 'regulation')?.regulation;

    if (regulation) {
        callback(regulation);
    }
}

export function useEntryHandler(entry: Entry) {
    const allReports = computed(() => {
        const reports = Object.values(entry.reports);
        return reports.map((report, index) => ({
            ...report,
            label: String.fromCharCode(index + 65)
        }));
    });
    const loaded = ref(false);
    const parameterMap = shallowRef<Map<number, Parameter>>();
    const mainPathListeners = new Set<(mainPath: TreeNode[]) => void>();
    const mainReport = shallowRef<Report>();
    const matrixId = ref(0);
    const { data: matrixParameters } = useMatrixParameters(matrixId);
    const includedParameters = computed(() => {
        const included: IncludedParameters = {};
        const reports = Object.values(entry.reports);
        reports.forEach((report) => {
            Array.from(report.parameters.entries()).forEach(([paramId, paramInfo]) => {
                const id = Number(paramId);
                if (included[id]) return;
                included[id] = {
                    ...paramInfo,
                    expected_quantity: paramInfo.quantity,
                    from_main_report: paramInfo?.from_main_report ?? report.is_main_report,
                    price: parameterMap.value?.get(id)?.price ?? 0
                };
            });
        });

        Object.entries(entry.quantityOverrides).forEach(([paramId, quantity]) => {
            const id = Number(paramId);
            if (included[id]) {
                included[id].quantity = quantity;
                return;
            }

            included[id] = {
                quantity,
                from_system: false,
                expected_quantity: quantity,
                from_main_report: false,
                price: parameterMap.value?.get(id)?.price ?? 0
            }
        });

        return included;
    });

    watch(matrixParameters, (parameters) => {
        if (parameters) {
            parameterMap.value = new Map(parameters.map((p) => [p.parameter_id, p]));
        }
    }, { immediate: true });

    watchEffect(() => {
        if (mainReport.value) {
            const structure = mainReport.value.structure.pathToSelection.value.map((node) => node.label);
            const instance = mainReport.value.instance.pathToSelection.value?.map((node) => node.label);
            let concept = [ ...structure ];

            if (typeof instance !== 'undefined' && !instance.includes('_default')) {
                concept = [ ...concept.concat(instance) ];
            }

            entry.concept = concept.join(', ');
        }
    });

    watch(
        [
            () => includedParameters.value,
            () => parameterMap.value
        ],
        ([included, paramMap]) => {
            let inEntry: IncludedParameters;

            if (entry.is_loaded && !loaded.value) {
                inEntry = entry.included_parameters;
                loaded.value = true;
            }
            else  {
                inEntry = {};
            }

            Object.entries(included).forEach(([paramId, param]) => {
                const currentlyIn = inEntry[Number(paramId)];
                if(currentlyIn) {
                    currentlyIn.quantity = param.quantity;
                }
                else {
                    inEntry[Number(paramId)] = param;
                }
            });

            entry.included_parameters = inEntry;

            if (paramMap) {
                entry.extras = Object.entries(inEntry).reduce((sum, [paramId, param]) => {
                    const paramPrice = paramMap.get(Number(paramId))?.price;

                    if (!paramPrice) return sum;

                    if (param.from_main_report && param.from_system) {
                        return sum + paramPrice * (param.quantity - param.expected_quantity);
                    };


                    return sum + paramPrice * param.quantity;
                }, 0);
            }
        }
    );

    watch(() => entry.extras + entry.bundle_price, (totalCost) => entry.total_cost = totalCost);

    function setupWatchers(report: Report) {
        watch(report.structure.pathToSelection, (path) => {
            withRegulationInPath(path, (regulation) => {
                report.instance.nodes.value = regulation?.instances;

                if (report.report_id === mainReport.value?.report_id) {
                    matrixId.value = regulation.matrix.id;
                    entry.matrix_id = regulation.matrix.id;

                    for (const listener of mainPathListeners) {
                        listener(path);
                    }
                }

                if (!entry.is_loaded) {
                    report.observation.value = regulation?.observation;
                }
            });
        }, { immediate: true });

        watch(
            [() => report.structure.selection.value, () => parameterMap.value],
            ([newSelection, parameterMap], [oldSelection]) => {
                if (newSelection) {
                    report.parameters.clear();
                    //entry.included_parameters = {};
                }

                if (newSelection?.type !== 'bundle') {
                    if (report.report_id === mainReport.value?.report_id && !entry.is_loaded) {
                        entry.bundle_price = 0;
                    }

                    return;
                }

                const bundle = newSelection.bundle;
                const mainSelectionHasChanged = newSelection.key !== oldSelection?.key

                if (mainSelectionHasChanged) {
                    if (newSelection && report.report_id === mainReport.value?.report_id) {
                        entry.bundle_price = bundle.price;
                        entry.takes = bundle.takes;
                    }
                }

                if (parameterMap) {
                    report.parameters.clear();
                    (bundle.parameters as { id: number }[]).forEach(({id}) => {
                        const parameter = parameterMap.get(id);

                        if (!parameter) return;

                        report.parameters.set(parameter.parameter_id, {
                            quantity: parameter.multiple ? bundle.takes : 1,
                            from_system: true,
                        });
                    });
                }
            }
        );

        watch(report.instance.selection, (selection) => {
            report.thresholds.system.clear();

            withRegulationInPath(report.structure.pathToSelection.value, (regulation) => {
                report.observation.value = regulation.observation;
            });

            if (selection?.type === 'definition') {
                (selection.thresholds as [number, Threshold][]).forEach(([paramId, thres]) => {
                    report.thresholds.system.set(paramId, {
                        parameter_id: paramId,
                        min: thres.min,
                        max: thres.max,
                        custom_boundary: 'none'
                    });
                });
            }

            const pathToSelection = report.instance.pathToSelection.value.map((node) => node.label);

            if (!pathToSelection.includes('_default') && pathToSelection.length > 0) {
                report.observation.value = report.observation.value + ', ' + pathToSelection.join(', ').toLocaleUpperCase();
            }
        });
    }

    function createReport(): Report {
        const reportId = ulid();
        const report: Report = {
            id: null,
            report_id: reportId,
            structure: useTree(nanoid()),
            instance: useTree(nanoid()),
            is_main_report: false,
            parameters: shallowReactive(new Map()),
            thresholds: {
                system: shallowReactive(new Map()),
                custom: reactive(new Map())
            },
            observation: ref('')
        };

        setupWatchers(report);

        entry.reports[reportId] = report;

        return report;
    }

    function syncWithMainPath(report: Report) {
        mainPathListeners.add((mainPath) => {
            report.structure.nodes.value =  [mainPath[0]];
        });

        const path = mainReport.value?.structure?.pathToSelection.value;
        report.structure.nodes.value = path?.[0]
            ? [ path[0] ]
            : [];
    }

    function addReport(): Report {
        const report = createReport();
        const reportsInEntry = Object.keys(entry.reports).length;

        if (reportsInEntry === 1) {
            report.structure.nodes.value = useRootNodes().value;
            report.is_main_report = true;
            mainReport.value = report;
        }

        if (reportsInEntry >= 2) {
            syncWithMainPath(report);
        }

        return report;
    }

    function setupReports() {
        Object.values(entry.reports).forEach((report, index) => {

            if (index === 0) {
                mainReport.value = report;
            }
            else {
                syncWithMainPath(report)
            }

            setupWatchers(report);
        });
    }

    function removeReport(reportId: string) {
        const report = entry.reports[reportId];
        report.structure.deleteInstance();
        report.instance.deleteInstance();
        delete entry.reports[reportId];
    }

    return {
        allReports,
        addReport,
        removeReport,
        setupReports
    };
}
