import { computed, unref } from "vue";
import { Analysis, AnalysisLikeControl, Config, PageProps } from "../types";
import { usePage } from "@inertiajs/vue3";
import { roundNumber } from "@/utils/number";

const pageProps = usePage<PageProps>().props;

const config: Config = {
    extendedColumns: [
        { key: 'quantity', inputType: 'number', header: 'Vol. de muestra (mL)', defaultValue: 100 },
        { key: 'capacity', inputType: 'number', header: 'Vol. de aforo (mL)', defaultValue: 100 },
        { key: 'cps', inputType: 'number', header: 'Promedio respuesta de equipo (CPS)', defaultValue: 0 },
    ],
    result: {
        header: 'Concentración (mg/L)',
        isReadOnly: true,
        decimals: 3
    },
    extendedAttributes: {
        slope: { header: 'Pendiente', value: 0 },
        yIntercept: { header: 'Ordenada', value: 0 },
        firstPoint: { header: 'Primer punto', value: 0 },
    },
    controls: {
        spiked: {
            type: 'spiked',
            label: 'Fortificado',
            enabled: false,
            needs_reference: true,
            result: 0,
            reported_result: 0,
        },
        duplicate: {
            type: 'duplicate',
            label: 'Duplicado',
            enabled: false,
            needs_reference: true,
            result: 0,
            reported_result: 0,
        },
    },
    setup: ({analyses, params, controls}) => {
        if (!params || !controls) return;
        const firstPoint = pageProps.batch.params?.firstPoint ?? pageProps.min_quantifiable;
        params.firstPoint = firstPoint;
        analyses.value.forEach((item) => calculate(item, params));
        Object.values(controls.value!).forEach((item) => calculate(item as AnalysisLikeControl, params));
    },
    veredictClass(analysis) {
        if (Number(analysis.reported_result.value) > Number(analysis.threshold)) {
            return 'bg-red-400';
        }

        if (Number(analysis.reported_result.value) === Number(analysis.threshold)) {
            return 'bg-amber-400';
        }

        return 'bg-emerald-400';
    }
}

function calculate(item: Analysis | AnalysisLikeControl, params: Record<string, any>) {
    if (!item.params) return;

    const p = item.params;
    const subtotal = computed(() => {
        return (unref(p.cps) - params.yIntercept) / params.slope;
    });
    const result = computed(() => (subtotal.value * unref(p.capacity)) / unref(p.quantity))
    item.result = computed(() => roundNumber(result.value, { decimals: 6 }));
    item.reported_result = computed(() => {
        return result.value < params.firstPoint
            ? `<${pageProps.min_quantifiable}`
            : roundNumber(result.value, { decimals: 3});
    });
}

export default config;
