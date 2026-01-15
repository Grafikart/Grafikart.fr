import { Form as InertiaForm } from '@inertiajs/react';
import {
    type ComponentProps,
    createContext,
    type PropsWithChildren,
    useContext,
} from 'react';

type Props = PropsWithChildren<ComponentProps<typeof InertiaForm>>;

const FormContext = createContext({
    errors: {} as Record<string, string>,
    fetching: false,
});

export function Form({ children, ...props }: Props) {
    return (
        <InertiaForm {...props}>
            {({ errors, processing }) => {
                return (
                    <FormContext value={{ errors, fetching: processing }}>
                        {children}
                    </FormContext>
                );
            }}
        </InertiaForm>
    );
}

export function useFormError(name: string): string | null {
    const errors = useContext(FormContext).errors;
    return errors[name] ?? null;
}

export function useFormErrors() {
    return useContext(FormContext).errors;
}

export function useFormFetching(): boolean {
    return useContext(FormContext).fetching;
}
