export function currency(input: number | string): string {
    let val = isNaN(Number(input)) ? 0 : input;
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(Number(val));
}

export function date(input: number| string | Date): string {
    const date = new Date(input);
    return date.toLocaleDateString('es-MX', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
}

export function time(input: number | string | Date): string {
    const date = new Date(input);
    return date.toLocaleTimeString('es-MX', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });
}

export const timestamp = (input: number | string | Date) => [ date(input), time(input) ].join(' ')