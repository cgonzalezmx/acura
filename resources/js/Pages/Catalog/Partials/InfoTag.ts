import { h } from 'vue';

interface InfoTagProps {
    title: string;
    user: string;
    date: string;
}

export default (props: InfoTagProps) => (
    h('div', { class: 'text-surface-500' }, [
        h('div', { class: 'text-sm' }, props.title),
        h('div', { class: 'text-xs' }, [
            h('div', ['Usuario: ', props.user]),
            h('div', ['Fecha y hora: ', props.date])
        ])
    ])
);