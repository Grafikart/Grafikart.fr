import { Handle, Position } from '@xyflow/react';
import { clsx } from 'clsx';
import { memo } from 'react';

import { type Node } from './types.ts';

type Props = {
    data: Node['data'];
    selected: boolean;
    type: string;
};

export const FlowNodeGate = memo(({ data, selected }: Props) => {
    const titleStyle = clsx(
        'text-md z-3 text-primary absolute -bottom-9 -translate-x-1/2 text-center font-bold',
    );

    return (
        <div className={clsx('relative size-px', selected && 'is-selected')}>
            <img
                alt=""
                width={30}
                height={30}
                className="size-18 absolute -bottom-2 -left-6 -translate-x-1/2 translate-y-3 transition-all"
                src={`/images/flow/${data.icon ?? 'gate'}.svg`}
            />
            <div>
                <div className={clsx(titleStyle, 'isometric w-max')}>
                    {data.title}
                </div>
            </div>
            {/* @ts-expect-error We are loose, Handle accepts no type */}
            <Handle position={Position.Top} className="react-flow__handle" />
        </div>
    );
});
