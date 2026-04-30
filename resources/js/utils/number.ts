interface Config {
    decimals: number;
    floor?: boolean;
}

export function roundNumber(input: number, config: Config) {
    const divisor = Math.pow(10, config.decimals)

    if (isNaN(input)) {
        return 0
    }

    const mathFunction = config.floor ? 'floor' : 'round'

    return Math[mathFunction](input * divisor) / divisor
}