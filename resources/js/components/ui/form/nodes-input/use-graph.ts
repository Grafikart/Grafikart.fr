import { addEdge, type OnConnect, useEdgesState, useNodesState } from '@xyflow/react';
import { createContext, useCallback, useMemo } from 'react';

import type { Edge, NewNode, Node } from '@/components/flow/types.ts';
import type { PathFormData, PathNodeData } from '@/types';

const valueToFlow = (nodes: PathFormData['nodes']): {edges: Edge[], nodes: Node[]} => {
    const edges = [] as Edge[]
    for (const node of nodes) {
        for (const parent of node.parents) {
            edges.push({
                id: parent.id.toString() + "-" + node.id?.toString(),
                source: parent.id.toString(),
                target: node.id!.toString(),
                type: parent.primary ? 'primary' : 'secondary',
                data: {},
            })
        }
    }
    return {edges, nodes: nodes.map(node => ({
            id: node.id?.toString() ?? '',
            type: node.contentType,
            position: {x: node.x, y: node.y},
            data: node,
        } satisfies Node))}
}

const flowToValue = (nodes: Node[], edges: Edge[]): PathNodeData[] => {
   const paths = new Map(nodes.map(node => ([node.id, {
       ...node.data,
       ...node.position,
       parents: [] as {id: number, primary: boolean}[],
   }])))

    for (const edge of edges) {
        paths.get(edge.target)?.parents?.push({
            id: parseInt(edge.source, 10),
            primary: edge.type === 'primary',
        })
    }

    return Array.from(paths.values())
}

/**
 * Custom hook that manages the state of a graph, including nodes, edges, and interactions between them.
 */
export const useGraph = (initial: PathNodeData[]) => {
    const initialFlow = useMemo(() => valueToFlow(initial), [initial])
  const [nodes, setNodes, onNodesChange] = useNodesState(initialFlow.nodes);
  const [edges, setEdges, onEdgesChange] = useEdgesState(initialFlow.edges);

  // Initial fetch
  const onConnect: OnConnect = useCallback(
    (params) => setEdges((edges) => addEdge(params, edges)),
    [setEdges],
  );
  const addNode = useCallback((node: NewNode): Node => {
    const id = Date.now() * -1;
    const newNode = {...node, id: id.toString(), origin: [0.5, 0.5], data: {...node.data, id}}
    setNodes((nodes) => [
      ...nodes, newNode
    ]);
    return newNode
  }, [setNodes]);
  const updateNode = useCallback((node: { id: string } & Partial<Node>) => {
    setNodes((nodes) =>
      nodes.map((n) =>
        n.id === node.id
          ? {
              ...n,
              ...node,
            }
          : n,
      ),
    );
  }, [setNodes]);
  const updateEdge = useCallback((edge: { id: string } & Partial<Edge>) => {
    setEdges((edges) => {
      return edges.map((e) =>
        e.id === edge.id
          ? {
              ...e,
              ...edge,
            }
          : e,
      );
    });
  }, [setEdges]);

  return {
    nodes,
    edges,
    onNodesChange,
    onEdgesChange,
    onConnect,
    updateNode,
    updateEdge,
    addNode,
      value: flowToValue(nodes, edges)
  };
};

export const GraphContext = createContext<
  Pick<ReturnType<typeof useGraph>, "updateNode">
>({ updateNode: () => {} });
