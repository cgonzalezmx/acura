import { computed } from 'vue';
import { Analysis, AnalysisLikeControl, Config } from '../types';
import { roundNumber } from '@/utils/number';
import AcceptanceCriteria from '../Partials/AcceptanceCriteria.vue';

const config: Config = {
    extendedColumns: [
        { key: 'quantity', inputType: 'number', header: 'Volumen de muestra (mL)', defaultValue: [] },
        { key: 'initial', inputType: 'number', header: 'Oxígeno disuelto inicial', defaultValue: [] },
        { key: 'final', inputType: 'number', header: 'Oxígeno disuelto final', defaultValue: [] },
        { key: 'result', inputType: 'info', header: 'DBO5 real mg/L', defaultValue: [], toggable: true, decimals: 2 },
        { key: 'oneThird', inputType: 'info', header: 'ODI/3', defaultValue: [], toggable: true, decimals: 1 },
        { key: 'substraction', inputType: 'info', header: 'ODI-ODF', defaultValue: [], toggable: true, decimals: 1 },
        { key: 'twoThirds', inputType: 'info', header: '(2/3) ODI', defaultValue: [], toggable: true, decimals: 1 },
        { key: 'acceptance', inputType: 'custom', header: 'Criterio de aceptación', defaultValue: {}, component: AcceptanceCriteria, omitFromControls: true }
    ],
    controls: {
        blank: {
            type: 'blank',
            label: 'Blanco reactivo',
            enabled: true,
            result: 0,
            reported_result: 0,
        },
        quality: {
            type: 'quality',
            label: 'Control de calidad',
            enabled: false,
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
    rowsPerAnalysis: 3,
    setup({analyses, controls}) {
        analyses.value.forEach((item) => {
            calculate(item, controls!.value!.blank as AnalysisLikeControl)
            selectRowForResult(item);
        });
        Object.values(controls?.value as Record<string, AnalysisLikeControl>).forEach((con) => calculateControls(con, controls!.value!.blank as AnalysisLikeControl))
    }
}

function getResult(substraction: number, quantity: number, blank: AnalysisLikeControl) {
    const { enabled, result: bResult } = blank;
    const result = roundNumber((substraction * 300) / quantity, { decimals: 4 });
    return enabled ? result - bResult : result;
}

function calculate(item: Analysis, blank: AnalysisLikeControl) {
    const p = item.params!;
    for (let i = 0; i < 3; i++) {
        p.oneThird.value[i] = computed(() => {
            return roundNumber(p.initial.value[i] / 3, { decimals: 4 });
        });
        const substraction = computed(() => roundNumber(p.initial.value[i] - p.final.value[i], { decimals: 4 }));
        p.substraction.value[i] = substraction;
        p.twoThirds.value[i] = computed(() => {
            return roundNumber((p.initial.value[i] * 2) / 3, { decimals: 4 });
        });
        p.result.value[i] = computed(() => getResult(substraction.value, p.quantity.value[i], blank));
    }
}

function calculateControls(control: AnalysisLikeControl, blank: AnalysisLikeControl) {
    const p = control.params!;
    const substraction = computed(() => roundNumber(p.initial - p.final, { decimals: 4}));
    p.substraction = substraction;
    p.oneThird = computed(() => roundNumber(p.initial / 3, { decimals: 4 }));
    p.twoThirds = computed(() => roundNumber((p.initial * 2) / 3, { decimals: 4 }));
    const result = control.type !== 'blank'
        ? computed(() => getResult(substraction.value, p.quantity, blank))
        : substraction;
    p.result = result;
    control.result = result;
}

function selectRowForResult(item: Analysis) {
    const p = item.params;
    const acceptanceIndex = computed(() => {
        return p?.acceptance?.value.index;
    });
    item.result = computed(() => {
        const index = acceptanceIndex.value;

        return p?.result?.value?.[index]?.value ?? 0;
    });
    item.reported_result = computed(() => {
        const index = acceptanceIndex.value;
        const result = p?.result?.value?.[index] ?? 0;

        return roundNumber(result.value, { decimals: 2 });
    });
}

export default config;
