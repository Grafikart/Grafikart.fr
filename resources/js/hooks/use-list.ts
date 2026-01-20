import { useCallback, useState } from 'react';

export function useList<T>(initialValue: T[] = []) {
    const [state, setState] = useState(initialValue);

    return [
        state,
        useCallback((newValue: T) => {
            setState((items) =>
                items.includes(newValue)
                    ? items.filter((item) => item !== newValue)
                    : [...items, newValue],
            );
        }, []),
        setState,
    ] as const;
}
