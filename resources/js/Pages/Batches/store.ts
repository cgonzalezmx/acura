import { computed, ref, shallowRef } from 'vue';
import { defineStore } from 'pinia';
import { usePage } from '@inertiajs/vue3';
import { Analysis, AnalysisLikeControl, Batch, Config, Control, PageProps, Procedure, SetupProps } from './types';

function dereferenceDefaultValue(value: unknown) {
    if (typeof value !== 'object') {
        return value;
    }

    return Array.isArray(value) ? [] : {};
}

function configAnalysisParams(analysis: Analysis, config: Config) {
    const params: Record<string, any> = {};

    config.extendedColumns?.forEach((col) => {
        const value = analysis.params?.[col.key] ?? dereferenceDefaultValue(col.defaultValue);
        params[col.key] = ref(value);
    });

    return {
        ...analysis,
        params
    };
}

function configAnalysisLikeControl(control: AnalysisLikeControl, config: Config) {
    const params: Record<string, any> = {};

    config.extendedColumns?.forEach((col) => {
        const value = control.params?.[col.key] ?? 0;
        params[col.key] = value;
    });

    return {
        ...control,
        params
    };
}

function configProcedureParams(batch: Batch, config: Config) {
    const params = batch.params ?? config.extendedAttributes!;
    const hasParams = Boolean(batch.params);
    const object: Record<string, any> = {};

    for (const [key, item] of Object.entries(params)) {
        object[key] = hasParams ? item : item.value;
    }

    return object;
}

function configProcedureControls(batch: Batch, config: Config) {
    if (!config.controls) return;

    const controls: Record<string, AnalysisLikeControl> = {};

    for (const [key, con] of Object.entries(config.controls)) {
        const value = batch.controls?.[key] ?? configAnalysisLikeControl(con as AnalysisLikeControl, config);
        controls[key] = value as AnalysisLikeControl;
    }

    return controls;
}

function fetchConfig(pageProps: PageProps) {
    const byArea: Record<string, () => Promise<{default: Config}>> = {
        a1: () => import('./configs/a1'),
        a2: () => import('./configs/a2')
    };

    const areaCode = pageProps.batch.analysis_area.code;
    const dynamicImport = byArea[areaCode.toLocaleLowerCase()];

    return dynamicImport();
}

export const useProcedureStore = defineStore('procedure', () => {
    const pageProps = usePage<PageProps>().props;
    const config = shallowRef<Config | null>(null);
    const analyses = shallowRef<Analysis[]>([]);
    const batch = pageProps.batch;
    const params = ref<Record<string, any>>();
    const controls = ref<Record<string, Control | AnalysisLikeControl>>();
    const analysisMap = computed(() => {
        const items: Record<number, Analysis> = {};

        for (const analysis of analyses.value) {
            items[analysis.id] = analysis;
        }

        return items;
    });
    const references = computed(() => {
        const values: Record<string, number> = {};
        for (const item of Object.values(controls.value ?? {})) {
            if (item.needs_reference) {
                values[item.type] = analysisMap.value[item.reference_id]?.result?.value ?? 0;
            }
        }

        return values;
    })
    const procedure = ref<Procedure>({
        name: pageProps.batch.name,
        checkin_time: pageProps.batch.checkin_time ? new Date(pageProps.batch.checkin_time) : null,
        checkout_time: pageProps.batch.checkout_time ? new Date(pageProps.batch.checkout_time) : null,
        log: pageProps.batch.log,
        solutions_log: pageProps.batch.solutions_log,
        sample_storages: pageProps.batch.sample_storages ?? [],
        analyzed_at: new Date(pageProps.batch.analyzed_at || Date.now())
    });

    fetchConfig(pageProps).then((module) => {
        const conf = module.default as Config;
        config.value = conf;

        if (conf.extendedColumns) {
            analyses.value = pageProps.analyses.map((item) => configAnalysisParams(item, conf)) as Analysis[];
        }
        else {
            analyses.value = pageProps.analyses;
        }

        if (conf.extendedAttributes) {
            params.value = configProcedureParams(batch, conf);
        }

        if (conf.controls) {
            controls.value = configProcedureControls(batch, config.value);
        }

        const setupProps: SetupProps = {
            analyses,
            params: params.value,
            controls,
        };
        conf.setup?.(setupProps);
    });

    return {
        analyses,
        analysisMap,
        procedure,
        config,
        params,
        controls,
        references,
    };
});
