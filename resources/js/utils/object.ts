const pick = <T, K extends keyof T>(obj: T, keys: K[]): Pick<T, K> => {
    const result = {} as Pick<T, K>;

    keys.forEach((key) => {
        const attr = obj[key];

        if (typeof attr !== 'undefined') {
            result[key] = attr;
        }
    });

    return result;
}

const omit = <T extends Record<string, any>, K extends keyof T>(obj: T, keys: K[]): Omit<T, K> => {
    const entries = Object.entries(obj) as [keyof T, T[keyof T]][];
    const filtered = entries.filter(([key]) => !keys.includes(key as K));

    return Object.fromEntries(filtered) as Omit<T, K>;
};

export {
    pick,
    omit
}