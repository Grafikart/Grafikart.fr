import { Handle, Position } from '@xyflow/react';
import { clsx } from 'clsx';
import { memo } from 'react';

import { type Node } from './types.ts';

type Props = {
    data: Node['data'];
    selected: boolean;
};

export const FlowNode = memo(({ data, selected }: Props) => {
    return (
        <div className={clsx('relative size-px', selected && 'is-selected')}>
            <SupportIcon selected={selected} />
            {data.icon && (
                <img
                    alt=""
                    width={50}
                    height={50}
                    className="absolute bottom-2 left-0 size-12 -translate-x-1/2 transition-all hover:-translate-y-1"
                    src={`/uploads/icons/${data.icon}.svg`}
                />
            )}
            <div>
                <div
                    className={clsx(
                        'text-md z-3 absolute -bottom-9 w-max -translate-x-1/2 text-center font-bold',
                        selected && 'text-primary',
                    )}
                >
                    {data.title}
                </div>
            </div>
            {/* @ts-expect-error We are loose, Handle accepts no type */}
            <Handle position={Position.Top} className="react-flow__handle" />
        </div>
    );
});

function SupportIcon(props: { selected: boolean }) {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            className="-translate-1/2 absolute left-0 top-0 w-10"
            viewBox="0 0 259 174"
        >
            <path
                className={clsx('fill-edge', props.selected && 'fill-primary')}
                d="M220.6 151.45c-50.46 29.13-132.27 29.13-182.73 0C-5.5 126.41 2.06 112.29 0 75.1c50.46-29.13 208.04-27.63 258.5 1.5 0 45.65-.5 53.25-37.9 74.85Z"
            />
            <path
                className={clsx(
                    'fill-card stroke-edge',
                    props.selected && 'fill-primary-light stroke-primary',
                )}
                strokeWidth="10"
                d="M129.23 5c32.42 0 64.58 7.16 88.87 21.18 24.32 14.04 35.34 31.74 35.34 48.42 0 16.68-11.02 34.38-35.34 48.42-24.3 14.02-56.45 21.18-88.87 21.18s-64.57-7.16-88.86-21.18C16.05 108.98 5.02 91.28 5.02 74.6c0-16.68 11.03-34.38 35.35-48.42C64.66 12.16 96.8 5 129.23 5Z"
            />
        </svg>
    );
}

type CircleProps = {
    fill: string;
    transform?: string;
    stroke: string;
};

/**
 * Draw an isometric circle (used for support)
 */
export function Circle({ stroke, fill, transform }: CircleProps) {
    return (
        <g transform={`${transform ?? ''} scale(2)`}>
            <path
                d="M-0.123473 5.49772C5.39937 5.49772 9.87653 3.03529 9.87653 -0.00228071C9.87653 -3.03985 5.39937 -5.50228 -0.123473 -5.50228C-5.64632 -5.50228 -10.1235 -3.03985 -10.1235 -0.00228071C-10.1235 3.03529 -5.64632 5.49772 -0.123473 5.49772Z"
                stroke={stroke}
                fill={fill}
                strokeWidth={0.5}
            />
        </g>
    );
}
